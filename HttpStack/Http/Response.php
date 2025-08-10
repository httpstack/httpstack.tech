<?php
namespace HttpStack\Http;
use HttpStack\Contracts\ResponseInterface;

class Response implements ResponseInterface{
    private int $statusCode;
    private array $headers;
    private string $body;
    public bool $sent = false;
    public function __construct(int $statusCode = 200, array $headers = [], string $body = "") {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->body = $body;
    }
    public function setContentType(string $contentType){
        $this->headers["Content-Type"] = $contentType;
        return $this;
    }
    public function setStatusCode(int $code): void {
        $this->statusCode = $code;
    }

    public function getStatusCode(): int {
        return $this->statusCode;
    }

    public function setHeader(string $name, string $value): void {
        $this->headers[$name] = $value;
    }

    public function getHeaders(): array {
        return $this->headers;
    }

    public function setBody(string $content): void {
        $this->body = $content;
    }
    public function redirect(string $url): void {
        $this->headers["Location"] = $url;
    }

    public function getBody(): string {
        return $this->body;
    }

    public function send(): void {
        
        http_response_code($this->statusCode);
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
        $this->sent = true;
        echo $this->body;
    }
}

?>