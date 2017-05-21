<?php

namespace WPSimpleCacheTests;

use WPSimpleCache\TransientCache;

class TransientCacheTest extends SimpleCacheTestCase
{
    public function createSimpleCache($prefix = '', $defaultTtl = 0)
    {
        return new TransientCache($GLOBALS['wpdb'], $prefix, $defaultTtl);
    }

    public function testDeleteWithPrefix()
    {
        $cache = $this->createSimpleCache('pfx');

        set_transient('pfx:key1', 'value');
        $success = $cache->delete('key1');

        $this->assertTrue($success);
        $this->assertFalse(get_transient('pfx:key1'));
    }

    public function testGetWithPrefix()
    {
        $cache = $this->createSimpleCache('pfx');

        set_transient('pfx:key1', 'value');

        $this->assertEquals('value', $cache->get('key1'));
    }

    public function testSetWithPrefix()
    {
        $cache = $this->createSimpleCache('pfx');

        $cache->set('key1', 'value');

        $this->assertEquals('value', unserialize(get_transient('pfx:key1')));
    }

    public function testHasWithPrefix()
    {
        $cache = $this->createSimpleCache('pfx');

        set_transient('pfx:key1', 'value');

        $this->assertTrue($cache->has('key1'));
    }

    public function testClearWithPrefix()
    {
        $cache = $this->createSimpleCache('pfx');

        set_transient('pfx:key1', 'value');
        set_transient('pfx:key2', 'value');
        set_transient('key1', 'value');
        set_transient('key2', 'value');

        $success = $cache->clear();

        $this->assertTrue($success);

        $this->assertNull($cache->get('key1'));
        $this->assertNull($cache->get('key2'));

        $this->assertEquals('value', get_transient('key1'));
        $this->assertEquals('value', get_transient('key2'));
    }

    public function testClearAlsoClearsTimeoutEntries()
    {
        $this->cache->set('key1', 'value', 360);

        // Sanity.
        $this->assertNotFalse(get_option('_transient_key1'));
        $this->assertNotFalse(get_option('_transient_timeout_key1'));

        $this->cache->clear();

        $this->assertFalse(get_option('_transient_key1'));
        $this->assertFalse(get_option('_transient_timeout_key1'));
    }

    public function testClearWithPrefixAlsoClearsTimeoutEntries()
    {
        $cache = $this->createSimpleCache('pfx');

        $cache->set('key1', 'value', 360);
        set_transient('key1', 'value', 360);

        // Sanity.
        $this->assertNotFalse(get_option('_transient_pfx:key1'));
        $this->assertNotFalse(get_option('_transient_timeout_pfx:key1'));
        $this->assertNotFalse(get_option('_transient_key1'));
        $this->assertNotFalse(get_option('_transient_timeout_key1'));

        $cache->clear();

        $this->assertFalse(get_option('_transient_pfx:key1'));
        $this->assertFalse(get_option('_transient_timeout_pfx:key1'));
        $this->assertNotFalse(get_option('_transient_key1'));
        $this->assertNotFalse(get_option('_transient_timeout_key1'));
    }
}
