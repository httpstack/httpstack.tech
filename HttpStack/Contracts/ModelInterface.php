<?php 
namespace HttpStack\Contracts;
interface ModelInterface{ 
    public function __construct(DatasourceInterface $dataSource);
    public function remove(string $abstract):void;
    public function clear():void;
    public function set(string|array $abstract, string|array $concrete = ''):void;
    public function setAll(array $data):void;
    public function get($abstract=''):mixed;
    public function getAll():array;
    public function has($abstract):bool;  
}
?>