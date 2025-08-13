<?php
namespace HttpStack\App\Controllers\Routes;
use HttpStack\Http\Request;
use HttpStack\Http\Response;
use HttpStack\Container\Container;
use HttpStack\Model\AbstractModel;

//use HttpStack\Template\Template;
class StackController{


    public function __construct(){
        
        //i feel like here some initail view shit con be setup or pulled from the container
        //if not no biggie
    }
    public function index(Request $req, Response $res, Container $container, $matches){
        //any initial view setup can go here
        
        $this->stack($req, $res,$container,$matches);
    }
    public function stack($req, $res,$container,$matches){
        $v = $container->make("view", "public/stack");
        $v->render();
        if(!$res->sent){
            $res->send();
        }
    }

}
?>