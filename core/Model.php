<?php

namespace core;

use DateTime;
class Model
{
    protected $fields_array;
    protected static $primaryKey = 'id';
    protected static $tableName = '';
    public function __construct()
    {
        $this->fields_array = [];
    }
    public function __set($name, $value)
    {
        $this->fields_array[$name] = $value;
    }
    public function __get($name)
    {
        return $this->fields_array[$name];
    }
    public static function deleteById($id)
    {
        Core::get()->db->delete(static::$tableName, [static::$primaryKey => $id]);
    }
    public static function deleteByCondition($conditionAssocArray)
    {
        Core::get()->db->delete(static::$tableName, $conditionAssocArray);
    }
    public function save()
    {
        $isInsert = true;
        $pk = isset($this->fields_array[static::$primaryKey]) ? $this->fields_array[static::$primaryKey] : null;
        if (is_int($pk) && $pk > 0) {
            $isInsert = false;
        }
        if ($isInsert) {
            Core::get()->db->insert(static::$tableName, $this->fields_array);
        } else {
            Core::get()->db->update(static::$tableName, $this->fields_array, [static::$primaryKey => $this->{static::$primaryKey}]);
        }
    }
    public static function findById($id): Model|null
    {
        $result = Core::get()->db->select(static::$tableName, "*", [static::$primaryKey => $id]);
        if (empty($result)) {
            return null;
        }
        $model = new static();
        foreach ($result[0] as $key => $value) {
            $model->$key = $value;
        }
        return $model;
    }
    public static function countAll(): int
    {
        $db = Core::get()->db;
        $stmt = $db->pdo->query("SELECT COUNT(*) FROM " . static::$tableName);
        return (int) $stmt->fetchColumn();
    }
    public static function findByCondition($conditionAssocArray, $order = null): array|null
    {
        $result = Core::get()->db->select(static::$tableName, "*", $conditionAssocArray, $order);
        if (empty($result)) {
            return null;
        }
        $models = [];
        foreach ($result as $row) {
            $model = new static();
            foreach ($row as $key => $value) {
                $model->$key = $value;
            }
            $models[] = $model;
        }
        return $models;
    }

    public static function findAll($order = null): array|null
    {
        return self::findByCondition([], $order);
    }
    public function toArray()
    {
        $array = [];
        foreach ($this->fields_array as $key => $value) {
            $array[$key] = $value;
        }
        return $array;
    }

}
