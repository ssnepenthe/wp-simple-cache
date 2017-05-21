<?php

function _sc_require_if_exists($file)
{
    if (file_exists($file)) {
        require_once $file;
    }
}

function _sc_tests_require_autoloader()
{
    _sc_require_if_exists(__DIR__ . '/../vendor/autoload.php');
}
