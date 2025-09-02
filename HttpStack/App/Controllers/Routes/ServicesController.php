<?php

namespace HttpStack\App\Controllers\Routes;

use HttpStack\Http\Request;
use HttpStack\Http\Response;
use HttpStack\Container\Container;
use HttpStack\Model\AbstractModel;
use HttpStack\App\Models\ViewModel;
use HttpStack\App\Views\View;


//use HttpStack\Template\Template;
class ServicesController
{
    protected ViewModel $viewModel;
    public function __construct(){}
    public function index(Request $req, Response $res, Container $container, $matches)
    {
        $this->viewModel = $container->make(ViewModel::class, "public/services");
        //bind the view data to the container so its available
        //within the ViewModel make
        //$res->setHeader("X-Controller-index", "servicesController::index");
        $this->services($req, $res, $container, $matches);
    }
    public function services($req, $res, $container, $matches)
    {
        
        $v = $container->make(View::class, "public/services");
        $v->model($this->viewModel);
        $v->render();
        /**/
        //$res->setHeader("X-Controller-services", "servicesController::services");
        if (!$res->sent) {
            $res->send();
            $res->sent = true;
        }
    }
}