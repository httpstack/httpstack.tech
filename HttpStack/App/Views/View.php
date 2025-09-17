<?php

namespace HttpStack\App\Views;


use \DocumentFragment;
use HttpStack\Http\Request;
use HttpStack\Http\Response;
use HttpStack\IO\FileLoader;
use HttpStack\Template\Template;
use HttpStack\Container\Container;
use HttpStack\Model\AbstractModel;
use HttpStack\App\Models\ViewModel;
use HttpStack\App\Models\TemplateModel;
use HttpStack\App\Datasources\FS\JsonDirectory;

class View
{

    protected Template $template;
    protected Response $response;
    protected Request $request;
    protected string $view;
    protected Container $container;
    protected array $settings;
    protected FileLoader $fl;



    public function __construct(protected ?AbstractModel $viewModel)
    {
        if ($viewModel) {
            $this->model($viewModel);
        }
        $this->container = $container;
        $this->response = $res;
        $this->request = $req;
        $this->settings = $container->make('config')['app']['template'];
        $this->fl = $container->make(FileLoader::class);
        $this->init();
    }
    protected function init()
    {
        //Get a Datasource for the DataModel
        $dataDir = $this->settings['dataDir'];
        $dataSource = $this->container->make(JsonDirectory::class, $dataDir, true);
        $dataModel = $this->container->make(TemplateModel::class, $dataSource);

        //Make the Template from the container
        $basePath = $this->settings['basePath'];
        $this->template = $this->container->make(Template::class, $basePath, $dataModel);
        //Bind and preload the asssets based on the types of assets 
        //wanted from the file loader
        $assetTypes = $this->settings['assetTypes']; ///config/app.php to modify asset types
        $assets = $this->fl->findFilesByExtension($assetTypes);
        $this->template->bindAssets($assets);

        //$this->response->setBody($thistemplate->saveHtml  ());
    }
    public function loadView(string $view)
    {
        $fl = $this->container->make(FileLoader::class);

        $fileContent = $fl->readFile($view);

        $viewNode = $this->toDomObject($fileContent);
        $frag = $this->template->createDocumentFragment();
        foreach (iterator_to_array($viewNode->childNodes) as $childNode) {
            $importedNode = $this->template->importNode($childNode, true); // true for deep clone
            $frag->appendChild($importedNode);
        }
        $targetNode = $this->template->getMap()->query('//*[@data-key="view"]')->item(0);
        $targetNode->appendChild($frag);
        $this->template->setMap();
    }
    protected function toDomObject($str)
    {
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        @$dom->loadHTML($str, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOWARNING);
        libxml_clear_errors();
        return $dom;
    }
    public function model(ViewModel $dataModel)
    {
        $this->viewModel = $dataModel;
        $this->template->setVars($dataModel->getAll());
    }
    public function render()
    {
        $html = $this->template->render();
        $this->response->setContentType("text/html")->setBody($html);
    }
    public function getView()
    {
        return $this->view;
    }
    public function getTemplate()
    {
        return $this->template;
    }
    public function importView(string $filePath)
    {
        $this->template->importView($filePath);
    }
}
