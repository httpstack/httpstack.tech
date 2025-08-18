<?php

namespace HttpStack\App\Controllers\Routes;

use HttpStack\Http\Request;
use HttpStack\Http\Response;
use HttpStack\Container\Container;
use HttpStack\Model\AbstractModel;
use HttpStack\App\Models\ViewModel;

//use HttpStack\Template\Template;
class LoginController
{

    public function __construct()
    {
        app()->getContainer()->bind("viewData", function () {
            return "public/login";
        });
    }
    public function index(Request $req, Response $res, Container $container, $matches)
    {
        //bind the view data to the container so its available
        //within the ViewModel make
        $container->bind("viewData", function () {
            return "public/login";
        });
        $this->login($req, $res, $container, $matches);
    }
    public function login($req, $res, $container, $matches)
    {
        $m = $container->make(ViewModel::class);

        $v = $container->make("view", "public/login");
        $v->model($m);

        $v->render();
        if (!$res->sent) {
            $res->send();
        }
    }
}
