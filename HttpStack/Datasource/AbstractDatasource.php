<?php
namespace HttpStack\Datasource;

use Closure;
use HttpStack\Datasource\Contracts\CRUD;
use HttpStack\Datasource\Contracts\Datasource;
abstract class AbstractDatasource implements Datasource
{
    /**
     * @var array $dataCache Cache for data read from the data source
     */
    protected array $dataCache = [];

    /**
     * @var bool $readOnly Indicates if the datasource is read-only
     * This var is implicitly set by the constructor
     */
    
    /**
     * Constructor to initialize the datasource with configuration
     *
     * @param array $config Configuration array containing 'crudHandlers', 'readOnly', and 'endPoint'
     * @throws \InvalidArgumentException if the configuration is not provided or invalid
     */
    public function __construct(protected bool $readOnly)
    {
        $this->readOnly = $readOnly;
    }
    /**
     * Implement the Datasource Contract
     *
     * @method setReadOnly
     * @method isReadOnly
     * @method disconnect
     * @param bool $readOnly
     */
    public function setReadOnly(bool $readOnly): void
    {
        $this->readOnly = $readOnly;
    }
    public function isReadOnly(): bool
    {
        return $this->readOnly;
    }
    public function disconnect(): mixed
    {
        // Simulate disconnecting from a data source
        return true;
    }


    /**
     * Require Child class to implement the CRUD Contract
     *
     * @method create
     * @method read
     * @method update
     * @method delete
     * @param array $data
     */ 
    abstract public function create(array $query, array $data): mixed;
    abstract public function read(array $payload = []): mixed;
    abstract public function update(array $where, array $payload): mixed;
    abstract public function delete(array $payload):mixed;
}
?>