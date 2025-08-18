<?php
return [
    "ctrl.mw.session" => \HttpStack\App\Controllers\Middleware\SessionController::class,
    "ctrl.mw.template" => \HttpStack\App\Controllers\Middleware\TemplateInit::class,
    "ctrl.routes.public" => \HttpStack\App\Controllers\Routes\PublicController::class,
    "ctrl.routes.home" => \HttpStack\App\Controllers\Routes\HomeController::class,
    "ctrl.routes.contact" => \HttpStack\App\Controllers\Routes\ContactController::class,
    "ctrl.routes.services" => \HttpStack\App\Controllers\Routes\ServicesController::class,
    "ctrl.routes.resume" => \HttpStack\App\Controllers\Routes\ResumeController::class,
    "ctrl.routes.stack" => \HttpStack\App\Controllers\Routes\StackController::class,
    "ctrl.routes.login" => \HttpStack\App\Controllers\Routes\LoginController::class
];
