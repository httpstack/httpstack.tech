<?php

namespace HttpStack\Template;

use \DOMPXath;
use DOMDocument;
use HttpStack\IO\FileLoader;
use HttpStack\Container\Container;
use HttpStack\Traits\ProcessTemplate;
use HttpStack\App\Models\TemplateModel;

class Template extends DOMDocument
{
    use ProcessTemplate;
    protected \DOMXPath $map;
    protected array $variables = [];
    protected array $functions = [];
    protected Container $container;
    protected TemplateModel $model;

    public function __construct(string $baseTemplatePath,  TemplateModel $tm)
    {

        //$baseTemplatePath = app()->getSettings()['template']['baseTemplatePath'];

        @$this->loadHTMLFile($baseTemplatePath, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $this->setMap();
        $this->setVars($tm->getAll());
    }
    public function addFunction(string $name, callable $function): void
    {
        if (!isset($this->functions[$name])) {
            $this->functions[$name] = $function;
        } else {
            throw new \Exception("Function {$name} already exists.");
        }
    }
    public function setVars(array $vars)
    {
        $this->variables = array_merge($this->variables, $vars);
    }
    public function setVar(string $key, mixed $value): void
    {
        $this->variables[$key] = $value;
    }
    public function getVars()
    {
        return $this->variables;
    }
    public function bindAssets(array $assets)
    {

        $head = $this->map->query("//head")[0];
        $body = $this->map->query("//body")[0];

        $imagePreloadUrls = [];
        $required = ["jquery.js"];
        $deffered = ["app.js"];
        foreach ($assets as $asset) {
            $strType = pathinfo($asset, PATHINFO_EXTENSION);
            $filename = pathinfo($asset, PATHINFO_BASENAME);
            $required = str_contains($filename, "required") || str_contains($filename, "cmp-");
            $src = str_replace(DOC_ROOT, "", $asset);
            $imagesToPreload = [];
            switch ($strType) {
                case "js":
                    $script = $this->createElement("script");
                    $script->setAttribute("type", "text/javascript");
                    if (str_contains($filename, "babel")) {
                        $script->setAttribute("type", "text/babel");
                    }
                    $script->setAttribute("src", $src);
                    if ($required) {
                        $head->appendChild($script);
                    } else {
                        //$script->setAttribute("defer", "defer");
                        $body->appendChild($script);
                    }
                    break;

                case "jsx":
                    $script = $this->createElement("script");
                    $script->setAttribute("type", "text/babel");
                    $script->setAttribute("src", $src);
                    $head->appendChild($script);
                    break;
                case "css":
                    $link = $this->createElement("link");
                    $link->setAttribute('type', 'text/css');
                    $link->setAttribute('rel', 'stylesheet');
                    $link->setAttribute('href', $src);
                    $head->appendChild($link);
                    break;

                case "woff":
                case "woff2":
                case "otf":
                case "ttf":
                    $link = $this->createElement("link");
                    $link->setAttribute("rel", "preload");
                    $link->setAttribute("href", $src);
                    $link->setAttribute("as", "font");
                    $link->setAttribute("type", "font/{$strType}");
                    $link->setAttribute("crossorigin", "anonymous"); // Required for font preloading
                    $head->appendChild($link);
                    break;

                case "jpg":
                    $imagesToPreload[] = $src;
                    break;
            }
        } //foreach
        if (!empty($imagesToPreload)) {
            $preloaderScriptContent = `
                (function() {
                    var imagesToPreload = " . json_encode($imagesToPreload) . ";
                    imagesToPreload.forEach(function(url) {
                        var img = new Image();
                        img.src = url;
                        // Optional: add event listeners for loaded/error if needed
                        img.onload = function() { console.log('Preloaded: ' + url); };
                        // img.onerror = function() { console.error('Failed to preload: ' + url); };
                    });
                })();
            `;

            $script = $this->createElement("script");
            $script->nodeValue = $preloaderScriptContent;
            $body->appendChild($script);
        }
        $this->setMap();
    }
    public function insertView($viewFragment)
    {
        $target = $this->map->query("//*[@data-key='view']");
        $target->append($viewFragment);
    }
    public function setMap()
    {
        $this->map = new \DOMXPath($this);
    }
    public function getMap()
    {
        return $this->map;
    }
}