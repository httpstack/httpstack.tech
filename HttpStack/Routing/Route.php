<?php
namespace HttpStack\Routing;
use \Closure;
use HttpStack\Contracts\RouteInterface;
class Route implements RouteInterface
{
    protected string $method;
    protected string $uri;
    protected string $type; // 'route' or 'mw'
    protected array $handlers = [];

    public function __construct(string $method, string $uri, callable $handler, string $type = 'route')
    {
        $this->method = strtoupper($method);
        $this->uri = $uri;
        $this->type = $type;
        $this->addHandler($handler);
    }

    public function addHandler(callable $handler): self
    {
        $this->handlers[] = $handler;
        return $this;
    }

    public function getHandlers(): array
    {
        return $this->handlers;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
?>