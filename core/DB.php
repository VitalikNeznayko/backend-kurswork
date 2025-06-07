<?php

namespace core;

class DB
{
    public $pdo;

    public function __construct($host, $name, $login, $password)
    {
        $this->pdo = new \PDO(
            "mysql:host=$host;dbname=$name;charset=utf8",
            $login,
            $password,
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ]
        );
    }

    protected function where($where)
    {
        if (is_array($where) && !empty($where)) {
            $where_string = "WHERE ";
            $where_fields = array_keys($where);
            $parts = [];
            foreach ($where_fields as $field) {
                $parts[] = "$field = :$field";
            }
            $where_string .= implode(' AND ', $parts);
        } else if (is_string($where)) {
            $where_string = $where;
        } else {
            $where_string = '';
        }
        return $where_string;
    }
    protected function order($order)
    {
        $order_string = '';
        if (is_array($order) && !empty($order)) {
            $order_parts = [];
            foreach ($order as $field => $direction) {
                $direction = strtoupper($direction);
                if (!in_array($direction, ['ASC', 'DESC'])) {
                    $direction = 'ASC';
                }
                $order_parts[] = "$field $direction";
            }
            $order_string = 'ORDER BY ' . implode(', ', $order_parts);
        }
        return $order_string;
    }
    public function select($table, $fields = "*", $where = null, $order = null): array
    {
        if (is_array($fields))
            $field_string = implode(', ', $fields);
        else
            if (is_string($fields))
            $field_string = $fields;
        else
            $field_string = '*';

        $where_string = $this->where($where);
        $order_string = $this->order($order);

        $sql = "SELECT $field_string FROM $table $where_string $order_string";
        $sth = $this->pdo->prepare($sql);
        if (is_array($where)) {
            foreach ($where as $key => $value) {
                $sth->bindValue(":$key", $value);
            }
        }
        $sth->execute();
        return $sth->fetchAll();
    }

    public function insert($table, $row_to_insert)
    {
        $fields_list = implode(", ", array_keys($row_to_insert));
        foreach ($row_to_insert as $key => $value)
            $params_array[] = ":$key";

        $params_list = implode(", ", $params_array);
        $sql = "INSERT INTO $table ($fields_list) VALUES ($params_list)";


        $sth = $this->pdo->prepare($sql);
        foreach ($row_to_insert as $key => $value) {
            $sth->bindValue(":$key", $value);
        }

        return $sth->execute();
    }

    public function update($table, $row_to_update, $where)
    {
        $where_string = $this->where($where);

        $set_parts = [];
        foreach ($row_to_update as $key => $value) {
            $set_parts[] = "$key = :$key";
        }
        $set_string = implode(', ', $set_parts);

        $sql = "UPDATE $table SET $set_string $where_string";
        $sth = $this->pdo->prepare($sql);
        foreach ($row_to_update as $key => $value) {
            $sth->bindValue(":$key", $value);
        }
        foreach ($where as $key => $value) {
            $sth->bindValue(":$key", $value);
        }
        $sth->execute();
        return $sth->rowCount();
    }

    public function delete($table, $where)
    {
        $where_string = $this->where($where);

        $sql = "DELETE FROM $table $where_string";
        $sth = $this->pdo->prepare($sql);
        foreach ($where as $key => $value) {
            $sth->bindValue(":$key", $value);
        }
        $sth->execute();
        return $sth->rowCount();
    }
}
