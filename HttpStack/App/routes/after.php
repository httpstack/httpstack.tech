<?php
use HttpStack\App\Controllers\Routes\PublicController;
use HttpStack\Routing\Route;

$root = new Route("GET","/", box()->makeCallable(["HttpStack\App\Controllers\Routes\PublicController", "index"]),"after");
$home = new Route("GET","/home", box()->makeCallable(["HttpStack\App\Controllers\Routes\PublicController", "index"]),"after");
$resume = new Route("GET","/resume", box()->makeCallable(["HttpStack\App\Controllers\Routes\PublicController", "resume"]),"after");
$contact = new Route("GET", "/stacks", box()->makeCallable(['HttpStack\App\Controllers\Routes\PublicController', "stacks"]), "after");
$about = new Route("GET", "/about", box()->makeCallable(["HttpStack\App\Controllers\Routes\PublicController", "about"]), "after");
return [$home,$resume,$contact,$about];
?>