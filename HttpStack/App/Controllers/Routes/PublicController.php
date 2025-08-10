<?php
namespace HttpStack\App\Controllers\Routes;
use HttpStack\Http\Request;
use HttpStack\Http\Response;
use HttpStack\Container\Container;
use HttpStack\Model\AbstractModel;

//use HttpStack\Template\Template;
class PublicController{


    public function __construct(){
        
        //i feel like here some initail view shit con be setup or pulled from the container
        //if not no biggie
    }
    public function index(Request $req, Response $res, Container $container, $matches){
        
        $v = $container->make("view", "public/home");
        $v->render();
        if(!$res->sent){
            $res->send();
        }   
    }
    public function resume($req, $res,$container,$matches){
        $v = $container->make("view", "public/resume");
        $v->render();
        if(!$res->sent){
            $res->send();
        }
    }

    public function contact($req, $res,$container,$matches){
        $res->setContentType("text/html")->setBody("Contact Page");
        if(!$res->sent){
            $res->send();
        }
    }
    public function stacks($req, $res,$container,$matches){
        $v = $container->make("view", "public/stack");
        $v->render();
        if(!$res->sent){
            $res->send();
        }
    }    
    public function about($req, $res,$container,$matches){
        $res->setContentType("text/html")->setBody("about Page");
        if(!$res->sent){
            $res->send();
        }
    }
}
?>