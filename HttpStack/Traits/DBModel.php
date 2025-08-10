<?php
namespace HttpStack\Traits;
use HttpStack\DataBase\DBConnect;
trait DBModel{
    protected array $queries = [];

    public function addQuery(string $queryName, string $sql, array $params = []):self{
        $this->queries[$queryName] = [$sql,$params];
        return $this;
    }
    public function runQuery(string $queryName, string $modelKey):self{
        list($sql,$params) = $this->queries[$queryName];
        $stmt = $this->dbConnect->prepare($sql);
        $stmt->execute($params);
        $this->model[$modelKey] = $stmt->fetchAll();
        return $this;
    }
    public function setDB(DBConnect $dbConnect){
        $this->dbConnect = $dbConnect;
    }
    public function query(string $sql, array $params = []): array
    {
        $stmt = $this->dbConnect->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function execute(string $sql, array $params = []): bool
    {
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }
}
?>