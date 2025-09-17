<?php

namespace HttpStack\App\Controllers\Routes;

use HttpStack\Http\Request;
use HttpStack\Http\Response;
use HttpStack\Container\Container;
use HttpStack\Model\AbstractModel;
use HttpStack\App\Models\ViewModel;
use HttpStack\App\Views\View;

use HttpStack\Test\MyClass;

//use HttpStack\Template\Template;
class HomeController
{
    public function __construct()
    {
        app()->getContainer()->bind("viewData", function () {
            return "public/home";
        });
    }
    public function index(Container $c, $matches)
    {
        //bind the view data to the container so its available
        //within the ViewModel make
        $c->bind("viewData", function () {
            return "public/home";
        });
        $this->home($c, $matches);
    }
    public function home($c, $matches)
    {
        /* 
        //$v = $container->make(View::class, "public/home");
        $v = $c->make(View::class);
        //$m = $container->make(ViewModel::class, $container, "public/home");
        //$v->model($m);

        $v->render();
        if (!$res->sent) {
            $res->send();
        }
            */
    }
}
