<?php

namespace HttpStack\Controller;

use HttpStack\Http\Request;
use HttpStack\Http\Response;
use HttpStack\View\View;
use HttpStack\Model\UserModel;

class MyCtrl extends BaseController
{
    public function handle(Request $request, Response $response)
    {
        $userId = $request->getParam('id');
        $model = new UserModel($userId);
        $view = new View($model, $response);
        $view->setBody('<h1>User Dashboard for ' . htmlspecialchars($model->getName()) . '</h1>');
        $view->send();
    }
}
