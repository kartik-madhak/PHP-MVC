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
        return $this->conn->execPreparedQuery($this->query, $this->args);
    }

    private function deduceTypes(array $arr): array
    {
        $cols = [];
        $types = [];
        foreach ($arr as $k => $v) {
            array_push($cols, $k);
            switch ($v) {
                case 'int':
                    if ($k == 'id') {
                        array_push($types, 'INTEGER KEY AUTO_INCREMENT');
                    } else {
                        array_push($types, 'INTEGER');
                    }
                    break;
                case 'string':
                    if ($k == 'created_at' || $k == 'updated_at') {
//                        var_dump($k, $v);
                        array_push($types, 'DATETIME');
                    } else {
                        array_push($types, 'VARCHAR(200)');
                    }
                    break;
            }
        }
        return compact('cols', 'types');
    }

    public function createTable(array $arr)
    {
        if($this->conn->exec('SELECT * FROM ' . $this->dbname) == true)
            return false;

        $res = $this->deduceTypes($arr);
        $cols = $res['cols'];
        $types = $res['types'];

//        print_r($arr);
        $this->query = 'CREATE TABLE ' . $this->dbname . '(';
        for ($i = 0; $i < count($cols); ++$i) {
            $this->query .= $cols[$i] . ' ' . $types[$i] . ',';
        }
        $this->query = rtrim($this->query, ',') . ')';
        $this->get();
        return true;
    }

    public function update(array $array)
    {
//        var_dump($array);
        $id = $array['id'];
        unset($array['id']);
        $this->query = 'UPDATE ' . $this->dbname . ' SET ';
        foreach ($array as $k => $v) {
            $this->query .= $k . '= ?,';
            array_push($this->args, $v);
        }
        $this->query = rtrim($this->query, ',') . ' WHERE id = ' . $id;
        $this->get();
    }

}