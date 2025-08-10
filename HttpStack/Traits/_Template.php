<?php 
namespace HttpStack\Traits;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
trait Template{

    // --- Default delimiters for templating
    const TMPL8_MUSTACHE = ['{{', '}}'];
    const TMPL8_HBAR = ['{%', '%}'];
        // --- Inject file
        public function inject(string $file, DOMNode $target): self
        {
            //THE $target IS THE DOM NODE THAT WILL HOST 
            //THE INJECTED $html DOCUMENT AS A FRAGMENT   
            $html = file_get_contents($file);
            $frag = $this->createFragment($html);
            $target->appendChild($frag);
            return $this;
        }

        // --- Expression Replace
        public function replaceExpressions(array|string $delimiters, array $data, DOMNode $context = null): self
        {
            if (is_string($delimiters)) {
                $delimiters = match ($delimiters) {
                    'MUSTACHE' => self::TMPL8_MUSTACHE,
                    'HBAR' => self::TMPL8_HBAR,
                    default => ['{{', '}}']
                };
            }
        
            [$start, $end] = $delimiters;
            $html = $this->save($context, true);
        
            // --- First pass: Replace data
            foreach ($data as $key => $value) {
                $html = str_replace("$start$key$end", $value, $html);
            }
        
            // --- Second pass: Execute functions if matching
            $pattern = '/' . preg_quote($start) . '([\w\-]+)(\((.*?)\))?' . preg_quote($end) . '/';

            $html = preg_replace_callback($pattern, function ($matches) {
                $funcName = $matches[1];
                $paramString = $matches[3] ?? '';
            
                if (isset($this->functions[$funcName])) {
                    $params = [];
            
                    if (!empty($paramString)) {
                        // Split parameters by comma and trim whitespace
                        $params = array_map('trim', explode(',', $paramString));
                        
                        // Optional: Remove quotes if needed
                        $params = array_map(function($param) {
                            return trim($param, '\'"');
                        }, $params);
                    }
            
                    return call_user_func_array($this->functions[$funcName], $params);
                }
            
                return '';
            }, $html);
            
        
            // --- Reload new HTML
            $newDom = new DOMDocument();
            libxml_use_internal_errors(true);
            $newDom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();
        
            $this->dom = $newDom;
            $this->xpath = new DOMXPath($this->dom);
        
            return $this;
        }
        public function addData(string $key, mixed $value): self
        {
            $this->data[$key] = $value;
            return $this;
        }

        public function removeData(string $key): self
        {
            unset($this->data[$key]);
            return $this;
        }

        public function getData(string $key): mixed
        {
            return $this->data[$key] ?? null;
        }

        // --- Functions for template expressions
        public function addFunction(string $name, callable $fn): self
        {
            $this->functions[$name] = $fn;
            return $this;
        }

        public function callFunction(string $name, ...$args): mixed
        {
            return $this->functions[$name](...$args);
        }

        // --- Render method
        public function render(string|DOMNode $source, array $data, bool $raw = false): string|DOMDocument
        {
            if (is_string($source)) {
                $this->dom = new DOMDocument('1.0', 'UTF-8');
                libxml_use_internal_errors(true);
                $this->dom->loadHTML($source, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
                libxml_clear_errors();
                $this->xpath = new DOMXPath($this->dom);
            } else {
                $this->dom = $source->ownerDocument;
                $this->xpath = new DOMXPath($this->dom);
            }
        
            $this->replaceExpressions(self::TMPL8_MUSTACHE, $data);
        
            return $this->save(null, $raw);
        }
}
?>