<?php

namespace WPSimpleCache;

use Psr\SimpleCache\CacheInterface;

abstract class AbstractCache implements CacheInterface
{
    abstract public function get($key, $default = null);

    abstract public function set($key, $value, $ttl = null);

    abstract public function delete($key);

    abstract public function clear();

    public function getMultiple($keys, $default = null)
    {
        if (! is_array($keys) && ! $keys instanceof \Traversable) {
            throw new InvalidArgumentException(sprintf(
                'Cache keys must be array or traversable, %s given',
                gettype($keys)
            ));
        }

        $results = [];

        foreach ($keys as $key) {
            $results[$key] = $this->get($key, $default);
        }

        return $results;
    }

    public function setMultiple($values, $ttl = null)
    {
        if (! is_array($values) && ! $values instanceof \Traversable) {
            throw new InvalidArgumentException(sprintf(
                'Cache values must be array or traversable, %s given',
                gettype($values)
            ));
        }

        $success = true;

        foreach ($values as $key => $value) {
            if (is_int($key)) {
                $key = strval($key);
            }

            $success = $success && $this->set($key, $value, $ttl);
        }

        return $success;
    }

    public function deleteMultiple($keys)
    {
        if (! is_array($keys) && ! $keys instanceof \Traversable) {
            throw new InvalidArgumentException(sprintf(
                'Cache keys must be array or traversable, %s given',
                gettype($keys)
            ));
        }

        $success = true;

        foreach ($keys as $key) {
            $success = $success && $this->delete($key);
        }

        return $success;
    }

    public function has($key)
    {
        return ! is_null($this->get($key));
    }

    protected function itemTtl($ttl)
    {
        if (is_null($ttl)) {
            return $this->defaultTtl;
        }

        // Thanks Symfony.
        if ($ttl instanceof \DateInterval) {
            $ttl = intval(
                \DateTime::createFromFormat('U', 0)->add($ttl)->format('U')
            );
        }

        if (is_int($ttl)) {
            return 0 < $ttl ? $ttl : -1;
        }

        throw new InvalidArgumentException(sprintf(
            'TTL must be integer, DateInterval or null, %s given',
            gettype($ttl)
        ));
    }

    protected function validateItemKey($key)
    {
        if (! is_string($key)) {
            throw new InvalidArgumentException(sprintf(
                'Cache key must be string, %s given',
                gettype($key)
            ));
        }

        if ('' === $key) {
            throw new InvalidArgumentException(
                'Cache key length must be greater than zero'
            );
        }

        if (false !== strpbrk($key, '{}()/\\@:')) {
            throw new InvalidArgumentException(sprintf(
                'Cache key [%s] contains one or more reserved characters {}()/\\@:',
                $key
            ));
        }
    }
}
