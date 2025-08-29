<?php

namespace HttpStack\Tests;

use PHPUnit\Framework\TestCase;
use HttpStack\Container\Container;
use HttpStack\Exceptions\AppException;
use Closure;
use ReflectionClass;
use ReflectionParameter;

// --- Test Classes for Container ---

class TestClassNoParams
{
    public string $name = 'default';

    public function __construct()
    {
        // No params
    }

    public function setName(string ): void
    {
        ->name = ;
    }

    public function getName(): string
    {
        return ->name;
    }
}

