<?php

namespace WPSimpleCacheTests;

use WPSimpleCache\ObjectCache;

class ObjectCacheTest extends SimpleCacheTestCase
{
    public function setUp()
    {
        parent::setUp();

        if ('none' === $this->backend) {
            $this->skippedTests = [
                'testSetTtl' => 'The core object cache does not handle cache expiration',
                'testSetWithDefaultTtl' => 'The core object cache does not handle cache expiration',
                'testSetMultipleTtl' => 'The core object cache does not handle cache expiration',
            ];
        }
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->skippedTests = [];
    }

    public function createSimpleCache(
        $prefix = '',
        $defaultTtl = 0,
        $aggressive = true
    ) {
        return new ObjectCache(
            $GLOBALS['wp_object_cache'],
            $prefix,
            $defaultTtl,
            $aggressive
        );
    }
}
