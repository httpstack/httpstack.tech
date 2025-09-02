<?php

namespace HttpStack\App\Controllers\Routes;

use HttpStack\Http\Request;
use HttpStack\Http\Response;
use HttpStack\Container\Container;
use HttpStack\Model\AbstractModel;
use HttpStack\App\Models\ViewModel;
use HttpStack\App\Views\View;


//use HttpStack\Template\Template;
class StackController
{
    protected ViewModel $viewModel;
    public function __construct(){}
    public function index(Request $req, Response $res, Container $container, $matches)
    {
        $this->viewModel = $container->make(ViewModel::class, "public/stacks");
        //bind the view data to the container so its available
        //within the ViewModel make
        //$res->setHeader("X-Controller-index", "stackController::index");
        $this->stack($req, $res, $container, $matches);
    }
    public function stack($req, $res, $container, $matches)
    {
        $v = $container->make(View::class, "public/stacks");
        $v->model($this->viewModel);
        $v->render();
        /**/
        //$res->setHeader("X-Controller-stack", "stackController::stack");
        if (!$res->sent) {
            $res->send();
            $res->sent = true;
        }
    }
}