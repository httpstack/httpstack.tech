<?php

namespace HttpStack\Datasource\Contracts;

interface CRUD
{
    public function create(array $query, array $data): mixed;
    public function read(array $payload = []): mixed;
    public function update(array $where, array $payload): mixed;
    public function delete(array $payload): mixed;
}