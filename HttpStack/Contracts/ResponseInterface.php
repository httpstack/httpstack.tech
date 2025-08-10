<?php
namespace HttpStack\Contracts;
/**
 * Interface ResponseInterface
 * @package Httpstack\Http\Interfaces
 *
 * This interface defines the methods for handling HTTP responses.
 */

 interface ResponseInterface {
    public function setStatusCode(int $code): void;
    public function getStatusCode(): int;
    public function setHeader(string $name, string $value): void;
    public function getHeaders(): array;
    public function setBody(string $content): void;
    public function getBody(): string;
    public function send(): void;
}
?>