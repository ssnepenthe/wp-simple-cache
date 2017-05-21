<?php

$_tests_dir = getenv('WP_TESTS_DIR') ?: '/tmp/wordpress-tests-lib';

require_once $_tests_dir . '/includes/functions.php';
require_once __DIR__ . '/functions.php';

tests_add_filter('muplugins_loaded', '_sc_tests_require_autoloader');

require_once $_tests_dir . '/includes/bootstrap.php';

require_once __DIR__ . '/SimpleCacheTestCase.php';
