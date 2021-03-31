<?php

namespace Lib\database;



use ReflectionException;
use ReflectionProperty;

abstract class Model
{
    public int $id;
    public string $created_at;
    public string $updated_at;

    public function __construct()
    {
    }

    public static function query(): FluentDB
    {
        $temp = explode('\\', get_called_class());
        return new FluentDB(get_called_class());
    }

    public static function createTable(): bool
    {
        $fluentDB = new FluentDB(get_called_class());
        $arr = get_class_vars(get_called_class());
        foreach ($arr as $k => $v) {
            try {
                $arr[$k] = (string)(new ReflectionProperty(get_called_class(), $k))->getType();
            } catch (ReflectionException $e) {
                echo 'AN ERROR OCCURRED';
                var_dump($e);
            }
        }
        return $fluentDB->createTable($arr);
    }

    public static function drop()
    {
        $fluentDB = new FluentDB(get_called_class());
        return $fluentDB->dropTable();
    }

    public function create(): void
    {
        $fluentDB = new FluentDB(get_called_class());
        $array = get_object_vars($this);
        $array['created_at'] = $array['updated_at'] = date('Y-m-d H:i:s', time());
        $fluentDB->insert($array);
    }

    public function save(): void
    {
        $fluentDB = new FluentDB(get_called_class());
        $array = get_object_vars($this);
        $array['updated_at'] = date('Y-m-d H:i:s', time());
        $fluentDB->update($array);
    }
}
