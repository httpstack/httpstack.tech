<?php

namespace HttpStack\App\Datasources\FS;

use HttpStack\Datasource\Contracts\CRUD;
use HttpStack\Datasource\AbstractDatasource;

/**
 * A datasource class for managing data from a single XML file.
 * This class extends AbstractDatasource and implements the CRUD interface
 */
class XmlFile extends AbstractDatasource implements CRUD
{
    protected string $xmlFilePath;
    protected array $dataCache = [];
    protected bool $isCacheValid = false;

    /**
     * Constructor to initialize the XmlFile datasource.
     *
     * @param string $filePath The absolute path to the XML file.
     * @param bool $readOnly Whether the datasource is read-only.
     * @throws \InvalidArgumentException if the file path is not provided or the file is invalid.
     */
    public function __construct(string $filePath, bool $readOnly)
    {
        parent::__construct($readOnly);
        $this->xmlFilePath = $filePath ?? '';

        if (!$this->xmlFilePath || !is_file($this->xmlFilePath)) {
            // If the file doesn't exist, we can assume we're creating a new one
            // as long as the datasource is not read-only.
            if ($this->isReadOnly()) {
                throw new \InvalidArgumentException("Invalid XML file path provided for a read-only datasource.");
            }
            // Create an empty root element for the new file.
            $this->createRootXmlFile();
        }

        // Load the initial data into the cache.
        $this->dataCache = $this->read();
    }

    /**
     * Reads and parses the XML file into an associative array.
     *
     * @param array $query Query parameters (currently not used for filtering a single file).
     * @return array The data from the XML file as an associative array.
     */
    public function read(array $query = []): array
    {
        if ($this->isCacheValid) {
            return $this->dataCache;
        }

        // Load the XML file as a SimpleXMLElement object.
        $xml = simplexml_load_file($this->xmlFilePath);
        if ($xml === false) {
            return [];
        }

        // Convert the SimpleXMLElement object to an array for consistency
        // with the JsonDirectory datasource.
        $this->dataCache = json_decode(json_encode($xml), true);
        $this->isCacheValid = true;

        return $this->dataCache;
    }

    /**
     * Creates a new element within the XML file.
     *
     * @param array $payload An array containing the parent XPath and the data to add.
     * Example: ['//pages', ['new_page' => ['title' => 'New Page', 'description' => '...']]]
     * @return mixed A boolean on success or throws an exception on failure.
     */
    public function create(array $payload): mixed
    {
        list($xpath, $data) = $payload;

        if ($this->isReadOnly()) {
            throw new \RuntimeException("Cannot create data in a read-only datasource.");
        }

        $xml = simplexml_load_file($this->xmlFilePath);
        $parent = $xml->xpath($xpath);
        if (empty($parent)) {
            throw new \RuntimeException("Parent element not found with XPath: " . $xpath);
        }

        // Assuming $data is an associative array with a single key for the new element.
        $elementName = key($data);
        $elementData = $data[$elementName];
        $newElement = $parent[0]->addChild($elementName);
        foreach ($elementData as $key => $value) {
            if (is_array($value)) {
                // If the value is an array, it might be a nested element (like a CTA link).
                $childElement = $newElement->addChild($key);
                foreach ($value as $childKey => $childValue) {
                    $childElement->addChild($childKey, $childValue);
                }
            } else {
                $newElement->addChild($key, $value);
            }
        }

        $this->saveXmlToFile($xml);
        $this->isCacheValid = false; // Invalidate cache after creation.
        return true;
    }

    /**
     * Updates an existing element in the XML file.
     *
     * @param array $payload An array containing the XPath of the element to update and the new data.
     * Example: ['//contact/title', 'New Contact Title']
     * @return mixed A boolean on success or throws an exception on failure.
     */
    public function update(array $payload): mixed
    {
        list($xpath, $data) = $payload;
        if ($this->isReadOnly()) {
            throw new \RuntimeException("Cannot update data in a read-only datasource.");
        }
        $xml = simplexml_load_file($this->xmlFilePath);
        $element = $xml->xpath($xpath);
        if (empty($element)) {
            throw new \RuntimeException("Element not found with XPath: " . $xpath);
        }
        // Assuming $data is a string to update the element's value.
        $element[0][0] = $data;

        $this->saveXmlToFile($xml);
        $this->isCacheValid = false; // Invalidate cache after update.
        return true;
    }

    /**
     * Deletes an element from the XML file.
     *
     * @param array $payload An array containing the XPath of the element to delete.
     * Example: ['//contact']
     * @return mixed A boolean on success or throws an exception on failure.
     */
    public function delete(array $payload): mixed
    {
        list($xpath) = $payload;
        if ($this->isReadOnly()) {
            throw new \RuntimeException("Cannot delete data in a read-only datasource.");
        }
        $xml = simplexml_load_file($this->xmlFilePath);
        $elements = $xml->xpath($xpath);
        if (empty($elements)) {
            throw new \RuntimeException("Element not found with XPath: " . $xpath);
        }
        // Iterate through all found elements and unset them.
        foreach ($elements as $element) {
            unset($element[0]);
        }
        $this->saveXmlToFile($xml);
        $this->isCacheValid = false; // Invalidate cache after deletion.
        return true;
    }

    /**
     * Saves the SimpleXMLElement object back to the file.
     *
     * @param \SimpleXMLElement $xml The XML object to save.
     * @throws \RuntimeException if the file cannot be written.
     */
    protected function saveXmlToFile(\SimpleXMLElement $xml): void
    {
        $result = $xml->asXML($this->xmlFilePath);
        if ($result === false) {
            throw new \RuntimeException("Failed to write data to file: " . $this->xmlFilePath);
        }
    }

    /**
     * Creates a new XML file with a root element if it doesn't exist.
     *
     * @throws \RuntimeException if the file cannot be created.
     */
    protected function createRootXmlFile(): void
    {
        $doc = new \DOMDocument('1.0', 'UTF-8');
        $root = $doc->createElement('data');
        $doc->appendChild($root);
        if ($doc->save($this->xmlFilePath) === false) {
            throw new \RuntimeException("Failed to create a new XML file at: " . $this->xmlFilePath);
        }
    }
}
