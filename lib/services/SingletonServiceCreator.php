<?php

namespace Lib\services;


class SingletonServiceCreator
{
    /**
     * @var array object
     */
    public static array $instances = [];

    public static function add(string $className)
    {
        if (!isset(SingletonServiceCreator::$instances[$className])) {
            SingletonServiceCreator::$instances[$className] = new $className();
        }
    }

    public static function get(string $className)
    {
        if (array_key_exists($className, SingletonServiceCreator::$instances)) {
            return SingletonServiceCreator::$instances[$className];
        }
        return null;
    }
}