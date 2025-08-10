<?php
return [
    "ctrl.mw.session" => \HttpStack\App\Controllers\Middleware\SessionController::class,
    "ctrl.mw.template" => \HttpStack\App\Controllers\Middleware\TemplateInit::class,
    "ctrl.routes.public" => \HttpStack\App\Controllers\Routes\PublicController::class
];
?>
];