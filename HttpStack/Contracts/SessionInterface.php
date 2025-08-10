<?php
namespace HttpStack\Contracts;

interface SessionInterface {
    public function start():void;
    public function get(string $key, $default = null);
    public function set(string $key, $value): void;
    public function all(): array;
    public function destroy(): void;
}