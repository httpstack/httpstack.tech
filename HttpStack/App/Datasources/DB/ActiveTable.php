<?php

namespace HttpStack\App\Datasources\DB;

use HttpStack\DataBase\DBConnect;
use HttpStack\Datasource\Contracts\CRUD;
use PDO;
class ActiveTable implements CRUD
{
    protected DBConnect $db;
    protected string $table;
    protected array $columns = [];
    protected string $primaryKeyField = 'id';
    protected int $lastInsertId;
    protected array $data = [];

    public function __construct(DBConnect $db, string $table)
    {
        $this->db = $db;
        $this->table = $table;
        $this->loadColumns();
    }

    protected function loadColumns()
    {
        $sql = "SHOW COLUMNS FROM {$this->table}";
        $result = $this->db->query($sql);
        foreach ($result as $row) {
            $this->columns[] = $row['Field'];
            $this->data[$row['Field']] = null;
        }
    }
    public function fetch(string|array|null $key): array
    {
        // If $key is null, return all rows
        if ($key === null) {
            return $this->read();
        }
        // If $key is an array, treat as where clause
        if (is_array($key)) {
            return $this->read($key);
        }
        // If $key is a string, treat as primary key or unique column
        return $this->read([$this->columns[0] => $key]); // Assumes first column is PK
    }
    public function __get($name)
    {
        return $this->data[$name] ?? null;
    }

    public function __set($name, $value)
    {
        if (in_array($name, $this->columns)) {
            $this->data[$name] = $value;
        }
    }

    // Read: $table->read(['email' => 'chris@httpstack.tech'])


    // Create: $table->create(['email' => ..., ...])
    public function create(array $query, array $data): mixed
    {
        $cols = array_keys($data);
        $placeholders = array_fill(0, count($cols), '?');
        $sql = "INSERT INTO {$this->table} (`" . implode('`,`', $cols) . "`) VALUES (" . implode(',', $placeholders) . ")";
        return $this->db->execute($sql, array_values($data));
    }
    public function read(array $where = [], array $columns = []): mixed
    {
        $cols = $columns ? '`' . implode('`,`', $columns) . '`' : '*';
        $sql = "SELECT $cols FROM {$this->table}";
        $params = [];
        if ($where) {
            $clauses = [];
            foreach ($where as $col => $val) {
                $clauses[] = "`$col` = ?";
                $params[] = $val;
            }
            $sql .= " WHERE " . implode(' AND ', $clauses);
        };
        return $this->db->prepared($sql,$params);
    }
    // Update: $table->update(['email' => ...], ['user_level' => 'admin'])
    public function update(array $where, array $data): mixed
    {
        $set = [];
        $params = [];
        foreach ($data as $col => $val) {
            $set[] = "`$col` = ?";
            $params[] = $val;
        }
        $sql = "UPDATE {$this->table} SET " . implode(', ', $set);
        if ($where) {
            $clauses = [];
            foreach ($where as $col => $val) {
                $clauses[] = "`$col` = ?";
                $params[] = $val;
            }
            $sql .= " WHERE " . implode(' AND ', $clauses);
        }
        return $this->db->execute($sql, $params);
    }
    // Delete: $table->delete(['email' => ...])
    public function delete(array $where): mixed
    {
        $sql = "DELETE FROM {$this->table}";
        $params = [];
        if ($where) {
            $clauses = [];
            foreach ($where as $col => $val) {
                $clauses[] = "`$col` = ?";
                $params[] = $val;
            }
            $sql .= " WHERE " . implode(' AND ', $clauses);
        }
        return $this->db->execute($sql, $params);
    }
}