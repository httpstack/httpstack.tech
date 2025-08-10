<?php
namespace HttpStack\App\Controllers\Middleware;

use HttpStack\Http\Request; 
use HttpStack\Http\Response; 
use HttpStack\IO\FileLoader;
use HttpStack\App\Views\View;
use HttpStack\Template\Template;
use HttpStack\Container\Container;

class TemplateInit
{
    protected Template $template; 
    protected FileLoader $fileLoader; 

    public function __construct()
    {

    }

    /**
     * Processes the request and modifies the template.
     *
     * @param mixed $req The request object.
     * @param mixed $res The response object.
     * @param mixed $container The dependency injection container.
     * @return void
     */
    public function process(Request $req, Response $res, Container $container)
    {
        $v = new View($req, $res, $container);
        //register the view namespace agian, returning this view
        // that has the template object within it.
        $container->singleton("view", function(Container $c, string $view) use($v){
          //HERE the view will be re-called but getting the $v (view) we already
          //created on line 31
          //
          //  $v = new View($req, $res, $container);
          //  this line above will instantiate view that in its construct, will:
          //  1.) "make" template from container and bind it to the $v->template;
          //  2.) get the asset file list from filoader and bind them to the template
          //  3.) Tie the current Response, Container and Request to the $v (view)
          //  4.) THATS IT! the state of the template at this point is the base template
          //      waiting for the controllor to call the containers->make("view", "public/home")
          //        or whatever the view.
          
          //AT THIS POINT the template has been built and the $view is the folder/basename
          //to load
          $fl = $c->make(FileLoader::class);
          $viewPath = $fl->findFile($view, null, "html");
         // dd($viewPath);
          $v->loadView($view);
          return $v;
        });



        //$html = $template->render();

        //var_dump($template);
        //$html = $template->saveHTML();
        //oopen the base template and just put the html into the body
        //$res->setHeader("MW Set Base", "base");
        //$res->setBody();
    }//pub
}