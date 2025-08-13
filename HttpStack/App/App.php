<?php

namespace HttpStack\App;

use DBTable;
use HttpStack\Http\Request;
use HttpStack\Http\Response;
use HttpStack\IO\FileLoader;
use HttpStack\Routing\Route;
use HttpStack\App\Views\View;
use HttpStack\Routing\Router;
use HttpStack\Template\Template;
use HttpStack\DataBase\DBConnect;
use HttpStack\Container\Container;
use HttpStack\DocEngine\DocEngine;
use HttpStack\App\Models\PageModel;
use HttpStack\App\Models\TemplateModel;
use HttpStack\Datasource\FileDatasource;
use HttpStack\App\Datasources\FS\XmlFile;
use HttpStack\App\Datasources\DB\ActiveTable;
use HttpStack\App\Datasources\FS\JsonDirectory;

class App
{
    protected Container $container;
    protected Request $request;
    protected Response $response;
    protected Router $router;
    protected array $settings = [];
    protected FileLoader $fileLoader;
    public bool $debug = true;
    public function __construct(string $appPath = "/var/www/html/App/app")
    {
        $this->container = new Container();

        // Bind the essential instances FIRST
        $this->container->singleton(Container::class, $this->container);
        $this->container->singleton(self::class, $this);
        $this->container->singleton(App::class, $this);
        $this->container->bind(View::class, function (Container $c, $req, $res) {
            return new View($c, $req, $res);
        });
        // INIT will bind all other services to the container
        $this->init();

        // Now that config is loaded, get settings
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
        $this->container->singleton('config', function () {
            $configDir = APP_ROOT . "/config";
            $configs = [];
            foreach (glob($configDir . '/*.php') as $file) {
                $key = basename($file, '.php');
                $configs[$key] = include $file;
            }
            return $configs;
        });

        // Load aliases from the config file into the container
        $aliases = $this->container->make('config')['aliases'] ?? [];
        foreach ($aliases as $alias => $fqn) {
            $this->container->alias($alias, $fqn);
        }

        // --- 2. Bind Core Services (as Singletons) ---
        $this->container->singleton(Request::class, fn() => new Request());
        $this->container->singleton(Response::class, fn() => new Response());
        $this->container->singleton(Router::class, fn() => new Router());
        $this->container->singleton(DBConnect::class, fn() => new DBConnect());

        // --- 3. Bind Models and Views (use `bind` for non-singletons) ---

        // Use `bind` because a PageModel is specific to a request
        $this->container->bind(PageModel::class, function (Container $c) {
            // The container will automatically create the DBConnect instance for you!
            $dbDatasource = new ActiveTable($c->make(DBConnect::class), "pages", false);
            return new PageModel($dbDatasource);
        });

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
        $this->container->singleton("template", function () {
            $tm = $this->container->make(TemplateModel::class);
            $fl = $this->container->make(FileLoader::class);
            $baseTemplatePath = $fl->findFile("base.html", null, "html");
            return new Template($baseTemplatePath, $tm);
        });
        // Use a singleton for the TemplateModel if its data is truly global
        $this->container->singleton(TemplateModel::class, function () {
            $dataDirectory = appPath("dataDir") . "/template";
            $dataSource = new JsonDirectory($dataDirectory, true);

            $t = new TemplateModel($dataSource);
            return $t;
        });
        $this->container->singleton(PageModel::class, function () {
            $dataSource = new XmlFile("/var/www/html/HttpStack/App/data/routes/home.xml", true);
            $pm = new PageModel($dataSource, []);
            return $pm;
        });
    }
    public function run()
    {
        $this->router->dispatch($this->request, $this->response, $this->container);
    }
}
