<?php

namespace HttpStack\App\Controllers\Routes;

use HttpStack\Http\Request;
use HttpStack\Http\Response;
use HttpStack\Container\Container;
use HttpStack\Model\AbstractModel;
use HttpStack\App\Models\ViewModel;

//use HttpStack\Template\Template;
class ServicesController
{
    public function __construct()
    {
        app()->getContainer()->bind("viewData", function () {
            return "public/services";
        });
    }
    public function index(Request $req, Response $res, Container $container, $matches)
    {
        //bind the view data to the container so its available
        //within the ViewModel make
        $container->bind("viewData", function () {
            return "public/services";
        });
        $this->services($req, $res, $container, $matches);
    }
    public function services($req, $res, $container, $matches)
    {
        $m = $container->make(ViewModel::class);

        $v = $container->make("view", "public/services");
        $v->model($m);

        $v->render();
        if (!$res->sent) {
            $res->send();
            dd($req->getUriParts());
        }
    }
}
