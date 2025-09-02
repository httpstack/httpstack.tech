<?php
namespace HttpStack\App\Datasources\FS;

use HttpStack\Contracts\DatasourceInterface;
use HttpStack\Datasource\AbstractDatasource;
use HttpStack\Datasource\Contracts\CRUD;
class JsonDirectory implements CRUD
{
    protected string $file;
    protected array $data = [];

    public function __construct(string $file)
    {
        $this->file = $file;
        $this->load();
    }

protected function load()
{
    if (is_dir($this->file)) {
        $this->data = [];
        foreach (glob($this->file . '/*.json') as $jsonFile) {
            $key = basename($jsonFile, '.json');
            $json = file_get_contents($jsonFile);
            $this->data[$key] = json_decode($json, true) ?? [];
        }
    } elseif (is_file($this->file)) {
        $json = file_get_contents($this->file);
        $this->data = json_decode($json, true) ?? [];
    }
}
    public function fetch(string|array|null $key): array
    {
        if ($key === null) {
            return $this->data;
        }
        if (is_array($key)) {
            return $this->read($key);
        }
        return $this->data[$key] ?? [];
    }
    protected function saveData()
    {
        file_put_contents($this->file, json_encode($this->data, JSON_PRETTY_PRINT));
    }

    // Recursive search for nested queries
    protected function recursiveFind($data, $query)
    {
        if (empty($query)) return $data;

        foreach ($query as $key => $val) {
            if (is_array($val)) {
                if (isset($data[$key])) {
                    return $this->recursiveFind($data[$key], $val);
                }
                return [];
            } else {
                // $val is a value, filter array of objects
                if (isset($data[$key]) && is_array($data[$key])) {
                    return array_filter($data[$key], function ($item) use ($val, $key) {
                        return isset($item[$key]) && $item[$key] == $val;
                    });
                } elseif (is_array($data)) {
                    // If $data is an array of objects
                    return array_filter($data, function ($item) use ($key, $val) {
                        return isset($item[$key]) && $item[$key] == $val;
                    });
                }
            }
        }
        return [];
    }

    // Unified read
    public function read(array $query = [], array $columns = []): array
    {
        $result = $this->recursiveFind($this->data, $query);

        // If columns specified, filter columns
        if ($columns && is_array($result)) {
            $result = array_map(function ($item) use ($columns) {
                return array_intersect_key($item, array_flip($columns));
            }, $result);
        }
        return $result;
    }

    // Unified create (append to array)
    public function create(array $query, array $data): mixed
    {
        $ref = &$this->data;
        foreach ($query as $key => $val) {
            if (!isset($ref[$key])) $ref[$key] = [];
            $ref = &$ref[$key];
        }
        if (is_array($ref)) {
            $ref[] = $data;
            $this->saveData();
            return true;
        }
        return false;
    }

    // Unified update
    public function update(array $query, array $data): bool
    {
        $ref = &$this->recursiveFind($this->data, $query);
        if (is_array($ref)) {
            foreach ($ref as &$item) {
                foreach ($data as $k => $v) {
                    $item[$k] = $v;
                }
            }
            $this->saveData();
            return true;
        }
        return false;
    }

    // Unified delete
    public function delete(array $query): bool
    {
        $ref = &$this->recursiveFind($this->data, $query);
        if (is_array($ref)) {
            foreach ($ref as $i => $item) {
                unset($ref[$i]);
            }
            $this->saveData();
            return true;
        }
        return false;
    }

    // Save all data
    public function save(array $data): void
    {
        $this->data = $data;
        $this->saveData();
    }
}