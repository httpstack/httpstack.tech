<?php
class App
{
    public function __construct(Request $req)
    {
        $this->request = $req;
        $this->container = new Container();
        $this->init();
        //at this point all of the services are bound so u can get the srtings
        $this->settings = $this->container->make("config")['app'];
        $this->reportErrors();
        $GLOBALS['app'] = $this;
    }
}
