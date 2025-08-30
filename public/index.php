<?php

//SWITCH THESE REQUIRES FOR ONLINE AND LOCAL CONFIGS
//require_once(__DIR__ . "/../HttpStack/App/init_prod.php");
require_once(__DIR__ . "/../HttpStack/App/init_dev.php");

use HttpStack\App\App;
$app = new App();

/* 
if (!$app->getRequest()->getUri()) {
    $app->getResponse()->redirect("/home");
};*/
$app->loadRoutes();
//[function(){return new class}]



$app->run();