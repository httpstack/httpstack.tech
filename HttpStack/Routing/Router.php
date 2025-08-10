<?php
namespace HttpStack\Routing;
class Router {
    private $after = [];
    private $before = [];

    public function after(Route $route){
        $newRouteUri = $route->getUri();
        $newRouteMethod = $route->getMethod();
        $newRouteHandlers = $route->getHandlers();
        //CHECK TO SEE IF THE ROUTE METHOD AND URI ARE REGISTERED
        if(isset($this->after[$newRouteMethod]) && isset($this->after[$newRouteMethod][$newRouteUri])){
            $oldRouteHandlers = $this->after[$newRouteMethod][$newRouteUri];
            foreach($newRouteHandlers as $newHandler){
                array_push($oldRouteHandlers, $newHandler);
            }
        }else{
            $this->after[$newRouteMethod][$newRouteUri] = $newRouteHandlers;
        }
    }
    public function before(Route $route){
        $newRouteUri = $route->getUri();
        $newRouteMethod = $route->getMethod();
        $newRouteHandlers = $route->getHandlers();
        //CHECK TO SEE IF THE ROUTE METHOD AND URI ARE REGISTERED
        if(isset($this->before[$newRouteMethod]) && isset($this->before[$newRouteMethod][$newRouteUri])){
            $oldRouteHandlers = $this->after[$newRouteMethod][$newRouteUri];
            foreach($newRouteHandlers as $newHandler){
                array_push($oldRouteHandlers, $newHandler);
            }
        }else{
            $this->before[$newRouteMethod][$newRouteUri] = $newRouteHandlers;
        }
    }

    public function dispatch($request, $response, $container) { 
        $method = $request->getMethod();
        $uri = $request->getUri();
        //dd($this->before);
        /**
         * LOOP MIDDLEWARES
         */
        foreach($this->before[$method] as $pattern => $handlers){
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
                foreach($handlers as $middleware){
                    if(is_array($middleware)){
                        list($className, $methodName) = $middleware;
                        //$instance = new $className();
                        $callable = [$className,$methodName];
                    }else{
                        $callable = $middleware;
                    }   
                    //dd($className);
                    call_user_func_array($callable, [$request,$response,$container,$matches]);           
                }
            }
        }
        /**
         * LOOP WARES
         * 
         */
        foreach($this->after[$method] as $pattern => $handlers){
            $matches = [];
            $regex = preg_replace('/\{\w+\}/', '([^/\/]+)', $pattern);
            
            if(preg_match("#$regex#", $request->getUri(),$matches)){
                foreach($handlers as $afterWare){
                    if(is_array($afterWare)){
                        list($className, $methodName) = $afterWare;
                        $instance = new $className();
                        $callable = [$instance,$methodName];
                    }else{
                        $callable = $afterWare;
                    }
                    call_user_func_array($callable, [$request,$response,$container,$matches]); 
                }
                //$container->call($this->after[$method][$key], [$request, $response, $container, $matches]);

            }
        }
    }
}

?>