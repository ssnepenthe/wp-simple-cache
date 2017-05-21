<?php

namespace WPSimpleCache;

use Psr\SimpleCache\InvalidArgumentException as SimpleCacheInvalidArgumentException;

class InvalidArgumentException extends \InvalidArgumentException implements SimpleCacheInvalidArgumentException
{

}
