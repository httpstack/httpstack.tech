<?php 
namespace HttpStack\Abstract;
use HttpStack\Contracts\ModelInterface;
abstract class AbstractModel implements ModelInterface{ 
    protected array $model;
    protected array $_model;
    /**
     * The AbstractModel will implement the methods to 
     * fetch/save - from and to the datasource and 
     * store/restore - the model to keep the last state 
     *                of the model, backed up before every write
     * and declare 3 instance properties
     * @param array $model - the current model data
     * @param array $_model - the previous model data
     * @param DatasourceInterface $dataSource - the datasource to fetch and save data from/to
     * 
     * It will require you to implement your models operations
     * with a consistent parameter pattern
     */
    public function __construct(protected DatasourceInterface $dataSource){
        $this->model = [];
        $this->_model = [];
        $this->fetch();
    }
    protected function fetch(string|array $key = null):void{
        $data = $this->dataSource->fetch($key);
        $this->set($data);
    }
    protected function save():void{   
            $this->store();
            $this->dataSource->save($this->model);
    }
    public function reStore():array{
        $this->model = $this->_model;
        return $this->model;
    } 
    public function store():array{
        $this->_model = $this->model;
        return $this->model;
    }

    abstract function remove(string $abstract):void;
    abstract function clear():void;
    abstract function set(string|array $abstract, string|array $concrete = ''):void;
    abstract function setAll(array $data):void;
    abstract function get($abstract=''):mixed;
    abstract function getAll():array;
    abstract function has($abstract):bool;
    
}
?>