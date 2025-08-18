<?php

use HttpStack\Routing\Route;
use HttpStack\App\Controllers\Routes\PublicController;


$services = new Route("GET", "/services", ["ctrl.routes.services", "index"], "after");
$home = new Route("GET", "/home", ["ctrl.routes.home", "index"], "after");
$resume = new Route("GET", "/resume", ["ctrl.routes.resume", "index"], "after");
$stack = new Route("GET", "/stacks", ["ctrl.routes.stack", "index"], "after");
$contact = new Route("GET", "/contact", ["ctrl.routes.contact", "index"], "after");
$login = new Route("GET", "/login", ["ctrl.routes.login", "index"], "after");
$loggingIn = new Route("POST", "/login", ["ctrl.routes.login", "login"], "after");
return [$home, $resume, $contact, $stack, $services, $login, $loggingIn];