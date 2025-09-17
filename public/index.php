<?php

//SWITCH THESE REQUIRES FOR ONLINE AND LOCAL CONFIGS
//require_once(__DIR__ . "/../HttpStack/App/init_prod.php");
require_once(__DIR__ . "/../HttpStack/App/init_dev.php");

use HttpStack\Http\Request;
use HttpStack\App\App;

$req = new Request();
$app = new App($req);

/*
if (!$app->getRequest()->getUri()) {
    $app->getResponse()->redirect("/home");
}; */
$app->loadRoutes();
//[function(){return new class}]



$app->run();
