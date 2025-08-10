<?php
namespace HttpStack\Contracts;

/**
 * Interface RequestInterface
 * @package Httpstack\Http\Interfaces
 *
 * This interface defines the methods for handling HTTP requests.
 */

 interface RequestInterface {
    public function getMethod(): string;
    public function getUri(): string;
    public function getQueryParams(): array;
    public function getBody(): string;
    public function getHeaders(): array;
    public function getHeader(string $name): ?string;
    
}

?>