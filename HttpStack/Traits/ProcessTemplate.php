<?php
namespace HttpStack\Traits;
use DOMDocument;
use DOMElement;
use DOMXPath;
trait ProcessTemplate{

    /**
     * Renders the template content, processing data-repeat loops, variables, and function calls.
     */
    public function render(): string
    {


        // --- Step 1: Process data-repeat loops using DOMXPath ---
        // This part remains the same as it operates on the DOM structure.
         // Ensure DOM is loaded for XPath queries
        $this->map = new DOMXPath($this);
        $repeatElements = $this->map->query("//*[@data-repeat]");

        // We need to iterate over a static list of elements because modifying the DOM
        // during iteration can cause issues with live NodeLists.
        $elementsToProcess = [];
        foreach ($repeatElements as $element) {
            $elementsToProcess[] = $element;
        }  

        foreach ($elementsToProcess as $repeatElement) {
            /** @var DOMElement $repeatElement */
            $repeatAttr = $repeatElement->getAttribute('data-repeat');

            if (preg_match('/^([a-zA-Z0-9_]+)\s+as\s+([a-zA-Z0-9_]+)$/', $repeatAttr, $matches)) {
                $collectionVarName = $matches[1];
                $itemVarName = $matches[2];

                if (isset($this->variables[$collectionVarName]) && is_iterable($this->variables[$collectionVarName])) {
                    $collectionData = $this->variables[$collectionVarName];

                    // Find the single template item within this repeat block (e.g., the <li>)
                    // Use a new XPath query on the specific repeat element to ensure it's a child.
                    $itemTemplateElements = $this->map->query("*[@data-repeat-item='" . $itemVarName . "']", $repeatElement);

                    if ($itemTemplateElements->length > 0) {
                        /** @var DOMElement $itemTemplate */
                        $itemTemplate = $itemTemplateElements->item(0);
                        // Remove the original template item from the DOM, we'll append clones
                        $itemTemplate->parentNode->removeChild($itemTemplate);

                        $renderedItemsFragment = $this->createDocumentFragment();

                        foreach ($collectionData as $itemData) {
                            /** @var DOMElement $clonedItem */
                            $clonedItem = $itemTemplate->cloneNode(true);
                            $clonedItem->removeAttribute('data-repeat-item');

                            // Process placeholders within the cloned item's attributes and text content
                            $this->processPlaceholdersInNode($clonedItem, $itemVarName, $itemData);

                            $renderedItemsFragment->appendChild($clonedItem);
                        }
                        // Replace the original data-repeat element with the fragment of rendered items
                        $repeatElement->parentNode->replaceChild($renderedItemsFragment, $repeatElement);
                    } else {
                        // If no data-repeat-item found, just remove the data-repeat attribute
                        $repeatElement->removeAttribute('data-repeat');
                    }
                } else {
                    // Collection data not found or not iterable, remove the data-repeat element
                    if ($repeatElement->parentNode) { // Ensure parent exists before attempting to remove
                         $repeatElement->parentNode->removeChild($repeatElement);
                    }
                }
            } else {
                // Malformed data-repeat attribute, remove it
                $repeatElement->removeAttribute('data-repeat');
            }
        }

        // --- Step 2: Convert the DOMDocument back to a string after DOM manipulations ---
        $output = $this->saveHTML();

        // --- Step 3: Process simple {{variable}} replacements ---
        $output = $this->replaceSimpleVariables($output, $this->variables);

        // --- Step 4: Process {{ functionName(params) }} calls ---
        $output = $this->replaceFunctionCalls($output);

        return $output;
    }

    /**
     * Recursively processes placeholders (e.g., item[key]) within a DOM node and its children.
     * This method is specifically for the new array-like access within data-repeat items.
     */
    protected function processPlaceholdersInNode(DOMElement $node, string $itemVarName, array|object $itemData): void
    {
        // Process attributes
        foreach ($node->attributes as $attr) {
            $originalValue = $attr->value;
            $newValue = $this->replaceDataAttributePlaceholders($originalValue, $itemVarName, $itemData);
            if ($originalValue !== $newValue) {
                $attr->value = $newValue;
            }
        }

        // Process text content of the node itself if it's a text node directly inside
        if ($node->nodeType === XML_ELEMENT_NODE && $node->hasChildNodes()) {
            foreach ($node->childNodes as $childNode) {
                if ($childNode->nodeType === XML_TEXT_NODE) {
                    $originalValue = $childNode->nodeValue;
                    $newValue = $this->replaceDataAttributePlaceholders($originalValue, $itemVarName, $itemData);
                    if ($originalValue !== $newValue) {
                        $childNode->nodeValue = $newValue;
                    }
                }
            }
        }

        // Recursively process children
        foreach ($node->childNodes as $child) {
            if ($child->nodeType === XML_ELEMENT_NODE) {
                $this->processPlaceholdersInNode($child, $itemVarName, $itemData);
            }
        }
    }

    /**
     * Replaces placeholders like 'item[key]' in a string.
     */
protected function replaceDataAttributePlaceholders(string $content, string $itemVarName, array|object $itemData): string
{
    //echo("\nProcessing content: " . $content . " for item: " . $itemVarName); // Add this
    $pattern = '/' . preg_quote($itemVarName) . '\[([a-zA-Z0-9_]+)\]/';

    return preg_replace_callback($pattern, function($matches) use ($itemData) {
        $key = $matches[1];
        //echo("\nMatched key: " . $key); // Add this
        if (is_array($itemData) && isset($itemData[$key])) {
            //echo("\nFound array data for key " . $key . ": " . $itemData[$key]); // Add this
            return (string)$itemData[$key];
        } elseif (is_object($itemData) && property_exists($itemData, $key)) {
            //echo("\nFound object data for key " . $key . ": " . $itemData->$key); // Add this
            return (string)$itemData->$key;
        }
        //echo("\nKey " . $key . " not found in data. Returning original: " . $matches[0]); // Add this
        return $matches[0]; // Return original if not found
    }, $content);
}


    /**
     * Replaces simple {{key}} variables in the content.
     * This is separate from the item[key] processing.
     */
    protected function replaceSimpleVariables(string $content, array $variables): string
    {
        //var_dump($variables);
        foreach ($variables as $key => $value) {
            //dd($key);
            //dd($value);
            if (is_string($value) || is_numeric($value) || is_bool($value) || is_null($value)) {
                $content = str_replace('{{' . $key . '}}', $value, $content);
            }
            // Note: This method is specifically for simple {{key}} variables.
            // Dot notation for global variables would need to be added here if desired.
        }
        return $content;
    }

    /**
     * Processes and replaces custom function calls like {{ myFunc(param1, 'param2') }} in the content.
     *
     * @param string $content The template content.
     * @return string The content with function calls replaced.
     */
    protected function replaceFunctionCalls(string $content): string
    {
        // Regex to find {{ functionName(params) }}
        // Captures: 1=functionName, 2=params string
        $pattern = '/\{\{\s*([a-zA-Z_][a-zA-Z0-9_]*)\s*\((.*?)\)\s*\}\}/';

        return preg_replace_callback($pattern, function ($matches) {
            $functionName = $matches[1];
            $paramsString = $matches[2]; // e.g., "89, 'hello', someVar"

            if (isset($this->functions[$functionName]) && is_callable($this->functions[$functionName])) {
                $callback = $this->functions[$functionName];

                // Parse parameters string into an array of actual values
                $args = [];
                if (!empty($paramsString)) {
                    // Simple parsing: split by comma, trim, and attempt to cast to int/float/bool/null
                    // This is a basic parser. For complex types (arrays, objects), you'd need a more robust parser.
                    $rawArgs = explode(',', $paramsString);
                    foreach ($rawArgs as $arg) {
                        $arg = trim($arg);
                        if (is_numeric($arg)) {
                            $args[] = str_contains($arg, '.') ? (float)$arg : (int)$arg;
                        } elseif (in_array(strtolower($arg), ['true', 'false'])) {
                            $args[] = (strtolower($arg) === 'true');
                        } elseif (strtolower($arg) === 'null') {
                            $args[] = null;
                        } elseif (str_starts_with($arg, "'") && str_ends_with($arg, "'")) {
                            $args[] = trim($arg, "'"); // String literal with single quotes
                        } elseif (str_starts_with($arg, '"') && str_ends_with($arg, '"')) {
                            $args[] = trim($arg, '"'); // String literal with double quotes
                        } elseif (isset($this->variables[$arg])) {
                            // If it's a variable name, use its value from $this->variables
                            $args[] = $this->variables[$arg];
                        } else {
                            // Treat as a string if no other type matches, or handle as an error
                            $args[] = $arg;
                        }
                    }
                }

                try {
                    // Call the defined function with the parsed arguments
                    $result = call_user_func_array($callback, $args);
                    return (string)$result; // Return the result as a string
                } catch (\Exception $e) {
                    // Handle errors during function execution (e.g., log, return empty string)
                    echo("\nTemplate function '{$functionName}' failed: " . $e->getMessage());
                    return ''; // Or return an error message placeholder
                }
            }
            return $matches[0]; // If function not found, return the original placeholder
        }, $content);
    }

}

?>