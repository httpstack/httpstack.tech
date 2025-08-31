<?php

namespace HttpStack\App;

use DBTable;
use HttpStack\Config\Config;
use HttpStack\Http\Request;
use HttpStack\Http\Response;
use HttpStack\IO\FileLoader;
use HttpStack\Routing\Route;
use HttpStack\App\Views\View;
use HttpStack\Routing\Router;
use HttpStack\Template\Template;
use HttpStack\Test\MyOtherClass;
use HttpStack\DataBase\DBConnect;
use HttpStack\Container\Container;
use HttpStack\DocEngine\DocEngine;
use HttpStack\App\Models\ViewModel;
use HttpStack\App\Models\TemplateModel;
use HttpStack\Datasource\FileDatasource;
use HttpStack\App\Datasources\FS\XmlFile;
use HttpStack\App\Datasources\DB\ActiveTable;
use HttpStack\App\Datasources\FS\JsonDirectory;
use HttpStack\Test\MyClass;

class App
{
    protected Container $container;
    protected Request $request;
    protected Response $response;
    protected ?Router $router;
    protected array $settings = [];
    protected FileLoader $fileLoader;
    public bool $debug = true;
    public function __construct()
    {
        $this->container = new Container();

        // Bind the essential instances FIRST
        $this->container->singleton(Container::class, $this->container);
        $this->container->singleton(App::class, $this);
        $this->container->singleton(self::class, $this);
        /**
         * This method will initialiizeall of the services. in order to build , resolve or make
         * any of the singletons or bindings you must tie the class name or the alias to an 
         * actula concrete implemtauton
         */
        $this->init();

        //with all of the ervices no boud to container you can make them
        $this->settings = $this->container->make('config')['app'];
        $this->request = $this->container->make(Request::class);
        $this->response = $this->container->make(Response::class);
        $this->router = $this->container->make(Router::class);
        $this->reportErrors();
        $GLOBALS["app"] = $this;

        
    }
    public function getRequest()
    {
        return $this->request;
    }
    public function getResponse()
    {
        return $this->response;
    }
    public function get(Route $route)
    {
        $this->router->after($route);
    }

    public function loadRoutes()
    {
        $routesDir = $this->settings['appPaths']['routesDir'];
        echo $routesDir;
        $configs = [];
        //LOOP OVER THE ROUTES DIRECTORY
        //AND GET ROUTE ARRAYS FROM THE FILES
        foreach (glob($routesDir . '/*.php') as $file) {
            //$file);
            $routes = include($file);
            //dd($routes);
            //LOOP OVER THE ROUTE ARRAYS AND REGISTER THWE ROUTES / MIDDLEWARES
            foreach ($routes as $route) {
                switch ($route->getType()) {
                    case "after":
                        $this->router->after($route);
                        break;

                    case "before":
                        $this->router->before($route);
                        break;
                }
            }
        }
    }

    public function getSettings()
    {
        return $this->settings;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function reportErrors()
    {
        if ($this->debug) {
            ini_set("display_errors", 1);
            ini_set("display_startup_errors", 1);
            error_reporting(32767); // E_ALL
        }
    }

    public function init()
    {
        // --- 1. Load Configurations and Aliases ---
        $this->container->singleton("config", function () {
            $cfg = new Config(APP_ROOT . "/config");
            return $cfg->getSettings();
        });

        // Load aliases from the config file into the container
        $aliases = $this->container->make('config')['aliases'] ?? [];
        foreach ($aliases as $alias => $fqn) {
            $this->container->alias($alias, $fqn);
        }

        // --- 2. Bind Core Services (as Singletons) ---
        $this->container->bind(Request::class, fn() => new Request());
        $this->container->bind(Response::class, fn() => new Response());
        $this->container->singleton(Router::class, fn() => new Router());

        $this->container->singleton(FileLoader::class, function (Container $c) {
            // We need the 'config' service to get the application settings
            $settings = $c->make('config')['app'];
            $fl = new FileLoader();
            // Loop over the appPaths and map them, just like in your original code
            if (!empty($settings['appPaths']) && is_array($settings['appPaths'])) {
                foreach ($settings['appPaths'] as $name => $path) {
                    $fl->mapDirectory($name, $path);
                }
            }
            return $fl;
        });
    }

    public function run(){
        $cfg = $this->container->make("config");
        $req = $this->container->make(Request::class);
        $res = $this->container->make(Response::class);
        $router = $this->container->make(Router::class);
        dd($router->getRoutes());
    }
}