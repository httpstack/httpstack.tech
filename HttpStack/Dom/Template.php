<?php
namespace App\DOM;
use \DOMDocument;

use \DOMXPath;
use \Exception;
use \DOMNode;
use \DOMNodeList;
use \DOMText;
use \DOMElement;
use App\Traits\DomRoutines;
/**
 * DomTemplate
 *
 * A powerful template engine extending DOMDocument and DOMXPath.
 */
class Template extends DOMDocument
{
    use DomRoutines; // Include the DomHelper trait for additional DOM manipulation methods
     // helpers like createElement, appendAfter, find(), etc.

    protected DOMXPath $xpath;
    protected array $data = [];
    protected array $functions = [];
    protected ?string $assetsPath = null;
    protected bool $isBase = false;
    public ?Template $view = null;
    protected bool $debug = false;

    public function __construct(string $document, bool $isBase = false)
    {
        parent::__construct('1.0', 'UTF-8');
        $this->preserveWhiteSpace = false;
        $this->formatOutput = true;
        $this->isBase = $isBase;

        if ($this->isHTML($document)) {
            @$this->loadHTML($document, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOBLANKS);       
        }else if(is_file($document) && file_exists($document)) {
            @$this->loadHTMLFile($document, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOBLANKS);
        } else {
            throw new Exception("Invalid document provided. Must be a valid HTML string or file path.");
        }
        $this->initXpath();
    }
    protected function isHTML($document): string
    {
        return preg_match('/<.*?>.*<\/.*>/si', $document);
    }
    public function bindAssets($arrConfig){
        //load the required files first
        extract($arrConfig);  //uri, assets, required  
        foreach($required as $reqAsset){
            $this->bindAsset($uri . $reqAsset);
        }
        foreach($assets as $asset){
            $this->bindAsset($uri . $asset);
        }
        $this->initXpath();
    }
    public function setAssetsPath(string $path): void
    {
        $this->assetsPath = rtrim($path, '/') . '/';
    }

    public function addFunction(string $name, callable $fn): void
    {
        $this->functions[$name] = $fn;
    }

    public function setData(array $data): void
    {
        $this->data = array_merge($this->data,$data);
    }

    public function render(): string
    {
        $html = $this->saveHTML();
        
        // Process expressions (like function calls)
        $html = $this->processFunctions($html);
        $html = $this->processKeys($html);
        $html = $this->processConditionals($html);
        $html = $this->processLoops($html);
    
        // Reload rendered HTML into this DOM
        @$this->loadHTML($html,  LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOBLANKS);
        $this->initXpath();
        return $this->saveHTML();
    }
    public function setBase(bool $isBase): void
    {
        $this->isBase = $isBase;
    }
    public function debug(): void
    {
        echo "<pre>" . htmlentities($this->saveHTML()) . "</pre>";
    }

    public function loadView(string $document): Template
    {
        $this->view = new Template($document);
        return $this->view;
    }


    public function insertView(DOMElement $srcNode, $destNode): void
    {
        if (!$this->view) {
            throw new Exception("No view loaded. Call loadView() first.");
        }
    
        if (!$srcNode || !$destNode) {
            throw new Exception("Cannot find source or destination elements.");
        }
    
        if (!$srcNode instanceof DOMNode || !$destNode instanceof DOMNode) {
            throw new Exception("Source and destination must be DOM nodes.");
        }
    
        // Import the source node into the current document
        $importedNode = $this->importNode($srcNode, true);
    
        // Append the imported node directly to the destination node
        $this->_append($destNode, $importedNode);
    
        // Move assets from view to base template
        $this->transferAssets($this->view);
    }

    protected function transferAssets(Template $view): void
    {
        $scripts = $view->xpath->query("//script[@src]");
        foreach ($scripts as $script) {
            if ($script instanceof DOMElement) {
                $newScript = $this->createElement('script');
                $newScript->setAttribute('src', $this->fullAsset($script->getAttribute('src')));
                $this->_findOne('body')->appendChild($newScript);
            }
        }
    
        $styles = $view->xpath->query("//link[@rel='stylesheet']");
        foreach ($styles as $link) {
            if ($link instanceof DOMElement) {
                $newLink = $this->createElement('link');
                $newLink->setAttribute('rel', 'stylesheet');
                $newLink->setAttribute('href', $this->fullAsset($link->getAttribute('href')));
                $this->_findOne('head')->appendChild($newLink);
            }
        }
    }

    protected function fullAsset(string $src): string
    {
        if ($this->assetsPath && !str_starts_with($src, 'http')) {
            return $this->assetsPath . ltrim($src, '/');
        }
        return $src;
    }

    protected function processKeys(string $html): string
    {
        foreach ($this->data as $key => $value) {
            // Check if the value is an array
            if (is_array($value)) {
                // Convert the array to a string (you can use json_encode, implode, etc.)
                $value = json_encode($value); // Example: Convert the array to a JSON string
            }
    
            // Ensure that the value is a string before replacing
            $html = str_replace('{{ ' . $key . ' }}', htmlspecialchars((string)$value), $html);
        }
        return $html;
    }

    private function processFunctions(string $html): string
    {
        // Regular expression to find function calls like {{ functionName(param1, param2) }}
        $pattern = '/\{\{\s*(\w+)\s*\(([^}]*)\)\s*\}\}/';
    
        return preg_replace_callback($pattern, function($matches) {
            $functionName = $matches[1]; // Function name (e.g., `myFunction`)
            $params = explode(',', $matches[2]); // Parameters passed (e.g., `param1`, `param2`)
    
            // Trim spaces for each param
            $params = array_map('trim', $params);
    
            // Check if the function is defined
            if (isset($this->functions[$functionName])) {
                // Call the function with the parameters
                return call_user_func_array($this->functions[$functionName], $params);
            }
    
            // If function is not found, return the original placeholder
            return $matches[0];
        }, $html);
    }

    protected function processConditionals(string $html): string
    {
        return preg_replace_callback('/\{\{ if (.+?) \}\}(.+?)\{\{ endif \}\}/s', function ($matches) {
            $key = trim($matches[1]);
            $content = $matches[2];
            return !empty($this->data[$key]) ? $content : '';
        }, $html);
    }

    protected function processLoops(string $html): string
    {
        return preg_replace_callback('/\{\{ foreach (.+?) as (.+?) \}\}(.+?)\{\{ endforeach \}\}/s', function ($matches) {
            $listName = trim($matches[1]);
            $itemName = trim($matches[2]);
            $template = $matches[3];

            if (empty($this->data[$listName]) || !is_array($this->data[$listName])) {
                return '';
            }

            $result = '';
            foreach ($this->data[$listName] as $item) {
                $chunk = str_replace('{{ ' . $itemName . ' }}', htmlspecialchars((string)$item), $template);
                $result .= $chunk;
            }
            return $result;
        }, $html);
    }
}
