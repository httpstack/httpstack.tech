<?php

namespace HttpStack\View;

use HttpStack\Http\Response;

class View
{
    protected $model;
    protected $response;

    public function __construct($model, Response $response)
    {
        $this->model = $model;
        $this->response = $response;
    }
    public function setBody($html)
    {
        $this->response->setBody($html);
    }
    public function send()
    {
        $this->response->send();
    }
}
