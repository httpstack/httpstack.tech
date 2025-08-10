<?php 
namespace HttpStack\Container;
use Closure;
use ReflectionClass;
use ReflectionParameter;
use ReflectionException;
use HttpStack\Exceptions\AppException;
use HttpStack\Contracts\ContainerInterface;

class Container implements ContainerInterface {
    protected $bindings = [];
    protected $instances = [];
    private array $props = [];
    protected $aliases = [];


    public function __construct() {

    }

    /**
     * NEW: Add an alias for a class FQN.
     */
    public function alias(string $alias, string $fqn): void
    {
        $this->aliases[$alias] = $fqn;
    }

    public function bind(string $abstract, $concrete): void {
        $this->bindings[$abstract] = $concrete;
    }


    public function singleton(string $abstract, $concrete) {
        $this->bindings[$abstract] = $concrete;
        $this->instances[$abstract] = null; // Mark it as a singleton
    }

    public function make(string $abstract, mixed ...$params): mixed {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        return $this->resolve($abstract, $params);
    }

    public function addProperty(string $name, $value) {
        $this->props[$name] = $value;
    }

    public function removeProperty(string $name) {
        unset($this->props[$name]);
    }

    public function getProperty(string $name) {
        return $this->props[$name] ?? null;
    }

    public function hasProperty(string $name) {
        return isset($this->props[$name]);
    }
    protected function resolveDependencies(array $dependencies, array $parameters): array
    {
        $results = [];
        
        foreach ($dependencies as $dependency) {
            // If the parameter is in the given parameters, use it
            if (array_key_exists($dependency->name, $parameters)) {
                $results[] = $parameters[$dependency->name];
                continue;
            }
            
            // If the parameter is a class, resolve it from the container
            $results[] = $this->resolveClass($dependency);
        }
        
        return $results;
    }
    protected function resolveClass(ReflectionParameter $parameter): mixed
    {
        $type = $parameter->getType();
        
        // If the parameter doesn't have a type hint or is a built-in type, 
        // and is optional, use the default value
        if (!$type || $type->isBuiltin()) {
            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }
            
            throw new AppException(
                "Unresolvable dependency: $parameter in class {$parameter->getDeclaringClass()->getName()}"
            );
        }
        
        try {
            // Use the ReflectionNamedType API which works in PHP 7.4+
            $typeName = $type instanceof \ReflectionNamedType ? $type->getName() : (string)$type;
            return $this->make($typeName);
        } catch (AppException $e) {
            // If we can't resolve the class but the parameter is optional, 
            // use the default value
            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }
            
            throw $e;
        }
    }
    public function call($callback, array $parameters = []): mixed
    {
        if (is_array($callback)) {
            // If the first element is a string (class name), instantiate it
            if (is_string($callback[0])) {
                $callback[0] = $this->make($callback[0]);
            }
            
            $reflectionMethod = new \ReflectionMethod($callback[0], $callback[1]);
            $dependencies = $reflectionMethod->getParameters();
            
            $parameters = $this->resolveDependencies($dependencies, $parameters);
            
            return $reflectionMethod->invokeArgs($callback[0], $parameters);
        }
        
        if ($callback instanceof Closure) {
            $reflectionFunction = new \ReflectionFunction($callback);
            $dependencies = $reflectionFunction->getParameters();
            
            $parameters = $this->resolveDependencies($dependencies, $parameters);
            
            return $reflectionFunction->invokeArgs($parameters);
        }
        
        if (is_string($callback) && strpos($callback, '::') !== false) {
            list($class, $method) = explode('::', $callback);
            return $this->call([$class, $method], $parameters);
        }
        
        if (is_string($callback) && method_exists($callback, '__invoke')) {
            $instance = $this->make($callback);
            return $this->call([$instance, '__invoke'], $parameters);
        }
        
        throw new AppException("Invalid callback provided to container->call()");
    }
        public function resolve(string $abstract, array $params = [])
        {
            // Resolve alias if it exists
            $abstract = $this->aliases[$abstract] ?? $abstract;

            // Get the concrete implementation from bindings.
            // If not bound, we'll assume the abstract is a concrete class we can auto-wire.
            $concrete = $this->bindings[$abstract] ?? $abstract;

            if ($concrete instanceof Closure) {
                return $concrete($this, ...$params);
            }
            
            if (is_object($concrete)) {
                return $concrete;
            }

            // If it's a string (class name), build it.
            if (is_string($concrete)) {
                try {
                    return $this->build($concrete, $params);
                } catch (AppException $e) {
                    // Re-throw with more context
                    throw new AppException("Failed to resolve '{$abstract}'. " . $e->getMessage());
                }
            }
            
            throw new AppException("Unresolvable dependency type for '{$abstract}'");
        }

    public function build(string $concrete, array $params = []): object
    {
        try {
            $reflector = new ReflectionClass($concrete);
        } catch (ReflectionException $e) {
            throw new AppException("Class {$concrete} does not exist.");
        }


        if (!$reflector->isInstantiable()) {
            throw new AppException("Cannot instantiate {$concrete}");
        }

        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {
            return new $concrete;
        }

        $dependencies = $constructor->getParameters();
        $resolvedDependencies = [];

        foreach ($dependencies as $dependency) {
            // This allows us to pass named parameters to override DI
            if (isset($params[$dependency->getName()])) {
                $resolvedDependencies[] = $params[$dependency->getName()];
                continue;
            }

            $type = $dependency->getType();

            if (!$type instanceof \ReflectionNamedType || $type->isBuiltin()) {
                if ($dependency->isDefaultValueAvailable()) {
                    $resolvedDependencies[] = $dependency->getDefaultValue();
                    continue;
                }
                throw new AppException("Cannot resolve primitive parameter \${$dependency->getName()} in class {$concrete}");
            }

            // Recursively resolve the dependency class
            $resolvedDependencies[] = $this->make($type->getName());
        }

        return $reflector->newInstanceArgs($resolvedDependencies);
    }

    public function makeCallable($handler): callable {
        // If the handler is already callable, return it as-is
        if (is_callable($handler)) {
            return $handler;
        }

        // If the handler is an array (e.g., [ClassName, MethodName])
        if (is_array($handler) && count($handler) === 2) {
            [$class, $method] = $handler;

            // Resolve the class using the container
            $instance = $this->make($class);

            // Ensure the method exists
            if (!method_exists($instance, $method)) {
                throw new AppException("Method '{$method}' does not exist in class '{$class}'");
            }

            // Return a callable
            return function (...$params) use ($instance, $method) {
                return $instance->$method(...$params);
            };
        }

        // If the handler is a string, check if it's a global function
        if (is_string($handler) && function_exists($handler)) {
            return $handler;
        }

        throw new AppException('Invalid handler provided');
    }
}
?>