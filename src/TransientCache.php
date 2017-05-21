<?php

namespace WPSimpleCache;

use wpdb;
use Psr\SimpleCache\CacheInterface;

/**
 * @todo ->*Multiple() implementations can probably be done more efficiently.
 */
class TransientCache extends AbstractCache
{
    const MAX_KEY_LENGTH = 172;

    protected $db;
    protected $defaultTtl;
    protected $prefix;

    public function __construct(wpdb $db, $prefix = '', $defaultTtl = null)
    {
        // 40 for length of sha1, additional 1 for ":" separator.
        if (self::MAX_KEY_LENGTH - 40 - 1 < strlen($prefix)) {
            throw new InvalidArgumentException(sprintf(
                'Provided prefix [%s, length of %s] exceeds maximum length of %s',
                $prefix,
                strlen($prefix),
                self::MAX_KEY_LENGTH
            ));
        }

        $this->db = $db;
        $this->prefix = strval($prefix);
        $this->defaultTtl = max(0, intval($defaultTtl));
    }

    public function get($key, $default = null)
    {
        $value = get_transient($this->itemKey($key));

        // Non-existent or expired transient returns false.
        if (false === $value) {
            return $default;
        }

        // WordPress already unserializes where necessary, but we are storing double
        // serialized values in order to make sure type in === type out.
        return maybe_unserialize($value);
    }

    public function set($key, $value, $ttl = null)
    {
        if (is_null($value) || 0 === $ttl || 0 > $ttl = $this->itemTtl($ttl)) {
            return $this->delete($key);
        }

        // Maybe a little weird... WordPress won't return the correct data type, so
        // we end up double serializing everything in order to correct this behavior.
        return set_transient($this->itemKey($key), serialize($value), $ttl);
    }

    public function delete($key)
    {
        if (! $this->has($key)) {
            return true;
        }

        return delete_transient($this->itemKey($key));
    }

    public function clear()
    {
        if (wp_using_ext_object_cache()) {
            return false;
        }

        $prefix = $this->prefix ? "{$this->prefix}:" : '';

        $sql = "DELETE FROM {$this->db->options}
            WHERE option_name LIKE %s
            OR option_name LIKE %s";

        $transient = $this->db->esc_like("_transient_{$prefix}") . '%';
        $timeout = $this->db->esc_like("_transient_timeout_{$prefix}") . '%';
        $count = $this->db->query($this->db->prepare($sql, $transient, $timeout));

        // Not sure this is a great idea but if we don't then values will still be
        // available during the same request.
        wp_cache_flush();

        return false === $count ? false : true;
    }

    protected function itemKey($key)
    {
        $this->validateItemKey($key);

        $prefix = $this->prefix ? "{$this->prefix}:" : '';
        $newKey = $prefix . $key;

        if (strlen($newKey) <= self::MAX_KEY_LENGTH) {
            return $newKey;
        }

        return $prefix . hash('sha1', $key);
    }
}
