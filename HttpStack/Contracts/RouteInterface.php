<?php
namespace HttpStack\Contracts;

interface RouteInterface
{
    public function addHandler(callable $handler): self;
    public function getHandlers(): array;
    public function getMethod(): string;
    public function getUri(): string;
    public function getType(): string;
}