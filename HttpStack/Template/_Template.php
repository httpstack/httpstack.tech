<?php 
namespace HttpStack\Template;

use \DOMNode;
use \DOMXPath;
use \Dom\XPath;
use \DOMElement;
use \DOMDocument;
use \DOMNodeList;
use HttpStack\IO\FileLoader;

define("ELEMENT_NODE", 1);
define("TEXT_NODE", 3);
define("ATTRIBUTE_NODE", 2);
/**
 * Splice Class
 * 
 * Wire templates together with this easy to use template engine.
 * Load your html files into the engine.
 * Perform HTML/DOM related tasks on 
 */

 class Template{
    public array $files = [];
    protected array $vars = [];
    protected array $assets = [];
    protected FileLoader $fileLoader;
    public string $defaultFileExt = "html";
    protected \DOMXPath $XPath;
    protected ?DOMDocument $dom;
    public function __construct(){
        $this->dom = new DOMDocument("1.0","utf-8");
        $this->fileLoader = box("fileLoader");
    }
    public function loadFile(string $nameSpace, string $baseFileName){
        //read a file contents into the $files at $nameSpace => $html
        $this->setFile($nameSpace, $this->fileLoader->readFile($baseFileName));

        return $this->files[$nameSpace];
        //dd($this->files);
    }
    public function getAssets():array{
        return $this->assets;
    }   
    public function getFile(string $nameSpace):string|array{
        return $nameSpace ? $this->files[$nameSpace] : $this->files;
    }
    public function setFile(string $nameSpace, string $html):self {
        $this->files[$nameSpace] = $html;
        return $this;
    }
    public function setTemplate(string $nameSpace):self{
        $binOptions = 4|32|64;//LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOBLANKS
        
        $this->dom->loadHTML($this->files[$nameSpace], $binOptions);
        $this->setXPath($this->dom);
        return $this;
    }
    
    protected function makeAsset(string $file):DOMElement{
        $ext = pathinfo($file, 4);//PATHINFO_EXTENSION
        $baseName = pathinfo($file, 2);//PATHINFO_BASENAME
        $filePath = $this->fileLoader->findFile($baseName, null, $ext);
        
        switch($ext){
            case "js":
                $element = $this->dom->createElement("script");
                $element->setAttribute("type", "text/javascript");
                $element->setAttribute("src", $filePath);
            break;

            case "css":
                $element = $this->dom->createElement("link");
                $element->setAttribute("type", "text/css");
                $element->setAttribute("href", $filePath);
            break;
        }
        return $element;
    }
    public function bindAssets(array $fileList):self{
        foreach($this->assets as $asset){
            echo "Binding asset: " . $asset->getAttribute("src") . "\n";
            if ($asset->nodeName === 'script') {
                // Append script to the body
                $this->dom->getElementsByTagName("body")->item(0)->appendChild($asset);
            } elseif ($asset->nodeName === 'link') {
                // Append link to the head
                $this->dom->getElementsByTagName("head")->item(0)->appendChild($asset);
            }
        }
        return $this;
    }
    public function loadAssets(array $fileList):void{
        $this->assets = [];
            foreach($fileList as $fileMeta){
                $file = $fileMeta['file'] ?? $fileMeta;
                $ext = pathinfo($file, PATHINFO_EXTENSION);
                if(!in_array($ext, ['js', 'css'])) continue;
                $this->assets[$file] = $this->makeAsset($file);
            }
    }
    public function getDom():DOMDocument{
        return $this->dom;
    }
    protected function setXPath(DOMDocument $dom = null):self{
        ($dom) ? 
            $this->XPath = new DOMXPath($dom) :
                $this->XPath = new DOMXPath(new DOMDocument());
        return $this;   
    }
    public function getXPath():DOMXPath{
        return $this->XPath;
    }
    public function setVar(string|array $key, string $value = ''):self{
        (is_array($key)) ? $this->vars = array_merge($this->vars, $key): $this->vars[$key] = $value;
        return $this;
    }
    public function getVar(string $key = ''): mixed {
        return $key === '' ? $this->vars : ($this->vars[$key] ?? null);
    }
 
    public function normalizeHtml(string $html): string
    {
        // Ensure doctype and <html> tags
        if (stripos($html, '<!doctype') === false) {
            $html = "<!DOCTYPE html>\n" . $html;
        }
        if (stripos($html, '<html') === false) {
            $html = "<html>\n" . $html;
        }
        if(strpos($html, '<head') === false && strpos($html, '<body') ===false){
            $html .= "\n<head>\n<title></title>\n</head>\n<body>";
        }
        if (stripos($html, '</html>') === false) {
            $html .= "\n</html>";
        }

        // Load into DOM to manipulate structure
        $dom = new DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $dom->loadHTML($html, 8192 | 4);// LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD

        // Ensure <head>
        $head = $dom->getElementsByTagName('head')->item(0);
        if (!$head) {
            $head = $dom->createElement('head');
            $dom->documentElement->insertBefore($head, $dom->documentElement->firstChild);
        }

        // Ensure <title>
        $titleTag = null;
        foreach ($head->getElementsByTagName('title') as $t) {
            $titleTag = $t;
            break;
        }
        if (!$titleTag) {
            $titleTag = $dom->createElement('title');
            $head->appendChild($titleTag);
        } else {
            $titleTag->textContent = "";
        }

        // Ensure <body>
        $body = $dom->getElementsByTagName('body')->item(0);
        if (!$body) {
            $body = $dom->createElement('body');
            $dom->documentElement->appendChild($body);
        }

        // Move stray nodes into <body>
        $htmlNode = $dom->documentElement;
        $toMove = [];
        foreach (iterator_to_array($htmlNode->childNodes) as $node) {
            if (
                $node->nodeType === ELEMENT_NODE &&
                !in_array($node->nodeName, ['head', 'body'])// ELEMENT_NODE
            ) {
                $toMove[] = $node;
            }
            if ($node->nodeType === TEXT_NODE && trim($node->nodeValue) !== '')// TEXT_NODE
            {
                $toMove[] = $node;
            }
        }
        foreach ($toMove as $node) {
            $body->appendChild($node);
        }

        libxml_clear_errors();
        return $dom->saveHTML();
    }
}
?>