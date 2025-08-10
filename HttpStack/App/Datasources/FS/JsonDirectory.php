<?php
namespace HttpStack\App\Datasources\FS;
use HttpStack\Datasource\Contracts\CRUD;
use HttpStack\Datasource\AbstractDatasource;
class JsonDirectory extends AbstractDatasource implements CRUD
{
    protected string $jsonDirectory;
    protected array $dataCache = [];
    protected bool $isCacheValid = false;   
    /**
     * Constructor to initialize the JsonDirectory datasource
     *
     * @param array $config Configuration array containing 'crudHandlers', 'readOnly', and 'endPoint'
     * @throws \InvalidArgumentException if the configuration is not provided or invalid
     */
    public function __construct(string $dirPath,bool $readOnly)
    {
        parent::__construct($readOnly);
        $this->jsonDirectory = $dirPath ?? '';
        if (!$this->jsonDirectory || !is_dir($this->jsonDirectory)) {
            throw new \InvalidArgumentException("Invalid JSON directory path provided.");
        }
        $this->dataCache = $this->read();
    }
    /**
     * Read data from the JSON directory
     *
     * @param array $query Query parameters to filter the data
     * @return array The data read from the JSON directory
     */
    public function read(array $query = []): array{
        if(empty($this->jsonDirectory) || !is_dir($this->jsonDirectory)) {
            return [];
        }
        if($this->dataCache && $this->isCacheValid) {
            // If cache has the data, return it
            return $this->dataCache;
        }
        $files = [];
        foreach (scandir($this->jsonDirectory) as $file) {
            if ($file !== '.' && $file !== '..') {
                $absPath = realpath($this->jsonDirectory . '/' . $file);
                if (is_file($absPath) && pathinfo($absPath, 4) === 'json') {
                    $content = file_get_contents($absPath);
                    if ($content !== false) {
                        //GET BASENAME AND MAKE IT THE KEY
                        $fileName = pathinfo($absPath, PATHINFO_BASENAME);
                        $files[$fileName] = json_decode($content, true);
                    }
                }
            }
        }
        if (count($query) > 0) {
            // Filter files based on the query keys
            $files = array_intersect_key($files, array_flip($query));
        }
        $this->dataCache = $files;
        //if cache has the data send it
       return $this->dataCache;
    }
    // Additional methods specific to JsonDirectory can be added here
    //after ANY C.U.D. operation, the cache should be invalidated
    public function create(array $payload): mixed{
        list($fileName, $data) = $payload;
        $filePath = $this->jsonDirectory . '/' . $fileName;
        if (file_exists($filePath)) {
            throw new \RuntimeException("File already exists: $fileName");
        }
        if ($this->isReadOnly()) {
            throw new \RuntimeException("Cannot create data in a read-only datasource."); 
        }
        $jsonData = json_encode($data, 128);
        if (file_put_contents($filePath, $jsonData) === false) {
            throw new \RuntimeException("Failed to write data to file: $fileName"); 
        }
        $this->isCacheValid = false; // Invalidate cache after creation
        return true;
    }

    public function update(array $payload): mixed{
        list($fileName, $data) = $payload;
        $filePath = $this->jsonDirectory . '/' . $fileName;
        if (!file_exists($filePath)) {
            throw new \RuntimeException("File does not exist: $fileName");
        }
        if ($this->isReadOnly()) {
            throw new \RuntimeException("Cannot update data in a read-only datasource.");
        }
        $jsonData = json_encode($data, 128);
        if (file_put_contents($filePath, $jsonData) === false) {
            throw new \RuntimeException("Failed to write data to file: $fileName");
        }
        $this->isCacheValid = false; // Invalidate cache after update
        return true;
    }
    public function delete(array $payload): mixed{
        list($fileName) = $payload;
        $filePath = $this->jsonDirectory . '/' . $fileName;
        if (!file_exists($filePath)) {
            throw new \RuntimeException("File does not exist: $fileName");
        }
        if ($this->isReadOnly()) {
            throw new \RuntimeException("Cannot delete data in a read-only datasource.");
        }
        if (!unlink($filePath)) {
            throw new \RuntimeException("Failed to delete file: $fileName");
        }
        $this->isCacheValid = false; // Invalidate cache after deletion
        return true;
    }
}


?>