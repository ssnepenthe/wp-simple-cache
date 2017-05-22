<?php

namespace WPSimpleCache;

use WP_Object_Cache;

class ObjectCache extends AbstractCache
{
    protected $aggressive;
    protected $cache;
    protected $defaultTtl;
    protected $prefix;

    public function __construct(
        WP_Object_Cache $cache,
        $prefix = '',
        $defaultTtl = null,
        $aggressive = false
    ) {
        $this->cache = $cache;
        $this->prefix = strval($prefix);
        $this->defaultTtl = max(0, intval($defaultTtl));
        $this->aggressive = boolval($aggressive);
    }

    public function clear()
    {
        if (! $this->prefix) {
            return $this->cache->flush();
        }

        if (method_exists($this->cache, 'delete_group')) {
            return $this->cache->delete_group($this->prefix);
        }

        return false;
    }

    public function delete($key)
    {
        // Key validation happens via has -> get.
        if (! $this->has($key)) {
            return true;
        }

        return $this->cache->delete($key, $this->prefix);
    }

    public function get($key, $default = null)
    {
        $this->validateItemKey($key);

        $value = $this->cache->get($key, $this->prefix, $this->aggressive, $found);

        // Maybe unserialize because we end up double serializing integers.
        return false === $found ? $default : maybe_unserialize($value);
    }

    public function getAggressive()
    {
        return $this->aggresive;
    }

    public function getCache()
    {
        return $this->cache;
    }

    public function getDefaultTtl()
    {
        return $this->defaultTtl;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function set($key, $value, $ttl = null)
    {
        if (is_null($value) || 0 === $ttl || 0 > $ttl = $this->itemTtl($ttl)) {
            return $this->delete($key);
        }

        $this->validateItemKey($key);

        // Yikes... I don't feel good about this and it definitely needs testing.
        // WP-Redis serializes everything except integers need some love too.
        if (is_int($value)) {
            $value = serialize($value);
        }

        return $this->cache->set($key, $value, $this->prefix, $ttl);
    }
}
