<?php
namespace Lib\services;


class SingletonServiceCreator
{
    /**
     * @var array object
     */
    private static array $instances = [];

    public static function add(string $className, object $instance)
    {
        SingletonServiceCreator::$instances[$className] = $instance;
    }

    public static function get(string $className)
    {
        if (array_key_exists($className, SingletonServiceCreator::$instances))
        {
            return SingletonServiceCreator::$instances[$className];
        }
        return null;
    }
}