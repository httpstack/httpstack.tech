<?php
namespace HttpStack\Http;
use HttpStack\Contracts\RequestInterface;

class Request implements RequestInterface{   
    private string $method;
   private string $uri;
   private array $queryParams;
   private string $body;
   private array $headers;
   private array $params = []; // URL parameters

   public function __construct() {
       $strBase = "/\/public/";
       $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
       $this->uri = preg_replace($strBase, "", $_SERVER['REQUEST_URI'] ?? '/');
       $this->queryParams = $_GET;
       $this->body = file_get_contents('php://input');
       $this->headers = getallheaders();
   }

   public function getMethod(): string {
       return $this->method;
   }

   public function getUri(): string {
       return $this->uri;
   }

   public function getQueryParams(): array {
       return $this->queryParams;
   }

   public function getBody(): string {
       return $this->body;
   }

   public function getHeaders(): array {
       return $this->headers;
   }

   public function getHeader(string $name): ?string {
       return $this->headers[$name] ?? null;
   }

   public function setParams(array $params): void {
       $this->params = $params;
   }

   public function setQueryParams(array $queryParams): void {
       $this->queryParams = $queryParams;
   }
   public function getQueryParam(string $key): ?string {
       return $this->queryParams[$key] ?? null;
   }
   public function getParams(): array {
       return $this->params;
   }

   public function getParam(string $key): ?string {
       return $this->params[$key] ?? null;
   }
}