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

        return false === $found ? $default : $value;
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

        return $this->cache->set($key, $value, $this->prefix, $this->itemTtl($ttl));
    }
}
