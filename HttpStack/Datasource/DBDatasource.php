<?php 
namespace HttpStack\Datasource;
use HttpStack\Abstract\AbstractDatasource;
use HttpStack\DataBase\DBConnect;

class DBDatasource extends \AbstractDatasource {
    protected DBConnect $dbConnect;

    public function __construct(DBConnect $dbConnect, string $tableName, bool $readOnly = true) {
        parent::__construct($readOnly);
        $this->dbConnect = $dbConnect;
        $this->tableName = $tableName;
        $this->primaryKey = 'id'; // Default primary key, can be overridden

        $this->rowID = -1;
    }
    public function setRowID(int $rowID): void {
        $this->rowID = $rowID;
    }
    public function getRowID(): int {
        return $this->rowID;
    }
    public function fetch(string|array|null $key): array {
        if (is_null($key)) {
            $stmt = $this->pdo->query("SELECT * FROM {$this->tableName}");
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } elseif (is_array($key)) {
            // Handle multiple keys
            $placeholders = implode(',', array_fill(0, count($key), '?'));
            $stmt = $this->pdo->prepare("SELECT * FROM {$this->tableName} WHERE id IN ({$placeholders})");
            $stmt->execute($key);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } else {
            // Handle single key
            $stmt = $this->pdo->prepare("SELECT * FROM {$this->tableName} WHERE id = ?");
            $stmt->execute([$key]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
    }

    public function save(array $data): void {
        if (empty($data)) {
            return;
        }
        // Assuming data contains 'id' and other fields
        if (isset($data['id'])) {
            // Update existing record
            $fields = implode('=?, ', array_keys($data)) . '=?';
            $stmt = $this->pdo->prepare("UPDATE {$this->tableName} SET {$fields} WHERE id = ?");
            $data['id'] = array_pop($data); // Move id to the end for binding
            $stmt->execute(array_values($data));
        } else {
            // Insert new record
            $fields = implode(',', array_keys($data));
            $placeholders = implode(',', array_fill(0, count($data), '?'));
            $stmt = $this->pdo->prepare("INSERT INTO {$this->tableName} ({$fields}) VALUES ({$placeholders})");
            $stmt->execute(array_values($data));
        }
    }

    public function delete(mixed $var): void {
        if (is_array($var)) {
            // Delete multiple records
            $placeholders = implode(',',
                array_fill(0, count($var), '?')
            );
            $stmt = $this->pdo->prepare("DELETE FROM {$this->tableName} WHERE id IN ({$placeholders})");
            $stmt->execute($var);
        } else {
            // Delete single record
            $stmt = $this->pdo->prepare("DELETE FROM {$this->tableName} WHERE id = ?");
            $stmt->execute([$var]);
        }
    }
}
//         }
//         } else {
//             // Single file mode
//             $data = json_decode(file_get_contents($this->path), true) ?? [];
//             if (is_string($key)) {
//                 return isset($data[$key]) ? [$key => $data[$key]] : [];
//             } elseif (is_array($key)) {
//                 $result = [];                                