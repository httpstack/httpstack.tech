<?php

namespace HttpStack\Contracts;
/**
 * Interface ContainerInterface
 * @package Base\App\Contracts
 *
 * This interface defines the methods for a dependency injection container.
 */

interface ContainerInterface
{
    public function bind(string $abstract, mixed $concrete): void;
    public function make(string $abstract, mixed ...$params): mixed;
    public function build(string $concrete, array $params);
}