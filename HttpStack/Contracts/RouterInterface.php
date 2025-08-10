<?php
namespace sApp\Contracts;

interface RouterInterface
{
    public function get(string $uri, callable $handler): void;

    public function dispatch(string $uri, string $method): void;
}

?>