<?php

namespace HttpStack\App\Controllers\Routes;

use HttpStack\Http\Request;
use HttpStack\Http\Response;
use HttpStack\Container\Container;
use HttpStack\Model\AbstractModel;
use HttpStack\App\Models\ViewModel;
use HttpStack\App\Views\View;


//use HttpStack\Template\Template;
class ResumeController
{
    protected ViewModel $viewModel;
    public function __construct(){}
    public function index(Request $req, Response $res, Container $container, $matches)
    {
        $this->viewModel = $container->make(ViewModel::class, "public/resume");
        //bind the view data to the container so its available
        //within the ViewModel make
        //$res->setHeader("X-Controller-index", "resumeController::index");
        $this->resume($req, $res, $container, $matches);
    }
    public function resume($req, $res, $container, $matches)
    {
        $v = $container->make(View::class, "public/resume");
        $v->model($this->viewModel);
        $v->render();
        /**/
        //$res->setHeader("X-Controller-resume", "resumeController::resume");
        if (!$res->sent) {
            $res->send();
            $res->sent = true;
        }
    }
}