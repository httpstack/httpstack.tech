<?php
namespace HttpStack\Template;

// Removed direct DOMDocument, DOMNode, DOMXPath, DOMElement imports
// as these are now managed internally by the HttpStack\Dom\Dom class
use Exception;
use HttpStack\Dom\Dom; // Import the TemplateModel
use Dev\v3\TemplateModel; // For error handling in render
use HttpStack\IO\FileLoader; // Crucial: Import the custom Dom class

/**
 * A straightforward template engine for replacing placeholders and executing functions.
 * Now with support for multiple namespaced templates and composition with HttpStack\Dom\Dom.
 *
 * @version 1.4.0
 *
 * Version Log:
 * 1.0.0: The initial class that handled basic variable replacement ({{var}}).
 * 1.1.0: A minor version bump for adding a significant, backward-compatible feature: the ability to define and use functions ({{func()}}).
 * 1.1.1: A patch version bump for fixing the bug where numeric literals in function arguments were not parsed correctly.
 * 1.2.0: Added support for multiple, namespaced templates and the {{include()}} function for composition.
 * 1.3.0: Changed constructor parameters to accept filePath, TemplateModel, and FileLoader for improved dependency management.
 * 1.4.0: Refactored to use HttpStack\Dom\Dom for all HTML/DOM manipulation, replacing direct DOMDocument and DOMXPath usage.
 */
class Template
{
    protected array $files = []; // Stores loaded template file contents (raw HTML strings)
    protected array $vars = []; // Stores variables for replacement (legacy/direct set)
    protected FileLoader $fileLoader;
    protected Dom $dom; // Changed from DOMDocument/DOMXPath to HttpStack\Dom\Dom instance
    protected TemplateModel $dataModel; // The TemplateModel instance for data
    protected array $functions = []; // Stores callable functions for template use

    public string $defaultFileExt = "html";
    public string $defaultLayout = "base.html";
    /**
     * Constructor for the Template engine.
     *
     * @param string $filePath The path to the main HTML template file.
     * @param TemplateModel $dataModel The TemplateModel instance containing data for the template.
     * @param FileLoader $fileLoader The file loader instance for resolving template paths.
     * @throws Exception If the main template file cannot be loaded.
     */
    public function __construct(string $filePath='', TemplateModel $dataModel, FileLoader $fileLoader)
    {
        $this->fileLoader = $fileLoader;
        $this->dataModel = $dataModel;
        $filePath = $fileLoader->findFile($this->defaultLayout, null, "html");
        // Load the main template file content as a raw HTML string
        $loadedContent = $this->fileLoader->readFile(basename($filePath)); // Assuming readFile takes base filename
        if ($loadedContent === null) {
            throw new Exception("Failed to load main template file: {$filePath}");
        }
        $this->setFile('main', $loadedContent);

        // Initialize the Dom object with the loaded content.
        // The Dom class itself will handle its internal DOMDocument and DOMXPath.
        // We pass the fileLoader to Dom as it also needs to resolve asset paths.
        $this->dom = new Dom($this->files['main'], $this->fileLoader);

        // Initialize template variables with data from the TemplateModel
        $this->setVar($this->dataModel->getAll());

        // Automatically append assets from the data model if they exist
        // This leverages the Dom class's asset handling capabilities.
        $assets = $this->dataModel->getAssets();
        if (!empty($assets)) {
            $this->dom->appendAssets($assets);
        }
    }

    /**
     * Loads a file's contents into the internal files array.
     *
     * @param string $nameSpace The namespace for the loaded file.
     * @param string $baseFileName The base filename to load.
     * @return string The content of the loaded file.
     * @throws Exception If the file cannot be found or read.
     */
    public function loadFile(string $nameSpace, string $baseFileName): string
    {
        $content = $this->fileLoader->readFile($baseFileName);
        if ($content === null) {
            throw new Exception("File '{$baseFileName}' not found or could not be read by FileLoader.");
        }
        $this->files[$nameSpace] = $content;
        return $this->files[$nameSpace];
    }

    /**
     * Retrieves a loaded file's content by namespace, or all files.
     *
     * @param string $nameSpace The namespace of the file to retrieve.
     * @return string|array The file content or all files if namespace is empty.
     */
    public function getFile(string $nameSpace = ''): string|array
    {
        return $nameSpace ? ($this->files[$nameSpace] ?? '') : $this->files;
    }

    /**
     * Sets the content for a given template namespace.
     * If the 'main' template is updated, it also re-initializes the internal Dom object
     * with the new content to ensure it's working with the latest HTML structure.
     *
     * @param string $nameSpace The namespace to set.
     * @param string $html The HTML content.
     * @return self
     */
    public function setFile(string $nameSpace, string $html): self
    {
        $this->files[$nameSpace] = $html;
        // If the 'main' template (which the Dom object is currently managing) is updated,
        // we need to re-initialize the Dom object with the new content.
        if ($nameSpace === 'main') {
            $this->dom = new Dom($this->files['main'], $this->fileLoader);
        }
        return $this;
    }

    /**
     * Sets the active template for parsing and manipulation.
     * This method now re-initializes the internal Dom object with the content
     * of the specified template namespace.
     *
     * @param string $nameSpace The namespace of the file to set as the current template.
     * @return self
     * @throws Exception If the namespace is not found or HTML cannot be loaded.
     */
    public function setTemplate(string $nameSpace): self
    {
        if (!isset($this->files[$nameSpace])) {
            throw new Exception("Template namespace '{$nameSpace}' not found.");
        }
        // Re-initialize the internal Dom object with the content of the specified template.
        // This effectively switches the DOM the Template class is working with.
        $this->dom = new Dom($this->files[$nameSpace], $this->fileLoader);
        return $this;
    }

    /**
     * Creates a DOMElement for an asset based on file extension.
     * This method is now largely redundant as HttpStack\Dom\Dom::appendAssets is preferred
     * for adding assets to the document.
     *
     * @param string $file The filename of the asset.
     * @return DOMElement|null The created DOMElement, or null if type is not supported.
     * @deprecated 1.4.0 Use HttpStack\Dom\Dom::appendAssets instead.
     */
    public function makeAsset(string $file): ?DOMElement
    {
        error_log("Warning: Template::makeAsset is deprecated. Use Dom::appendAssets instead.");
        // This method's logic is now handled by Dom::appendAssets.
        // Returning null or throwing an error is appropriate here.
        return null;
    }

    /**
     * Creates an array of DOMElement assets from a list of files.
     * This method is now largely redundant as HttpStack\Dom\Dom::appendAssets is preferred.
     *
     * @param array $fileList An array of filenames.
     * @return array An array of DOMElement assets.
     * @deprecated 1.4.0 Use HttpStack\Dom\Dom::appendAssets instead.
     */
    public function makeResources(array $fileList): array
    {
        error_log("Warning: Template::makeResources is deprecated. Use Dom::appendAssets instead.");
        return [];
    }

    /**
     * Returns the current DOMDocument instance from the internal Dom object.
     *
     * @return \DOMDocument
     */
    public function getDom(): \DOMDocument
    {
        return $this->dom->getDomDocument(); // Delegate to the Dom object
    }

    /**
     * Returns the current DOMXPath instance from the internal Dom object.
     *
     * @return \DOMXPath
     */
    public function getXPath(): \DOMXPath
    {
        return $this->dom->getXPath(); // Delegate to the Dom object
    }

    /**
     * Sets template variables.
     *
     * @param string|array $key The variable name or an associative array of variables.
     * @param string $value The value if $key is a string.
     * @return self
     */
    public function setVar(string|array $key, string $value = ''): self
    {
        if (is_array($key)) {
            $this->vars = array_merge($this->vars, $key);
        } else {
            $this->vars[$key] = $value;
        }
        return $this;
    }

    /**
     * Retrieves a template variable or all variables.
     *
     * @param string $key The variable name.
     * @return mixed The variable value, all variables, or null if not found.
     */
    public function getVar(string $key = ''): mixed
    {
        return $key === '' ? $this->vars : ($this->vars[$key] ?? null);
    }

    /**
     * Defines a callable function that can be used in the template.
     *
     * @param string $name The name of the function in the template (e.g., 'word_wrap').
     * @param callable $callback The PHP callable function.
     * @return self
     */
    public function define(string $name, callable $callback): self
    {
        $this->functions[$name] = $callback;
        return $this;
    }

    /**
     * Renders the template by replacing variables and executing functions.
     *
     * @param string $templateNamespace The namespace of the template to render.
     * @return string The rendered HTML content.
     * @throws Exception If the template namespace is not found or rendering fails.
     */
    public function render(string $templateNamespace = 'main'): string
    {
        if (!isset($this->files[$templateNamespace])) {
            throw new Exception("Template namespace '{$templateNamespace}' not loaded.");
        }

        // IMPORTANT: Ensure the Dom object is working with the correct template content
        // if setTemplate was called previously.
        // This line ensures that if setTemplate was used to switch the active template,
        // the Dom object is updated before rendering.
        if ($this->dom->getDomDocument()->saveHTML() !== $this->files[$templateNamespace]) {
             $this->dom = new Dom($this->files[$templateNamespace], $this->fileLoader);
             // Re-apply assets if the Dom object was re-instantiated
             $assets = $this->dataModel->getAssets();
             if (!empty($assets)) {
                 $this->dom->appendAssets($assets);
             }
        }


        // Get all data from the TemplateModel
        $data = $this->dataModel->getAll();
        // Merge with any directly set vars (though dataModel should be primary source)
        $data = array_merge($data, $this->vars);

        // Get the current HTML content from the Dom object (which holds the manipulated DOM)
        // This HTML now includes any appended assets or other DOM modifications.
        $html = $this->dom->saveHtml();

        // 1. Replace variables (e.g., {{varName}})
        $html = preg_replace_callback('/\{\{([a-zA-Z0-9_]+)\}\}/', function ($matches) use ($data) {
            $varName = $matches[1];
            return $data[$varName] ?? ''; // Return empty string if variable not found
        }, $html);

        // 2. Execute functions (e.g., {{func(arg1, "arg2")}})
        $html = preg_replace_callback('/\{\{([a-zA-Z0-9_]+)\((.*?)\)\}\}/', function ($matches) use ($data) {
            $functionName = $matches[1];
            $argsString = $matches[2];

            if (!isset($this->functions[$functionName]) || !is_callable($this->functions[$functionName])) {
                error_log("Warning: Undefined or non-callable function '{$functionName}' in template.");
                return ''; // Return empty string or an error message
            }

            $args = [];
            // Basic argument parsing: split by comma, trim, handle quotes/numbers
            // This is a simplified parser; a real templating engine would have a more robust one.
            if (!empty($argsString)) {
                // Split by comma, but handle quoted strings
                preg_match_all('/(?:[^,"]+|"[^"]*")+/', $argsString, $argMatches);
                foreach ($argMatches[0] as $arg) {
                    $arg = trim($arg);
                    if (str_starts_with($arg, '"') && str_ends_with($arg, '"')) {
                        $args[] = substr($arg, 1, -1); // Remove quotes for string literal
                    } elseif (is_numeric($arg)) {
                        $args[] = (strpos($arg, '.') !== false) ? (float)$arg : (int)$arg; // Numeric literal
                    } elseif (array_key_exists($arg, $data)) {
                        $args[] = $data[$arg]; // Variable from data context
                    } elseif ($arg === 'true') {
                        $args[] = true;
                    } elseif ($arg === 'false') {
                        $args[] = false;
                    } elseif ($arg === 'null') {
                        $args[] = null;
                    } else {
                        $args[] = $arg; // Treat as literal string if not found in data
                    }
                }
            }

            try {
                return call_user_func_array($this->functions[$functionName], $args);
            } catch (Exception $e) {
                error_log("Error executing template function '{$functionName}': " . $e->getMessage());
                return ''; // Return empty string on error
            }
        }, $html);

        return $html;
    }

    /**
     * Normalizes HTML structure (ensures doctype, html, head, body tags).
     * This method is now handled by the internal Dom object if needed.
     *
     * @param string $html The HTML string to normalize.
     * @return string The normalized HTML string.
     * @deprecated 1.4.0 Normalization is handled by Dom class on load.
     */
    public function normalizeHtml(string $html): string
    {
        error_log("Warning: Template::normalizeHtml is deprecated. Normalization is handled by Dom class on load.");
        // If you need to normalize an arbitrary HTML string, you could create a temporary Dom instance.
        // For now, return as is or throw an error.
        return $html;
    }
}
