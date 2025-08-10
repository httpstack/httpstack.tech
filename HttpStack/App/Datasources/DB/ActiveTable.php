<?php
namespace HttpStack\App\Datasources\DB;

use PDOException;
use HttpStack\DataBase\DBConnect; // Corrected namespace if needed
use HttpStack\Datasource\Contracts\CRUD; // Import the CRUD interface
use HttpStack\Datasource\AbstractDatasource; // Import PDOException for specific error handling

class ActiveTable extends AbstractDatasource implements CRUD
{
    protected DBConnect $conn;
    protected string $page;
    protected string $table = "pages";

    /**
     * Constructor for the DBDatasource.
     *
     * @param DBConnect $conn The database connection instance.
     * @param string $table The name of the database table to operate on.
     * @param bool $readOnly Whether this datasource instance is read-only.
     */
    public function __construct(DBConnect $conn, string $page, bool $readOnly = true)
    {
        parent::__construct($readOnly);
        $this->conn = $conn;
        $this->page = $page;
    }

    /**
     * Creates a new record in the database table.
     *
     * @param array $payload An associative array of data to insert (column => value).
     * @return mixed The ID of the newly inserted row, or true on success.
     * @throws \RuntimeException If the operation fails or is not allowed in read-only mode.
     */
    public function create(array $payload): mixed
    {
        if ($this->isReadOnly()) {
            throw new \RuntimeException("Cannot create data in a read-only datasource.");
        }

        if (empty($payload)) {
            throw new \InvalidArgumentException("Payload for create operation cannot be empty.");
        }

        $columns = implode(', ', array_keys($payload));
        $placeholders = ':' . implode(', :', array_keys($payload));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";

        try {
            $stmt = $this->conn->prepare($sql);
            foreach ($payload as $key => &$value) {
                $stmt->bindValue(':' . $key, $value);
            }
            $stmt->execute();

            // Return the last inserted ID if available, otherwise true
            return $this->conn->lastInsertId() ?: true;
        } catch (PDOException $e) {
            throw new \RuntimeException("Database create operation failed: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    /**
     * Reads data from the database table.
     *
     * @param array $query An associative array of query parameters (column => value) for filtering.
     * If empty, all records are returned.
     * @return array An array of associative arrays, where each inner array represents a record.
     * @throws \RuntimeException If the read operation fails.
     */
    public function read(array $query = []): array
    {
        $sql = "SELECT * FROM {$this->table}";
        $conditions = [];
        $params = [];

        foreach ($query as $column => $value) {
            $conditions[] = "{$column} = :{$column}";
            $params[":{$column}"] = $value;
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new \RuntimeException("Database read operation failed: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    /**
     * Updates existing records in the database table.
     *
     * @param array $payload An associative array with 'data' (column => new_value) and 'where' (column => value) keys.
     * 'where' is used to specify which records to update.
     * @return bool True on success, false on failure.
     * @throws \RuntimeException If the operation fails or is not allowed in read-only mode.
     * @throws \InvalidArgumentException If 'data' or 'where' keys are missing or empty.
     */
    public function update(array $payload): bool
    {
        if ($this->isReadOnly()) {
            throw new \RuntimeException("Cannot update data in a read-only datasource.");
        }

        if (!isset($payload['data']) || !is_array($payload['data']) || empty($payload['data'])) {
            throw new \InvalidArgumentException("Update payload must contain non-empty 'data' array.");
        }
        if (!isset($payload['where']) || !is_array($payload['where']) || empty($payload['where'])) {
            throw new \InvalidArgumentException("Update payload must contain non-empty 'where' array for conditions.");
        }

        $setClauses = [];
        $params = [];
        foreach ($payload['data'] as $column => $value) {
            $setClauses[] = "{$column} = :set_{$column}";
            $params[":set_{$column}"] = $value;
        }

        $whereClauses = [];
        foreach ($payload['where'] as $column => $value) {
            $whereClauses[] = "{$column} = :where_{$column}";
            $params[":where_{$column}"] = $value;
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $setClauses) . " WHERE " . implode(' AND ', $whereClauses);

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount() > 0; // Return true if at least one row was affected
        } catch (PDOException $e) {
            throw new \RuntimeException("Database update operation failed: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    /**
     * Deletes records from the database table.
     *
     * @param array $payload An associative array of query parameters (column => value) to identify records to delete.
     * @return bool True on success, false on failure.
     * @throws \RuntimeException If the operation fails or is not allowed in read-only mode.
     * @throws \InvalidArgumentException If the payload is empty.
     */
    public function delete(array $payload): bool
    {
        if ($this->isReadOnly()) {
            throw new \RuntimeException("Cannot delete data in a read-only datasource.");
        }

        if (empty($payload)) {
            throw new \InvalidArgumentException("Payload for delete operation cannot be empty (must specify conditions).");
        }

        $conditions = [];
        $params = [];
        foreach ($payload as $column => $value) {
            $conditions[] = "{$column} = :{$column}";
            $params[":{$column}"] = $value;
        }

        $sql = "DELETE FROM {$this->table} WHERE " . implode(' AND ', $conditions);

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount() > 0; // Return true if at least one row was affected
        } catch (PDOException $e) {
            throw new \RuntimeException("Database delete operation failed: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }
}
