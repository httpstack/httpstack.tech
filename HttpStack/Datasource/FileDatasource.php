<?php
namespace HttpStack\Datasource;
use HttpStack\Abstract\AbstractDatasource;
use HttpStack\Contracts\DatasourceInterface;
use HttpStack\IO\FileLoader;

/**
 * FileDatasource
 *
 * This datasource can be constructed with either a directory path or a file path.
 * - If a directory is provided, it treats each JSON file in the directory as a data entry.
 * - If a file is provided, it treats the file as a single JSON data source.
 *
 * Usage:
 *   $ds = new FileDatasource('/path/to/data'); // Directory mode
 *   $ds = new FileDatasource('/path/to/data.json'); // Single file mode
 *
 * Methods:
 *   fetch($key): array
 *     - If $key is a string: returns the file (directory mode) or key (file mode).
 *     - If $key is an array: [file, key] for directory mode, or multiple keys for file mode.
 *     - If $key is null: returns all data.
 *
 *   save($data): bool
 *     - Saves data to the appropriate file(s), unless read-only.
 */
class FileDatasource extends AbstarctDatasource {
    protected FileLoader $fileLoader;
    
    protected string $path;
    protected bool $isDir;

    public function __construct(string $dataPath, bool $readOnly = true){
        $this->fileLoader = box("fileLoader");
        $this->fileLoader->mapDirectory("datasource", is_dir($dataPath) ? $dataPath : dirname($dataPath));
        $this->path = $dataPath;
        $this->isDir = is_dir($dataPath);
    }

    public function delete(mixed $var): void {
        if ($this->readOnly) {
            return;
        }
        if ($this->isDir) {
            // Directory mode
            $file = "{$this->path}/{$var}.json";
            if (is_file($file)) {
                unlink($file);
            }
        } else {
            // Single file mode
            if (is_file($this->path)) {
                $data = json_decode(file_get_contents($this->path), true) ?? [];
                unset($data[$var]);
                file_put_contents($this->path, json_encode($data, JSON_PRETTY_PRINT));
            }
        }
    }
    public function fetch(string|array|null $key): array {
        if ($this->isDir) {
            // Directory mode
            if (is_string($key)) {
                $file = "{$this->path}/{$key}.json";
                return is_file($file) ? (json_decode(file_get_contents($file), true) ?? []) : [];
            } elseif (is_array($key) && count($key) === 2) {
                [$file, $innerKey] = $key;
                $filePath = "{$this->path}/{$file}.json";
                if (is_file($filePath)) {
                    $data = json_decode(file_get_contents($filePath), true);
                    return isset($data[$innerKey]) ? [$innerKey => $data[$innerKey]] : [];
                }
                return [];
            } elseif ($key === null) {
                $result = [];
                foreach (glob("{$this->path}/*.json") as $file) {
                    $basename = pathinfo($file, PATHINFO_FILENAME);
                    $result[$basename] = json_decode(file_get_contents($file), true) ?? [];
                }
                return $result;
            }
        } else {
            // Single file mode
            if (is_file($this->path)) {
                $data = json_decode(file_get_contents($this->path), true) ?? [];
                if (is_string($key)) {
                    return isset($data[$key]) ? [$key => $data[$key]] : [];
                } elseif (is_array($key)) {
                    $result = [];
                    foreach ($key as $k) {
                        if (isset($data[$k])) $result[$k] = $data[$k];
                    }
                    return $result;
                } elseif ($key === null) {
                    return $data;
                }
            }
        }
        return [];
    }

    public function save(array $data): void {
        if ($this->readOnly) {
            return;
        }
        echo " <br/>datasource, gonna start write <br/>";
        
        if ($this->isDir) {
            // Save each key as a file
            foreach ($data as $filename => $content) {
                $file = "{$this->path}/{$filename}.json";
                
                file_put_contents($file, json_encode($content, JSON_PRETTY_PRINT));
            }
            return;
        } else {
            // Save all data to the single file
            file_put_contents($this->path, json_encode($data, JSON_PRETTY_PRINT));
        }
        /*
        */
    }
}