<?php

namespace HttpStack\Controller;

use HttpStack\Http\Request;
use HttpStack\Http\Response;

abstract class BaseController
{
    abstract public function handle(Request $request, Response $response);
}
