<?php /** @noinspection PhpExpressionResultUnusedInspection */


namespace Lib\database;


class FluentDB
{
    private MySQLAccess $conn;
    private string $query;
    private array $args;
    private string $dbname;
    private string $class;

    private bool $whereAdded;

    public function __construct(string $class)
    {
        $this->conn = new MySQLAccess();
        $this->class = $class;
        $temp = explode('\\', $class);
        $this->dbname = $temp[count($temp) - 1] . 's';
        $this->whereAdded = false;
        $this->query = '';
        $this->args = [];
    }

    public function select(array $cols = ['*']): self
    {
        $this->query = 'SELECT ' . implode(', ', $cols) . ' FROM ' . $this->dbname . ' ';
        return $this;
    }

    public function where(string $col, string $value): self
    {
        if (!$this->whereAdded) {
            $this->query .= ' WHERE ';
            $this->whereAdded = true;
        } else {
            $this->query .= ' AND ';
        }
        $this->query .= $col . ' = ? ';
        array_push($this->args, $value);
        return $this;
    }

    public function whereIn(string $col, array $values): self
    {
        if (!$this->whereAdded) {
            $this->query .= ' WHERE ';
            $this->whereAdded = true;
        } else {
            $this->query .= ' AND ';
        }
        $this->query .= $col . ' IN (' . rtrim(str_repeat('?,', count($values)), ',') . ')';
        array_push($this->args, ...$values);
        return $this;
    }

    public function orWhere(string $col, string $value): self
    {
        if (!$this->whereAdded) {
            $this->query . ' WHERE ';
            $this->whereAdded = true;
        } else {
            $this->query . ' OR ';
        }
        $this->query . $col . ' = ? ';
        array_push($this->args, $value);
        return $this;
    }

    public function insert(array $cols)
    {
        $names = array_keys($cols);
        $this->query = 'INSERT INTO ' . $this->dbname . ' (' . implode(', ', $names) . ') '
            . 'VALUES(' . rtrim(str_repeat('?,', count($cols)), ',') . ')';
        $this->args = array_values($cols);
        $this->get();
    }

    public function get()
    {
//        var_dump($this->query, $this->args);
//        dump($this);
        return $this->conn->execPreparedQuery($this->query, $this->args);
    }

    public function getFirstOrFalse()
    {
        $res = $this->get();
        return $res == false ? false : $res[0];
    }

    private function deduceTypes(array $arr): array
    {
        $cols = [];
        $cols['id'] = 'INTEGER KEY AUTO_INCREMENT';
        foreach ($arr as $k => $v) {
            switch ($v) {
                case 'int':
                    if ($k != 'id') {
                        $cols[$k] = 'INTEGER';
                    }
                    break;
                case 'string':
                    if ($k != 'created_at' && $k != 'updated_at') {
                        $cols[$k] = 'VARCHAR(200)';
                    }
                    break;
                case 'float':
                    $cols[$k] = 'FLOAT';
                    break;
            }
        }
        $cols['created_at'] = 'DATETIME';
        $cols['updated_at'] = 'DATETIME';
        return $cols;
    }

    public function dropTable()
    {
        return $this->conn->exec('DROP TABLE ' . $this->dbname);
    }

    public function createTable(array $arr)
    {
        if($this->conn->exec('SELECT * FROM ' . $this->dbname) == true)
            return false;

        $cols = $this->deduceTypes($arr);

//        print_r($arr);
        $this->query = 'CREATE TABLE ' . $this->dbname . '(';
        foreach ($cols as $k => $v) {
            $this->query .= $k . ' ' . $v . ',';
        }
        $this->query = rtrim($this->query, ',') . ')';
        $this->get();
        return true;
    }

    public function update(array $array)
    {
////        var_dump($array);
//        $id = $array['id'];
        unset($array['id']);
        $this->query = 'UPDATE ' . $this->dbname . ' SET ';
        foreach ($array as $k => $v) {
            $this->query .= $k . '= ?,';
            array_push($this->args, $v);
        }
        $this->query = rtrim($this->query, ',');
        return $this;
    }

    public function delete(): self
    {
        $this->query = 'DELETE FROM ' . $this->dbname . ' ';
        return $this;
    }

}