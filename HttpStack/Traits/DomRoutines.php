<?php
namespace App\Traits;
use \DOMDocument;
use \DOMXPath;
use \DOMNode;
use \DOMNodeList;
use \DOMElement;
use \DOMDocumentFragment;
use \DOMText;

use \LogicException;
use \Exception;

/**
 * Trait DomHelper
 * Helper methods to manipulate the DOM more easily with extended capabilities.
 * Must be used inside a class that extends DOMDocument.
 */
trait DomRoutines
{
    /** @var DOMXPath $xpath Cached XPath engine */
    protected DOMXPath $xpath;

    /** Initialize the XPath engine */
    protected function initXpath(): void
    {
        $this->xpath = new DOMXPath($this);
    }

    /**
     * Find elements using XPath query
     */
    public function _find(string $query, ?DOMNode $contextNode = null): DOMNodeList
    {
        return $this->xpath->query($query, $contextNode);
    }
    
    /**
     * Find the first matching element using XPath
     */
    public function _findOne(string $query, ?DOMNode $contextNode = null): ?DOMNode
    {
        $nodes = $this->xpath->query($query, $contextNode);
        return ($nodes && $nodes->length > 0) ? $nodes->item(0) : null;
    }
    protected function bindAsset($strAssetPath){
        $ext = pathinfo($strAssetPath, PATHINFO_EXTENSION);
        switch($ext){
            case "svg":
            case "jpg":
            case "png":

            break;

            case "css":
                $newLink = $this->createElement('link');
                $newLink->setAttribute('rel', 'stylesheet');
                $newLink->setAttribute('href', $strAssetPath);
                $this->_findOne('head')->appendChild($newLink);
            break;

            case "font":

            break;

            case "js":
                $newScript = $this->createElement("script");
                $newScript->setAttribute("src", $strAssetPath);
                $this->_findOne('body')->appendChild($newScript);
            break;
        }
    }
    /**
     * Create element with optional text node
     */
    public function _createElementWithText(string $name, ?string $text = null): DOMElement
    {
        $el = $this->createElement($name);
        if ($text !== null) {
            $el->appendChild($this->createTextNode($text));
        }
        return $el;
    }

    /**
     * Append child to parent node
     */
    public function _append(DOMNode $parent, DOMNode $child): DOMNode
    {
        return $parent->appendChild($child);
    }

    /**
     * Replace old node with new node
     */
    public function _replace(DOMNode $oldNode, DOMNode $newNode): void
    {
        $oldNode->parentNode?->replaceChild($newNode, $oldNode);
    }

    /**
     * Remove a node from the DOM
     */
    public function _delete(DOMNode $node): void
    {
        $node->parentNode?->removeChild($node);
    }

    /**
     * Clone a node deeply
     */
    public function _clone(DOMNode $node): DOMNode
    {
        return $node->cloneNode(true);
    }

    /**
     * Import and append nodes matching XPath from another DOMDocument
     */
    public function _appendFragment(DOMDocument $sourceDoc, string $query, DOMNode $target): void
    {
        $sourceXpath = new DOMXPath($sourceDoc);
        $nodes = $sourceXpath->query($query);
        foreach ($nodes as $node) {
            $imported = $this->importNode($node, true);
            $target->appendChild($imported);
        }
    }

    /**
     * Set inner HTML of a DOM element
     */
    public function _setInnerHtml(DOMElement $element, string $html): void
    {
        while ($element->firstChild) {
            $element->removeChild($element->firstChild);
        }

        $tmp = new DOMDocument();
        $tmp->loadHTML("<div>$html</div>", LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        foreach ($tmp->documentElement->childNodes as $child) {
            $imported = $this->importNode($child, true);
            $element->appendChild($imported);
        }
    }

    /**
     * Get inner HTML content of an element
     */
    public function _getInnerHtml(DOMNode $element): string
    {
        $html = '';
        foreach ($element->childNodes as $child) {
            $html .= $element->ownerDocument->saveHTML($child);
        }
        return $html;
    }

    /**
     * Dump the current DOM tree to output
     */
    public function _dumpTree(): void
    {
        echo $this->saveHTML();
    }

    /**
     * Return DOM tree as string
     */
    public function _getTree(): string
    {
        return $this->saveHTML();
    }

    /**
     * Create a document fragment from HTML string
     */
    public function _createFragment(string|DOMElement $html): DOMDocumentFragment
    {
        if($html instanceof DOMElement){
            $html = $this->importNode($html, true);
        }
        $fragment = $this->createDocumentFragment();
        @$fragment->appendChild($html);
        return $fragment;
    }

    /**
     * Create and append a stylesheet link in head
     */
    public function _addStylesheet(string $href): void
    {
        $link = $this->createElement('link');
        $link->setAttribute('rel', 'stylesheet');
        $link->setAttribute('href', $href);

        $head = $this->getElementsByTagName('head')->item(0);
        if ($head) {
            $head->appendChild($link);
        }
    }
    public function css2xpath(string $cssSelector): string
    {
        $xpath = '//'; // Start with the root XPath
        $cssSelector = trim($cssSelector);

        // Split the selector into parts based on combinators
        $parts = preg_split('/\s+(?![^\[]*\])/', $cssSelector); // Split by spaces not inside attribute selectors

        foreach ($parts as $part) {
            if (strpos($part, '>') !== false) {
                // Handle child combinator
                $subParts = explode('>', $part);
                foreach ($subParts as $subPart) {
                    $xpath .= $this->convertSimpleSelector(trim($subPart)) . '/';
                }
                $xpath = rtrim($xpath, '/');
            } else {
                // Handle descendant combinator
                $xpath .= $this->convertSimpleSelector($part) . '//';
            }
        }

        return rtrim($xpath, '/');
    }
    public function convertSimpleSelector(string $selector): string
    {
        $xpath = '';

        // Handle ID selector
        if (strpos($selector, '#') === 0) {
            $id = substr($selector, 1);
            $xpath .= "*[@id='$id']";
        }
        // Handle class selector
        elseif (strpos($selector, '.') === 0) {
            $class = substr($selector, 1);
            $xpath .= "*[contains(concat(' ', normalize-space(@class), ' '), ' $class ')]";
        }
        // Handle attribute selector
        elseif (preg_match('/\[(.+?)\]/', $selector, $matches)) {
            $attribute = $matches[1];
            $xpath .= "*[@$attribute]";
        }
        // Handle element selector
        else {
            $xpath .= $selector;
        }

        return $xpath;
    }
    /**
     * Create and append a script in body
     */
    public function _addScript(string $src, bool $defer = true): void
    {
        $script = $this->createElement('script');
        $script->setAttribute('src', $src);
        if ($defer) {
            $script->setAttribute('defer', 'defer');
        }

        $body = $this->getElementsByTagName('body')->item(0);
        if ($body) {
            $body->appendChild($script);
        }
    }

    /**
     * Create and append preload link (e.g., for fonts/images)
     */
    public function _addPreload(string $href, string $as): void
    {
        $link = $this->createElement('link');
        $link->setAttribute('rel', 'preload');
        $link->setAttribute('href', $href);
        $link->setAttribute('as', $as);

        $head = $this->getElementsByTagName('head')->item(0);
        if ($head) {
            $head->appendChild($link);
        }
    }

    /**
     * Watch a DOMNode for mutation (placeholder, JS polyfill would be required)
     * This is a conceptual method.
     */
    public function _watchNode(DOMNode $node, callable $callback): void
    {
        // Placeholder — watching DOM mutations in PHP is not possible.
        // You'd have to export data to JS MutationObserver in frontend context.
        throw new LogicException("Watching DOM nodes is only supported in frontend (JS).");
    }

    /**
     * Load HTML into the document safely
     */
    public function _loadSafeHTML(string $html): void
    {
        libxml_use_internal_errors(true);
        $this->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
    }

    /**
     * Create element with attributes
     */
    public function _createElementWithAttrs(string $name, array $attrs = []): DOMElement
    {
        $el = $this->createElement($name);
        foreach ($attrs as $attr => $value) {
            $el->setAttribute($attr, $value);
        }
        return $el;
    }

        /**
     * Append multiple nodes to all elements matching the query.
     *
     * @param string $query The CSS selector to find target elements.
     * @param array $nodes An array of DOMNode objects to append.
     * @return void
     */
    public function appendMultiple(string $query, array $nodes): void
    {
        $targets = $this->find($query);
        if ($targets) {
            foreach ($targets as $target) {
                foreach ($nodes as $node) {
                    $target->appendChild($node->cloneNode(true));
                }
            }
        }
    }

    /**
     * Replace the first element matching the query with a new node.
     *
     * @param string $query The CSS selector to find the target element.
     * @param DOMNode $newNode The new DOMNode to replace the first matching element.
     * @return void
     */
    public function replaceFirst(string $query, DOMNode $newNode): void
    {
        $targets = $this->find($query);
        if ($targets && isset($targets[0])) {
            $parent = $targets[0]->parentNode;
            if ($parent) {
                $parent->replaceChild($newNode, $targets[0]);
            }
        }
    }
}
