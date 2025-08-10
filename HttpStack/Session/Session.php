<?php
namespace HttpStack\Session;
use HttpStack\Contracts\SessionInterface;
class Session implements SessionInterface {
    public function start():void{
        session_start();
    }
    public function get(string $key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }
    public function set(string $key, $value): void {
        $_SESSION[$key] = $value;
    }
    public function all(): array {
        return $_SESSION;
    }
    public function destroy(): void {
        session_destroy();
    }
}