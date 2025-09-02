<?php

namespace HttpStack\Datasource\Contracts;

interface Datasource
{
    public function fetch(string|array|null $key): array;
    public function save(array $data):void;
    public function setReadOnly(bool $readOnly): void;
    public function isReadOnly(): bool;  
    public function disconnect(): mixed;
}