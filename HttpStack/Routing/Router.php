<?php

namespace HttpStack\Routing;

use HttpStack\Container\Container;
use HttpStack\Http\Request;


class Router
{
    public $after = [];
    private $before = [];
    public function get($uri, $handle): void
    {
        $r = new Route("GET", $uri, $handle, "after");
        $this->after($r);
    }
    public function post($uri, $handle): void
    {
        $r = new Route("POST", $uri, $handle, "after");
        $this->after($r);
    }
    public function mw($method, $uri, $handle): void
    {
        $r = new Route($method, $uri, $handle, "before");
        $this->before($r);
    }
    public function after(Route $route)
    {

        $newRouteUri = $route->getUri();
        $newRouteMethod = $route->getMethod();
        $newRouteHandlers = $route->getHandlers();
        //CHECK TO SEE IF THE ROUTE METHOD AND URI ARE REGISTERED
        if (isset($this->after[$newRouteMethod]) && isset($this->after[$newRouteMethod][$newRouteUri])) {

            $oldRouteHandlers = $this->after[$newRouteMethod][$newRouteUri];
            foreach ($newRouteHandlers as $newHandler) {
                array_push($oldRouteHandlers, $newHandler);
            }
        } else {
            $this->after[$newRouteMethod][$newRouteUri] = $newRouteHandlers;
        }
    }
    public function before(Route $route)
    {
        $newRouteUri = $route->getUri();
        $newRouteMethod = $route->getMethod();
        $newRouteHandlers = $route->getHandlers();
        //CHECK TO SEE IF THE ROUTE METHOD AND URI ARE REGISTERED
        if (isset($this->before[$newRouteMethod]) && isset($this->before[$newRouteMethod][$newRouteUri])) {
            $oldRouteHandlers = $this->after[$newRouteMethod][$newRouteUri];
            foreach ($newRouteHandlers as $newHandler) {
                array_push($oldRouteHandlers, $newHandler);
            }
        } else {
            $this->before[$newRouteMethod][$newRouteUri] = $newRouteHandlers;
        }
    }
    public function route(Route $route)
    {
        $type = $route->getType();
        switch ($type) {
            case "after":
                $this->after($route);
                break;

            case "before":
                $this->before($route);
                break;
        }
    }


    public function dispatch(Container $c)
    {
        $request = $c->make(Request::class);
        $method = $request->getMethod();
        $uri = $request->getUri();
        //dd($this->before);
        /**
         * LOOP MIDDLEWARES
         */
        if (isset($this->before[$method])) {
            foreach ($this->before[$method] as $pattern => $handlers) {
                // Convert {param} to regex
                $regex = preg_replace('/\{\w+\}/', '([^/]+)', $pattern);
                // Convert wildcard * to regex
                $regex = str_replace('*', '.*', $regex);
                // Allow full match for .*
                if ($regex === '.*') {
                    $regex = '.*';
                }
                $matches = [];
                if (preg_match("#^$regex$#", $uri)) {
                    foreach ($handlers as $middleware) {
                        if (is_array($middleware)) {
                            list($className, $methodName) = $middleware;
                            //$instance = new $className();
                            $callable = [$className, $methodName];
                        } else {
                            $callable = $middleware;
                        }
                        //dd($className);
                        call_user_func_array($callable, [$c, $matches]);
                    }
                }
            }
        }
        /**
         * LOOP WARES
         * 
         */
        foreach ($this->after[$method] as $pattern => $handlers) {
            $matches = [];
            $regex = preg_replace('/\{\w+\}/', '([^/\/]+)', $pattern);

            if (preg_match("#$regex#", $request->getUri(), $matches)) {
                foreach ($handlers as $afterWare) {
                    if (is_array($afterWare)) {
                        list($className, $methodName) = $afterWare;
                        $instance = new $className();
                        $callable = [$instance, $methodName];
                    } else {
                        $callable = $afterWare;
                    }
                    call_user_func_array($callable, [$c, $matches]);
                }
                //$container->call($this->after[$method][$key], [$request, $response, $container, $matches]);

            }
        }
    }

    public function getRoutes()
    {
        return $this->after;
    }
    public function getMiddleWares()
    {
        return $this->before;
    }
}
