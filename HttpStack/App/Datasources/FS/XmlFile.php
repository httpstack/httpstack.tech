<?php

namespace HttpStack\App\Datasources\FS;

use HttpStack\Datasource\Contracts\CRUD;
use HttpStack\Datasource\AbstractDatasource;

class XmlFile extends AbstractDatasource implements CRUD
{
    protected string $xmlFilePath;
    protected array $dataCache = [];
    protected bool $isCacheValid = false;

    public function __construct(string $filePath, bool $readOnly)
    {
        parent::__construct($readOnly);
        $this->xmlFilePath = $filePath ?? '';

        if (!$this->xmlFilePath || !is_file($this->xmlFilePath)) {
            if ($this->isReadOnly()) {
                throw new \InvalidArgumentException("Invalid XML file path provided for a read-only datasource.");
            }
            $this->createRootXmlFile();
        }

        $this->dataCache = $this->read();
    }

    public function read(array $query = []): array
    {
        if ($this->isCacheValid) {
            return $this->dataCache;
        }

        $doc = new \DOMDocument();
        $doc->load($this->xmlFilePath);
        $root = $doc->documentElement;

        $this->dataCache = $this->domElementToArray($root);
        $this->isCacheValid = true;

        return $this->dataCache;
    }

    public function create(array $payload): mixed
    {
        [$xpath, $data] = $payload;

        if ($this->isReadOnly()) {
            throw new \RuntimeException("Cannot create data in a read-only datasource.");
        }

        $doc = new \DOMDocument();
        $doc->load($this->xmlFilePath);
        $xpathObj = new \DOMXPath($doc);
        $parentNodes = $xpathObj->query($xpath);

        if ($parentNodes->length === 0) {
            throw new \RuntimeException("Parent element not found with XPath: " . $xpath);
        }

        $parent = $parentNodes->item(0);
        $elementName = key($data);
        $elementData = $data[$elementName];
        $newElement = $doc->createElement($elementName);

        foreach ($elementData as $key => $value) {
            if (is_array($value)) {
                $child = $doc->createElement($key);
                foreach ($value as $subKey => $subValue) {
                    $child->appendChild($doc->createElement($subKey, htmlspecialchars($subValue)));
                }
                $newElement->appendChild($child);
            } else {
                $newElement->appendChild($doc->createElement($key, htmlspecialchars($value)));
            }
        }

        $parent->appendChild($newElement);
        $this->saveDomToFile($doc);
        $this->isCacheValid = false;

        return true;
    }

    public function update(array $payload): mixed
    {
        [$xpath, $data] = $payload;

        if ($this->isReadOnly()) {
            throw new \RuntimeException("Cannot update data in a read-only datasource.");
        }

        $doc = new \DOMDocument();
        $doc->load($this->xmlFilePath);
        $xpathObj = new \DOMXPath($doc);
        $nodes = $xpathObj->query($xpath);

        if ($nodes->length === 0) {
            throw new \RuntimeException("Element not found with XPath: " . $xpath);
        }

        $nodes->item(0)->nodeValue = htmlspecialchars($data);
        $this->saveDomToFile($doc);
        $this->isCacheValid = false;

        return true;
    }

    public function delete(array $payload): mixed
    {
        [$xpath] = $payload;

        if ($this->isReadOnly()) {
            throw new \RuntimeException("Cannot delete data in a read-only datasource.");
        }

        $doc = new \DOMDocument();
        $doc->load($this->xmlFilePath);
        $xpathObj = new \DOMXPath($doc);
        $nodes = $xpathObj->query($xpath);

        if ($nodes->length === 0) {
            throw new \RuntimeException("Element not found with XPath: " . $xpath);
        }

        foreach ($nodes as $node) {
            $node->parentNode->removeChild($node);
        }

        $this->saveDomToFile($doc);
        $this->isCacheValid = false;

        return true;
    }

    protected function saveDomToFile(\DOMDocument $doc): void
    {
        $doc->formatOutput = true;
        if ($doc->save($this->xmlFilePath) === false) {
            throw new \RuntimeException("Failed to write data to file: " . $this->xmlFilePath);
        }
    }

    protected function createRootXmlFile(): void
    {
        $doc = new \DOMDocument('1.0', 'UTF-8');
        $root = $doc->createElement('data');
        $doc->appendChild($root);
        $this->saveDomToFile($doc);
    }

    // Removed array type hint to allow returning either an array or a string
    protected function domElementToArray(\DOMElement $element)
    {
        $result = [];
        $text = '';
        $hasChildElements = false;

        foreach ($element->childNodes as $node) {
            if ($node instanceof \DOMElement) {
                $hasChildElements = true;
                $value = $this->domElementToArray($node);

                if (!isset($result[$node->tagName])) {
                    $result[$node->tagName] = [];
                }
                $result[$node->tagName][] = $value;
            } elseif ($node instanceof \DOMText || $node instanceof \DOMCdataSection) {
                $text .= $node->nodeValue;
            }
        }

        $trimmedText = trim($text);

        // If there are no child elements, return the trimmed text directly
        if (!$hasChildElements && !empty($trimmedText)) {
            return $trimmedText;
        }

        // Flatten single-item arrays
        foreach ($result as $key => $value) {
            if (is_array($value) && count($value) === 1) {
                $result[$key] = $value[0];
            }
        }

        return $result;
    }
}
