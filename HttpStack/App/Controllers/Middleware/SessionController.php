<?php

namespace HttpStack\App\Controllers\Middleware;

use HttpStack\Session\Session;

class SessionController
{
  protected $session;
  public function __construct()
  {
    // FOR READABILITY
    // SET WITH PROPERTY PROMOTION IN CONSTRUCT
    $this->session = new Session();
    if (session_status() !== PHP_SESSION_ACTIVE) {
      $this->session->start();
      $this->session->set("sessUser", "Guest");
    }
  }
  public function process($c, $matches)
  {
    //$res->setHeader("Middleware", "Session Started");
  }
}
