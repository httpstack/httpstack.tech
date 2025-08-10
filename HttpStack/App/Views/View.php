<?php
namespace HttpStack\App\Views;


use \DocumentFragment;
use HttpStack\Http\Request;
use HttpStack\Http\Response;
use HttpStack\IO\FileLoader;
use HttpStack\Template\Template;
use HttpStack\Container\Container;
use HttpStack\Model\AbstractModel;
use HttpStack\App\Models\TemplateModel;

class View {

    protected Template $template;
    protected Response $response;
    protected string $view;
    protected Container $container;

    public function __construct(Request $req, Response $res, Container $container){
        // make sure templateModel is foing the logic of preparing the model since 
        // it is the concrete model 
        //box(abstract) is a helper for $container->make(abstract);
        $this->container = $container;
        $this->response = $res;
        $assetTypes = ["js", "css", "woff", "woff2", "otf", "ttf", "jpg", "jsx"];
        $this->template = $container->make("template");
        $fl = $container->make(FileLoader::class);
        $assets = $fl->findFilesByExtension($assetTypes, null);
        $this->template->bindAssets($assets);
        /*
        //example of defining a function with parameter
        $this->template->define("myFunc", function($myparam){
            return $myparam;
        });
        */
    }
    public function loadView(string $filePath){
        $fl = $this->container->make(FileLoader::class);
        $fileContent = $fl->readFile($filePath);
        $viewNode = $this->toDomObject($fileContent);
        $frag = $this->template->createDocumentFragment();
        foreach (iterator_to_array($viewNode->childNodes) as $childNode) {
            $importedNode = $this->template->importNode($childNode, true); // true for deep clone
            $frag->appendChild($importedNode);
        }
        $targetNode = $this->template->getMap()->query('//*[@data-key="view"]')->item(0);
        $targetNode->appendChild($frag);
    }
    protected function toDomObject($str){
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        @$dom->loadHTML($str, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOWARNING);
        libxml_clear_errors();
        return $dom;
    }

    public function render(){
        $html = $this->template->render();
        $this->response->setContentType("text/html")->setBody($html);
    }
    public function getView(){
        return $this->view;
    }
    public function getTemplate(){
        return $this->template;
    }
    public function importView(string $filePath){
        $this->template->importView($filePath);
    }
}
?>