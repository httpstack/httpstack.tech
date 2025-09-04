<?php

namespace HttpStack\App\Controllers\Routes;

use HttpStack\Http\Request;
use HttpStack\Http\Response;
use HttpStack\Container\Container;
use HttpStack\Model\AbstractModel;
use HttpStack\App\Models\ViewModel;
use HttpStack\App\Views\View;
use HttpStack\Database\DBConnect;
use HttpStack\App\Datasources\DB\ActiveTable;

//use HttpStack\Template\Template;
class LoginController
{
    protected ViewModel $viewModel;
    public function __construct(){}
    public function index(Request $req, Response $res, Container $container, $matches)
    {
        $this->viewModel = $container->make(ViewModel::class, "public/login");
        //see if controller was submitted to
        $method = $req->getMethod();
        if ($method === "POST") {
            $this->login($req, $res, $container, $matches);
        }
        else{
            $this->form($req, $res, $container, $matches);
        }
        if (!$res->sent) {
            $res->send();
            $res->sent = true;
        }
    }
    public function form($req, $res, $container, $matches){
        $v = $container->make(View::class, "public/login/form.html");
        $v->model($this->viewModel);
        $v->render();
    }
public function login($req, $res, $container, $matches)
{
    $db = $container->make(DBConnect::class);
    $table = $container->make(ActiveTable::class, $db, "users");
    $params = explode("&", urldecode($req->getBody()));
    $data = [];
    foreach($params as $param) {
        list($key, $value) = explode("=", $param);
        $data[$key] = $value;
    }
    // Fetch user by email
    $user = $table->fetch(['email' => $data['email']]);
    if ($user && isset($user[0]['password'])) {
        if (password_verify($data['password'], $user[0]['password'])) {
            // Password matches, login success
            // ...set session, etc.
            echo 'good';
            $res->redirect('/dashboard');
        } else {
            // Invalid password
            $res->redirect('/login?error=invalid_credentials');
            echo 'bad';
        }
    } else {
        // User not found
        $res->redirect('/login?error=user_not_found');
    }
}
}