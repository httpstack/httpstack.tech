<?php
if(!function_exists("app")){
    function app(){
        if(isset($GLOBALS["app"])){
            return $GLOBALS["app"];
        }
    }
}
if(!function_exists(function: "consoleLog")){
    function consoleLog(string $text):void{
        //echo "<script> console.log(\'$text\');</script>";
    }
}
if(!function_exists("box")){
    function box(string $make=null, array $params = []){
        // if u pass an abstract to box("myTool") it will return the resolved data
        // otherwise it just returns the container;
        return $make ? 
        // fetch container
        // make abstract and return result
        app()->getContainer()->make($make, $params):
        //fetch container
        app()->getContainer();
    }
}
if(!function_exists("appPath")){
    function appPath(string|null $path):string|array{
        if($path){
           return app()->getSettings()["appPaths"][$path];
        }
        return app()->getSettings()["appPaths"];
    }
}
if(!function_exists("config")){
    function config(string|null $key):string|array{
        if($key){
           return app()->getSettings()[$key];
        }
        return app()->getSettings();
    }
}
 function dd(mixed $data){
    $debug = app()->debug;

    if($debug){
        
        echo "<hr/><pre>";
        print_r($data);
        echo "</pre><hr/>";
    }
 }
?>