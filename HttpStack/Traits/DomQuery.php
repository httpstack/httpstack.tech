<?php
namespace HttpStack\Traits;
use DOMDocument;
use DOMNode;
use DOMDocumentFragment;
use DOMElement;
trait DomQuery{
        // --- Constants for search types
    const DOMQ_BYID = 1;
    const DOMQ_BYCLASS = 2;
    const DOMQ_BYTAG = 3;
    const DOMQ_BYXPATH = 4;
    const DOMQ_BYCSS = 5;
        // --- Get method
        public function get(string $expr, int $type = self::DOMQ_BYCSS, bool $raw = false): mixed
        {
            $query = '';

            switch ($type) {
                case self::DOMQ_BYID:
                    $query = "//*[@id='$expr']";
                    break;
                case self::DOMQ_BYCLASS:
                    $query = "//*[contains(concat(' ', normalize-space(@class), ' '), ' $expr ')]";
                    break;
                case self::DOMQ_BYTAG:
                    $query = "//$expr";
                    break;
                case self::DOMQ_BYXPATH:
                    $query = $expr;
                    break;
                case self::DOMQ_BYCSS:
                default:
                    $query = $this->cssToXpath($expr);
                    break;
            }

            $nodes = $this->xpath->query($query);
            if (!$nodes) return null;

            if ($nodes->length == 1) {
                return $raw ? $this->dom->saveHTML($nodes->item(0)) : $nodes->item(0);
            }
            return $nodes;
        }

        // --- Set (append)
        public function set(DOMNode $parent, DOMNode $child): self
        {
            $parent->appendChild($child);
            return $this;
        }

        // --- Create new element
        public function element(string $tag, array $attributes = [], ?string $content = null): DOMElement
        {
            $element = $this->dom->createElement($tag);
            foreach ($attributes as $k => $v) {
                $element->setAttribute($k, $v);
            }
            if ($content) {
                $element->appendChild($this->dom->createTextNode($content));
            }
            return $element;
        }

        // --- Create fragment
        public function fragment(string $html): DOMDocumentFragment
        {
            $frag = $this->dom->createDocumentFragment();
            @$frag->appendXML($html);
            return $frag;
        }

        // --- Insert before
        public function before(DOMNode $refNode, DOMNode $newNode): self
        {
            $refNode->parentNode->insertBefore($newNode, $refNode);
            return $this;
        }

        // --- Replace node
        public function replace(DOMNode $oldNode, DOMNode $newNode): self
        {
            $oldNode->parentNode->replaceChild($newNode, $oldNode);
            return $this;
        }


        

        // --- Traverse with callback
        public function traverse(callable $callback, ?DOMNode $context = null): self
        {
            $context = $context ?? $this->dom->documentElement;
            $this->recursiveTraverse($callback, $context);
            return $this;
        }

        protected function recursiveTraverse(callable $callback, DOMNode $node)
        {
            $callback($node);
            foreach ($node->childNodes as $child) {
                $this->recursiveTraverse($callback, $child);
            }
        }

       
        // --- Data container functions

        // --- CSS to XPath (basic)
        public function cssToXpath(string $css): string
        {
            $css = trim($css);
            $css = str_replace(' ', '//', $css);
            $css = preg_replace('/#([\w\-]+)/', "*[@id='$1']", $css);
            $css = preg_replace('/\.([\w\-]+)/', "[contains(concat(' ', normalize-space(@class), ' '), ' $1 ')]", $css);
            $css = '//' . $css;
            return $css;
        }

        // --- Simple XPath to CSS (basic)
        public function xpathToCss(string $xpath): string
        {
            // NOTE: This will be rough/basic; real conversion is complex
            $css = str_replace('//', ' ', $xpath);
            $css = preg_replace('/\*\[@id=\'([^\']+)\'\]/', '#$1', $css);
            $css = preg_replace('/\[contains\(concat\(\' \', normalize-space\(@class\), \' \'\), \' ([^\']+) \'\\)\]/', '.$1', $css);
            return trim($css);
        }

        // --- Check if string is HTML
        public function isHtml(string $string): bool
        {
            return $string !== strip_tags($string);
        }
}
?>