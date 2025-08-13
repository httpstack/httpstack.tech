<?php
namespace HttpStack\App\Controllers\Routes;
use HttpStack\Http\Request;
use HttpStack\Http\Response;
use HttpStack\Container\Container;
use HttpStack\Model\AbstractModel;

//use HttpStack\Template\Template;
class ResumeController{


    public function __construct(){
        
        //i feel like here some initail view shit con be setup or pulled from the container
        //if not no biggie
    }
    public function index(Request $req, Response $res, Container $container, $matches){
        //any initial view setup can go here
        
        $this->resume($req, $res,$container,$matches);
    }
    public function resume($req, $res,$container,$matches){
        $v = $container->make("view", "public/resume");
        $v->render();
        if(!$res->sent){
            $res->send();
        }
    }

}
?>