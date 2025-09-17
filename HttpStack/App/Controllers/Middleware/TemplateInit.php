<?php

namespace HttpStack\App\Controllers\Middleware;

use HttpStack\Http\Request;
use HttpStack\Http\Response;
use HttpStack\IO\FileLoader;
use HttpStack\App\Views\View;
use HttpStack\Template\Template;
use HttpStack\Container\Container;
use HttpStack\App\Models\ViewModel;
use HttpStack\App\Models\TemplateModel;
use HttpStack\App\Datasources\FS\JsonDirectory;

class TemplateInit
{
  protected Template $template;
  protected FileLoader $fileLoader;

  public function __construct() {}

  /**
   * Processes the request and modifies the template.
   *
   * @param mixed $req The request object.
   * @param mixed $res The response object.
   * @param mixed $container The dependency injection container.
   * @return void
   */
  public function process(Container $container)
  {


    //$pm = $container->make(ViewModel::class);

    //var_dump($pm);
    //$v = $container->make(View::class,  $req, $res);
    //register the view namespace agian, returning this view
    // that has the template object within it.
    /*
    $container->bind(View::class, function (Container $c, string $view) use ($v) {
      $fl = $c->make(FileLoader::class);
      $viewPath = $fl->findFile($view, null, "html");
      // dd($viewPath);
      $v->loadView($view);
      return $v;
    });
    */
  }
}
