<?php

namespace Lib\database;



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

    public static function createTable()
    {
        $fluentDB = new FluentDB(get_called_class());
        $arr = get_class_vars(get_called_class());
        foreach ($arr as $k => $v) {
            $arr[$k] = (string)(new \ReflectionProperty(get_called_class(), $k))->getType();
        }
        $fluentDB->createTable($arr);
    }

    public function create()
    {
        $fluentDB = new FluentDB(get_called_class());
        $array = get_object_vars($this);
        $array['created_at'] = $array['updated_at'] = date('Y-m-d H:i:s', time());
        $fluentDB->insert($array);
    }

    public function save()
    {
        $fluentDB = new FluentDB(get_called_class());
        $array = get_object_vars($this);
        $array['created_at'] = $array['updated_at'] = date('Y-m-d H:i:s', time());
        $fluentDB->update($array);
    }
}
