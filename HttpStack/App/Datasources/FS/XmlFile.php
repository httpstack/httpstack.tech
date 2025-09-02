<?php

namespace HttpStack\App\Datasources\FS;

use HttpStack\Contracts\DatasourceInterface;

class XmlFile implements DatasourceInterface
{
    protected string $file;
    protected \SimpleXMLElement $xml;

    public function __construct(string $file)
    {
        $this->file = $file;
        if (is_file($file)) {
            $this->xml = simplexml_load_file($file);
        } else {
            $this->xml = new \SimpleXMLElement('<root/>');
        }
    }

    public function fetch(string|array|null $key): array
    {
        if ($key === null) {
            return json_decode(json_encode($this->xml), true);
        }
        if (is_array($key)) {
            return $this->read($key);
        }
        // If $key is a string, return that element
        return isset($this->xml->$key) ? json_decode(json_encode($this->xml->$key), true) : [];
    }

    public function read(array $query = [], array $columns = []): array
    {
        $data = json_decode(json_encode($this->xml), true);
        // Traverse query path
        foreach ($query as $key => $val) {
            if (isset($data[$key])) {
                $data = $data[$key];
                if (is_array($val)) {
                    foreach ($val as $subKey => $subVal) {
                        if (is_array($data)) {
                            $data = array_filter($data, function ($item) use ($subKey, $subVal) {
                                return isset($item[$subKey]) && $item[$subKey] == $subVal;
                            });
                        }
                    }
                }
            }
        }
        // Filter columns
        if ($columns && is_array($data)) {
            $data = array_map(function ($item) use ($columns) {
                return array_intersect_key($item, array_flip($columns));
            }, $data);
        }
        return $data;
    }

    public function save(array $data): void
    {
        // Overwrite XML with new data
        $xml = new \SimpleXMLElement('<root/>');
        $this->arrayToXml($data, $xml);
        $xml->asXML($this->file);
        $this->xml = $xml;
    }

    public function delete(mixed $var): mixed
    {
        // Remove element by key
        $data = json_decode(json_encode($this->xml), true);
        if (is_string($var) && isset($data[$var])) {
            unset($data[$var]);
            $this->save($data);
            return true;
        }
        return false;
    }

    protected function arrayToXml(array $data, \SimpleXMLElement &$xml)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $subnode = $xml->addChild(is_numeric($key) ? "item$key" : $key);
                $this->arrayToXml($value, $subnode);
            } else {
                $xml->addChild(is_numeric($key) ? "item$key" : $key, htmlspecialchars($value));
            }
        }
    }
}