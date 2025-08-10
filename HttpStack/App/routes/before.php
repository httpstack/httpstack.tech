<?php
use HttpStack\Routing\Route;
use HttpStack\App\Controllers\Middleware\TemplateInit;
use HttpStack\App\Controllers\Middleware\SessionController;

$global = new Route("GET",".*",[new SessionController,'process'], "before");
$global->addHandler(box()->makeCallable([TemplateInit::class, 'process']));
return [$global];
?>