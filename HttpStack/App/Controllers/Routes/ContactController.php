<?php

namespace HttpStack\App\Controllers\Routes;

use HttpStack\Http\Request;
use HttpStack\Http\Response;
use HttpStack\Container\Container;
use HttpStack\Model\AbstractModel;
use HttpStack\App\Models\ViewModel;

//use HttpStack\Template\Template;
class ContactController
{
    public function __construct()
    {
        //i feel like here some initail view shit con be setup or pulled from the container
        //if not no biggie
        app()->getContainer()->bind("viewData", function () {
            return "public/contact";
        });
    }
    public function index(Request $req, Response $res, Container $container, $matches)
    {
        //bind the view data to the container so its available
        //within the ViewModel make
        $container->bind("viewData", function () {
            return "public/contact";
        });
        $this->contact($req, $res, $container, $matches);
    }
    public function contact($req, $res, $container, $matches)
    {
        $m = $container->make(ViewModel::class);
        $v = $container->make("view", "public/contact");

        $v->model($m);
        $v->render();
        if (!$res->sent) {
            $res->send();
        }
    }
}
