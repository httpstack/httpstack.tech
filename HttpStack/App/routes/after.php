<?php

use HttpStack\Routing\Route;
use HttpStack\App\Controllers\Routes\PublicController;



return [
    new Route("GET", "/home", ["ctrl.routes.home", "index"], "after"), 
    new Route("GET", "/resume", ["ctrl.routes.resume", "index"], "after"), 
    new Route("GET", "/stacks", ["ctrl.routes.stack", "index"], "after"), 
    new Route("GET", "/contact", ["ctrl.routes.contact", "index"], "after"), 
    new Route("GET", "/login", ["ctrl.routes.login", "index"], "after"), 
    new Route("POST", "/login", ["ctrl.routes.login", "index"], "after"), 
    new Route("GET", "/services", ["ctrl.routes.services", "index"], "after"),
    new Route("GET", "/test", ["ctrl.routes.test", "index"], "after")
];