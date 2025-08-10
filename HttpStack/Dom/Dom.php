<?php
namespace HttpStack\Dom;

use DOMNode;
use DOMXPath;
use Exception;
use DOMElement;
use DOMDocument;
use DOMNodeList;
use DOMDocumentFragment; // For better error handling
use HttpStack\IO\FileLoader; // Assuming you have a FileLoader class

/**
 * Dom class extends DOMDocument to provide high-level methods for
 * HTML document ingestion and manipulation using DOMXPath.
 * Enhanced to handle font and image asset appending with preloading.
 * DOMXPath is reinitialized after every DOM mutation to ensure consistency.
 */
class Dom extends DOMDocument
{
    protected DOMXPath $xpath;
    protected array $imageUrlsToPreload = []; // To collect image URLs for preloading
    protected ?FileLoader $fileLoader = null; // Optional FileLoader instance

    /**
     * Constructor. Ingests an HTML string or loads an HTML file.
     *
     * @param string $htmlContentOrFilePath The HTML string or path to an HTML file.
     * @param FileLoader|null $fileLoader Optional FileLoader instance for resolving local asset paths.
     * @throws Exception If the HTML content cannot be loaded.
     */
    public function __construct(string $htmlContentOrFilePath, ?FileLoader $fileLoader = null)
    {
        // Call parent constructor for DOMDocument setup
        parent::__construct("1.0", "utf-8");

        $this->fileLoader = $fileLoader; // Store the file loader

        // Suppress errors with @ for loadHTML/loadHTMLFile as they can be noisy
        // but always check the return value.
        $options = LIBXML_HTML_NODEFDTD;

        if (str_contains($htmlContentOrFilePath, "<") || str_starts_with(trim($htmlContentOrFilePath), '<')) {
            // Assume it's an HTML string if it contains '<' or starts with '<' after trimming
            if (@$this->loadHTML($htmlContentOrFilePath, $options) === false) {
                throw new Exception("Failed to load HTML string.");
            }
        } else {
            // Otherwise, assume it's a file path
            if (!file_exists($htmlContentOrFilePath)) {
                throw new Exception("HTML file not found: {$htmlContentOrFilePath}");
            }
            if (@$this->loadHTMLFile($htmlContentOrFilePath, $options) === false) {
                throw new Exception("Failed to load HTML file: {$htmlContentOrFilePath}");
            }
        }

        // Initialize DOMXPath for querying the document
        $this->initXPath(); // Initial XPath setup
    }

    /**
     * Reinitializes the DOMXPath object.
     * This method should be called after any structural changes to the DOMDocument.
     *
     * @return void
     */
    protected function initXPath(): void
    {
        $this->xpath = new DOMXPath($this);
    }

    /**
     * Retrieves document assets (placeholder for actual implementation).
     * This method would typically parse the DOM for specific asset links (e.g., <link>, <script>, <img>).
     *
     * @return array An array of document assets.
     */
    public function getDocAssets(): array
    {
        $assets = [];

        // Find all <link> and <script> tags
        $linkNodes = $this->xpath->query("//link[@href]");
        foreach ($linkNodes as $node) {
            $assets[] = [
                'type' => 'link', // Generic link
                'href' => $node->getAttribute('href'),
                'rel' => $node->getAttribute('rel'),
                'as' => $node->getAttribute('as') // Capture 'as' for preload links
            ];
        }

        $scriptNodes = $this->xpath->query("//script[@src]");
        foreach ($scriptNodes as $node) {
            $assets[] = [
                'type' => 'script',
                'src' => $node->getAttribute('src')
            ];
        }

        // Find all <img> tags for preloading
        $imgNodes = $this->xpath->query("//img[@src]");
        foreach ($imgNodes as $node) {
            $assets[] = [
                'type' => 'image',
                'src' => $node->getAttribute('src')
            ];
            // Also collect for internal preloading mechanism
            $this->imageUrlsToPreload[] = $node->getAttribute('src');
        }

        return $assets;
    }

    /**
     * Appends assets (e.g., CSS/JS links, fonts, images) to the HTML document.
     * This is a high-level method to add elements to the <head> or <body>.
     *
     * @param array $assets An array of asset data (e.g., from your JSON).
     * @return void
     */
    public function appendAssets(array $assets): void
    {
        $head = $this->getElementsByTagName('head')->item(0);
        $body = $this->getElementsByTagName('body')->item(0);

        if (!$head && !$body) {
            error_log("Warning: No <body> or <head> tag found to append assets.");
            return;
        }

        foreach ($assets as $asset) {
            if (!isset($asset['type'])) {
                continue; // Skip malformed asset entries
            }

            switch ($asset['type']) {
                case 'sheet': // CSS stylesheet
                    if (isset($asset['cdn'])) {
                        $href = $asset['cdn'];
                    } elseif (isset($asset['filename']) && $this->fileLoader) {
                        $href = $this->fileLoader->findFile($asset['filename'], null, 'css');
                    } else {
                        continue; // No valid source for stylesheet
                    }
                    $link = $this->createElement('link');
                    $link->setAttribute('rel', 'stylesheet');
                    $link->setAttribute('href', $href);
                    if ($head) {
                        $head->appendChild($link);
                    } else {
                        $body->appendChild($link); // Fallback to body if no head
                    }
                    break;

                case 'script': // JavaScript file
                    if (isset($asset['cdn'])) {
                        $src = $asset['cdn'];
                    } elseif (isset($asset['filename']) && $this->fileLoader) {
                        $src = $this->fileLoader->findFile($asset['filename'], null, 'js');
                    } else {
                        continue; // No valid source for script
                    }
                    $script = $this->createElement('script');
                    $script->setAttribute('src', $src);
                    // Defer scripts by default for better performance
                    $script->setAttribute('defer', 'defer');
                    if ($body) {
                        $body->appendChild($script);
                    } else {
                        $head->appendChild($script); // Fallback to head if no body
                    }
                    break;

                case 'font': // Font file (for preloading)
                    $fontSrc = null;
                    if (isset($asset['cdn'])) {
                        $fontSrc = $asset['cdn'];
                    } elseif (isset($asset['filename']) && $this->fileLoader) {
                        // Attempt to resolve local font file path
                        $fontSrc = $this->fileLoader->findFile($asset['filename'], null, 'font'); // Assuming 'font' type in FileLoader
                    }

                    if ($fontSrc) {
                        $link = $this->createElement('link');
                        $link->setAttribute('rel', 'preload');
                        $link->setAttribute('as', 'font');
                        $link->setAttribute('href', $fontSrc);
                        $link->setAttribute('crossorigin', 'anonymous'); // Essential for fonts
                        if ($head) {
                            $head->appendChild($link);
                        } else {
                            $body->appendChild($link); // Fallback
                        }
                    } else {
                        error_log("Warning: No valid source (cdn or filename) found for font asset: " . json_encode($asset));
                    }

                    // Optionally, if it's a font stylesheet (e.g., Google Fonts), add a regular stylesheet link
                    if (isset($asset['stylesheet_cdn'])) {
                        $link = $this->createElement('link');
                        $link->setAttribute('rel', 'stylesheet');
                        $link->setAttribute('href', $asset['stylesheet_cdn']);
                        if ($head) {
                            $head->appendChild($link);
                        } else {
                            $body->appendChild($link); // Fallback
                        }
                    }
                    break;

                case 'image': // Image file (collect for preloading script)
                    $imageSrc = null;
                    if (isset($asset['src'])) {
                        $imageSrc = $asset['src'];
                    } elseif (isset($asset['filename']) && $this->fileLoader) {
                        // Attempt to resolve local image file path
                        $imageSrc = $this->fileLoader->findFile($asset['filename'], null, 'image'); // Assuming 'image' type in FileLoader
                    }

                    if ($imageSrc) {
                        // Collect image URLs to be preloaded by a single JS block
                        $this->imageUrlsToPreload[] = $imageSrc;
                    } else {
                        error_log("Warning: No valid source (src or filename) found for image asset: " . json_encode($asset));
                    }
                    break;
                // Add more asset types (e.g., 'inline_script', 'inline_style') as needed
            }
        }

        // After processing all assets, append the image preloading script if any images were collected
        if (!empty($this->imageUrlsToPreload)) {
            $this->appendImagePreloadScript();
        }

        // Reinitialize XPath after appending assets
        $this->initXPath();
    }

    /**
     * Generates and appends a JavaScript block to preload collected image URLs.
     * This script is added to the <body>.
     *
     * @return void
     */
    protected function appendImagePreloadScript(): void
    {
        $body = $this->getElementsByTagName('body')->item(0);
        if (!$body) {
            error_log("Warning: No <body> tag found to append image preload script.");
            return;
        }

        $preloadScriptContent = "
            (function() {
                var imagesToPreload = " . json_encode(array_unique($this->imageUrlsToPreload)) . ";
                imagesToPreload.forEach(function(url) {
                    var img = new Image();
                    img.src = url;
                    // Optional: Add event listeners for load/error if needed for tracking
                    // img.onload = function() { console.log('Preloaded: ' + url); };
                    // img.onerror = function() { console.error('Failed to preload: ' + url); };
                });
            })();
        ";

        $script = $this->createElement('script');
        $script->setAttribute('type', 'text/javascript');
        $script->appendChild($this->createTextNode($preloadScriptContent));
        $body->appendChild($script);

        // Clear the collected URLs after appending the script
        $this->imageUrlsToPreload = [];
    }

    /**
     * Finds nodes using an XPath query.
     *
     * @param string $query The XPath query string.
     * @param DOMNode|null $contextNode The context node for the query.
     * @return DOMNodeList A DOMNodeList containing all matching nodes.
     */
    public function find(string $query, ?DOMNode $contextNode = null): DOMNodeList
    {
        return $this->xpath->query($query, $contextNode);
    }

    /**
     * Finds the first node matching an XPath query.
     *
     * @param string $query The XPath query string.
     * @param DOMNode|null $contextNode The context node for the query.
     * @return DOMElement|null The first matching DOMElement, or null if not found.
     */
    public function findOne(string $query, ?DOMNode $contextNode = null): ?DOMElement
    {
        $nodes = $this->xpath->query($query, $contextNode);
        return $nodes->item(0) instanceof DOMElement ? $nodes->item(0) : null;
    }

    /**
     * Updates content or attributes of nodes matching an XPath query.
     *
     * @param string $query The XPath query string.
     * @param callable $callback A callback function (DOMElement $node): void to apply to each node.
     * @return int The number of nodes updated.
     */
    public function update(string $query, callable $callback): int
    {
        $count = 0;
        $nodes = $this->xpath->query($query); // Query before modification

        // Iterate over a copy of the NodeList if modifications might affect traversal
        // For simple attribute/content updates, direct iteration is usually fine.
        // For structural changes (removing/adding siblings), a copy is safer.
        $nodesArray = iterator_to_array($nodes); // Convert to array to avoid issues during iteration if nodes are removed/moved

        foreach ($nodesArray as $node) {
            if ($node instanceof DOMElement) {
                $callback($node);
                $count++;
            }
        }
        $this->initXPath(); // Reinitialize XPath after updating nodes
        return $count;
    }

    /**
     * Creates and appends a new element to a specified parent node.
     *
     * @param string $parentQuery XPath query to find the parent node.
     * @param string $tagName The tag name of the new element (e.g., 'div', 'p').
     * @param array $attributes Optional associative array of attributes for the new element.
     * @param string $content Optional inner HTML content for the new element.
     * @return DOMElement|null The newly created DOMElement, or null if parent not found.
     */
    public function create(string $parentQuery, string $tagName, array $attributes = [], string $content = ''): ?DOMElement
    {
        $parentNode = $this->findOne($parentQuery);
        if ($parentNode) {
            $newElement = $this->createElement($tagName);
            foreach ($attributes as $name => $value) {
                $newElement->setAttribute($name, $value);
            }
            if ($content) {
                // Create a document fragment to parse HTML content safely
                $fragment = $this->createDocumentFragment();
                // LoadHTML will parse the string as HTML, including entities
                @$fragment->appendXML($content);
                $newElement->appendChild($fragment);
            }
            $parentNode->appendChild($newElement);
            $this->initXPath(); // Reinitialize XPath after creating a new element
            return $newElement;
        }
        return null;
    }

    /**
     * Deletes nodes matching an XPath query.
     *
     * @param string $query The XPath query string.
     * @return int The number of nodes deleted.
     */
    public function delete(string $query): int
    {
        $nodes = $this->xpath->query($query);
        $count = 0;
        // Iterate over a copy of the NodeList because removing nodes
        // while iterating over the live NodeList can cause issues.
        $nodesArray = iterator_to_array($nodes);
        foreach ($nodesArray as $node) {
            if ($node->parentNode) {
                $node->parentNode->removeChild($node);
                $count++;
            }
        }
        $this->initXPath(); // Reinitialize XPath after deleting nodes
        return $count;
    }

    /**
     * Returns the current HTML content of the DOMDocument.
     *
     * @return string The HTML content.
     */
    public function toHtml(): string
    {
        return $this->saveHTML();
    }

    /**
     * Returns the internal DOMDocument object.
     * This is useful if you need to perform direct DOMDocument operations.
     *
     * @return DOMDocument
     */
    public function getDomDocument(): DOMDocument
    {
        return $this; // Since Dom extends DOMDocument, $this is the DOMDocument instance.
    }

    /**
     * Returns the internal DOMXPath object.
     *
     * @return DOMXPath
     */
    public function getXPath(): DOMXPath
    {
        return $this->xpath;
    }
}
