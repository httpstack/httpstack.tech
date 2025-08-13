<?php

use HttpStack\Routing\Route;
use HttpStack\App\Controllers\Routes\PublicController;

$fncCtrlServices = box()->makeCallable(["HttpStack\App\Controllers\Routes\ServicesController", "index"]);
$fncCtrlHome = box()->makeCallable(["HttpStack\App\Controllers\Routes\HomeController", "index"]);
$fncCtrlResume = box()->makeCallable(["HttpStack\App\Controllers\Routes\ResumeController", "index"]);
$fncCtrlStack = box()->makeCallable(["HttpStack\App\Controllers\Routes\StackController", "index"]);
$fncCtrlContact = box()->makeCallable(["HttpStack\App\Controllers\Routes\ContactController", "index"]);

$services = new Route("GET", "/services", $fncCtrlServices, "after");
$home = new Route("GET", "/home", $fncCtrlHome, "after");
$resume = new Route("GET", "/resume", $fncCtrlResume, "after");
$stack = new Route("GET", "/stacks", $fncCtrlStack, "after");
$contact = new Route("GET", "/contact", $fncCtrlContact, "after");
return [$home, $resume, $contact, $stack, $services];
