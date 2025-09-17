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
use HttpStack\Credential\MySQLCredentials;
use HttpStack\Test\MyClass;

class App
{
    protected Container $container;
    protected Response $response;
    protected ?Router $router;
    protected array $settings = [];
    protected FileLoader $fileLoader;
    public bool $debug = true;
    public function __construct(protected Request $request)
    {

        $this->container = new Container();

        /**
         * This method will initialiizeall of the services. in order to build , resolve or make
         * any of the singletons or bindings you must tie the class name or the alias to an 
         * actula concrete implemtauton
         * i did add a static array to the container class
         */
        $this->init();

        //with all of the ervices no boud to container you can make them
        $this->settings = $this->container->make('config')['app'];
        $this->request = $this->container->make(Request::class);
        $this->response = $this->container->make(Response::class);
        $this->router = $this->container->make(Router::class);
        $this->fileLoader = $this->container->make(FileLoader::class);
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
    public function loadRoutes()
    {
        $routesDir = $this->settings['appPaths']['routesDir'];
        //echo $routesDir;
        $configs = [];
        //LOOP OVER THE ROUTES DIRECTORY
        //AND GET ROUTE ARRAYS FROM THE FILES
        foreach (glob($routesDir . '/*.php') as $file) {
            //$file);
            $routes = include($file);

            //LOOP OVER THE ROUTE ARRAYS AND REGISTER THE ROUTES / MIDDLEWARES
            foreach ($routes as $route) {
                $this->addRoute($route);
            }
        }
        //print($this->router->after[0]);
    }
    public function init()
    {
        // --- 1. Load Configurations and Aliases ---
        $this->container->singleton("config", function () {
            $cfg = new Config(APP_ROOT . "/config");
            return $cfg->getSettings();
        });


        //BIND MANY RESPONSE AND REQUEST
        $this->container->bind(Request::class, $this->request);
        $this->container->bind(Response::class, Response::class);


        // --- 2. Bind Core Services (as Singletons) ---

        $this->container->singleton(Router::class, fn() => new Router());
        $this->container->singleton(FileLoader::class, function () {
            // We need the 'config' service to get the application settings
            $fl = new FileLoader();
            // Loop over the appPaths and map them, just like in your original code
            if (!empty($this->settings['appPaths']) && is_array($this->settings['appPaths'])) {
                foreach ($this->settings['appPaths'] as $name => $path) {
                    $fl->mapDirectory($name, $path);
                }
            }
            return $fl;
        });

        //bind Data services
        $this->container->singleton(DBConnect::class, function () {
            return new DBConnect();
        });
        $this->container->bind(ActiveTable::class, function ($c, $db, $table) {
            return new ActiveTable($db, $table);
        });
        $this->container->bind(TemplateModel::class, function ($c, $ds) {
            return new TemplateModel($ds);
        });

        $this->container->bind(ViewModel::class, function ($c, $dataPath) {
            $fl = $c->make(FileLoader::class);
            $xmlFile = $fl->findFile($dataPath, null, "xml");
            $dataSource = $c->make(XmlFile::class, $xmlFile, true);
            return new ViewModel($dataSource);
        });
        $this->container->bind(XmlFile::class, function ($c, $filePath, $readOnly) {
            return new XmlFile($filePath, $readOnly);
        });
        $this->container->bind(JsonDirectory::class, function ($c, $dataDir, $readOnly) {
            return new JsonDirectory($dataDir, true);
        });
        $this->container->singleton(Template::class, function ($c, $baseLayout, $tm) {
            return new Template($baseLayout, $tm);
        });
        //$dataSource = $this->container->make(JsonDirectory::class, $dataDir, true);
        //BIND VIEW SERVICES

    }

    public function run()
    {
        $this->router->dispatch($this->container);
    }

    public function createRoute($method, $uri, $handlers, $type = "after")
    {
        return new Route($method, $uri, $handlers, $type);
    }
    protected function addRoute(Route $route)
    {
        $this->router->route($route);
    }
}
