<?php

namespace HttpStack\App\Controllers\Routes;

use HttpStack\Http\Request;
use HttpStack\Http\Response;
use HttpStack\Container\Container;
use HttpStack\Model\AbstractModel;
use HttpStack\App\Models\ViewModel;

//use HttpStack\Template\Template;
class ResumeController
{


    public function __construct()
    {
        app()->getContainer()->bind("viewData", function () {
            return "public/resume";
        });
        //i feel like here some initail view shit con be setup or pulled from the container
        //if not no biggie
    }
    public function index(Request $req, Response $res, Container $container, $matches)
    {
        //any initial view setup can go here
        $container->bind("viewData", function () {
            return "public/resume";
        });
        $this->resume($req, $res, $container, $matches);
    }
    public function resume($req, $res, $container, $matches)
    {
        $m = $container->make(ViewModel::class);
        $v = $container->make("view", "public/resume");

        $v->model($m);
        $v->render();
        if (!$res->sent) {
            $res->send();
        }
    }
}
