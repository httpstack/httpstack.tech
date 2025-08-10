<?php

namespace HttpStack\IO;

use HttpStack\Exceptions\AppException;

/**
 * File Loader.
 *
 * Handles file loading, mapping directories, and finding files
 */
class FileLoader
{
    /**
     * Mapped directories.
     */
    protected array $mappedDirectories = [];

    /**
     * Default extension for files.
     */
    protected string $defaultExtension = 'php';

    protected string $defaultHtmlExtension = 'html';
    /**
     * File cache.
     */
    protected array $fileCache = [];

    /**
     * Create a new file loader.
     */
    public function __construct()
    {
        // Constructor
    }

    /**
     * Map a directory to a namespace.
     *
     * @return $this
     */
    public function mapDirectory(string $name, string $directory): self
    {
        // Ensure the directory exists
        if (!is_dir($directory)) {
            throw new AppException("Directory not found: {$directory}");
        }

        $this->mappedDirectories[$name] = rtrim($directory, '/');

        return $this;
    }

    /**
     * Get a mapped directory path.
     */
    public function getDirectory(string $name): ?string
    {
        return $this->mappedDirectories[$name] ?? null;
    }

    /**
     * Get all mapped directories.
     */
    public function getDirectories(): array
    {
        return $this->mappedDirectories;
    }

    /**
     * Find a file by name in mapped directories (searches subdirectories).
     */
    public function findFile(string $name, ?string $directory = null, ?string $extension = null): ?string
    {
        // Use default extension if not supplied
        $extension = $extension ?? $this->defaultExtension;

        // Only add extension if not already present
        if (!empty($extension) && !pathinfo($name, PATHINFO_EXTENSION)) {
            $name .= '.' . ltrim($extension, '.');
        }

        // Normalize the search name to use forward slashes for consistency
        $normalizedName = str_replace('\\', '/', $name);

        // If a directory is specified, search only there
        if ($directory !== null) {
            $dir = $this->mappedDirectories[$directory] ?? $directory;

            if (is_dir($dir)) {
                $files = $this->scanDirectoryForExtensions($dir, (array)$extension);
                foreach ($files as $file) {
                    // Normalize the file path relative to the base directory for comparison
                    $relativePath = str_replace('\\', '/', substr($file, strlen($dir) + 1));

                    // Check if the relative path ends with the normalized search name
                    if ($relativePath === $normalizedName || basename($file) === $normalizedName) {
                        return $file;
                    }
                }
            }
            return null;
        }

        // If no directory specified, search all mapped directories
        foreach ($this->mappedDirectories as $dir) {
            if (is_dir($dir)) {
                $files = $this->scanDirectoryForExtensions($dir, (array)$extension);
                foreach ($files as $file) {
                    // Normalize the file path relative to the base directory for comparison
                    $relativePath = str_replace('\\', '/', substr($file, strlen($dir) + 1));

                    // Check if the relative path ends with the normalized search name
                    if ($relativePath === $normalizedName || basename($file) === $normalizedName) {
                        return $file;
                    }
                }
            }
        }
        return null;
    }

    /**
     * Find all files by extension(s) in mapped directories.
     *
     * @param string|array $extensions A single extension string or an array of extensions.
     * @param string|array|null $directories Optional. A single directory name, an array of directory names, or null for all mapped directories.
     */
    public function findFilesByExtension(array $extensions, ?string $directory = null): array
    {
        $foundFiles = [];
        if ($directory !== null) {
            $dir = $this->mappedDirectories[$directory] ?? $directory;
            if (is_dir($dir)) {
                $foundFiles = array_merge($foundFiles, $this->scanDirectoryForExtensions($dir, $extensions));
            }
        } else {
            foreach ($this->mappedDirectories as $dir) {
                if (is_dir($dir)) {
                    $foundFiles = array_merge($foundFiles, $this->scanDirectoryForExtensions($dir, $extensions));
                }
            }
        }
        return array_unique($foundFiles);
    }


    /**
     * Scan a directory for files with specific extensions.
     *
     * @param string $directory The directory to scan.
     * @param array $extensions An array of extensions (e.g., ['php', 'html']).
     * @return array
     */
    protected function scanDirectoryForExtensions(string $directory, array $extensions): array
    {
        $foundFiles = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $fileExtension = pathinfo($file->getFilename(), PATHINFO_EXTENSION);
                if (in_array($fileExtension, $extensions)) {
                    $foundFiles[] = $file->getPathname();
                }
            }
        }
        return $foundFiles;
    }


    /**
     * Load a file's contents.
     *
     * @throws AppException
     */
    public function loadFile(string $path, bool $useCache = true): string
    {
        // Check the cache first
        if ($useCache && isset($this->fileCache[$path])) {
            return $this->fileCache[$path];
        }

        // Resolve the path if it's a mapped directory
        if (isset($this->mappedDirectories[$path])) {
            $path = $this->mappedDirectories[$path];
        }

        // If the path is not absolute, try to find the file
        if (!is_file($path)) {
            $foundPath = $this->findFile($path);

            if ($foundPath === null) {
                throw new AppException("File not found: {$path}");
            }

            $path = $foundPath;
        }

        // Load the file
        $content = include $path; // Be cautious: include returns the value of the included file, not its content

        if ($content === false) {
            throw new AppException("Failed to read file: {$path}");
        }

        // Cache the content
        if ($useCache) {
            $this->fileCache[$path] = $content;
        }

        return $content;
    }

    /**
     * Require a PHP file.
     *
     * @throws AppException
     */
    public function requireFile(string $path)
    {
        // Resolve the path if it's a mapped directory
        if (isset($this->mappedDirectories[$path])) {
            $path = $this->mappedDirectories[$path];
        }

        // If the path is not absolute, try to find the file
        if (!is_file($path)) {
            $foundPath = $this->findFile($path);

            if ($foundPath === null) {
                throw new AppException("File not found: {$path}");
            }

            $path = $foundPath;
        }

        return require $path;
    }
    public function parseJsonFile(string $path): array
    {
        if(is_file($path)){
            $content = file_get_contents($path);
            $data = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new AppException("Failed to parse JSON file: {$path}");
            }

            return $data;
        } else {
            throw new AppException("File not found: {$path}");
        }
    }
    /**
     * Include a PHP file.
     *
     * @throws AppException
     */
    public function includeFile(string $path)
    {
        // Resolve the path if it's a mapped directory
        if (isset($this->mappedDirectories[$path])) {
            $path = $this->mappedDirectories[$path];
        }

        // If the path is not absolute, try to find the file
        if (!is_file($path)) {
            $foundPath = $this->findFile($path);

            if ($foundPath === null) {
                throw new AppException("File not found: {$path}");
            }

            $path = $foundPath;
        }

        return include $path;
    }

    /**
     * Write content to a file.
     */
    public function writeFile(string $path, string $content): bool
    {
        // Ensure the directory exists
        $directory = dirname($path);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // Write the content
        $result = file_put_contents($path, $content);

        // Update the cache
        if ($result !== false) {
            $this->fileCache[$path] = $content;

            return true;
        }

        return false;
    }
    public function readFile(string $baseName){
        $path = $this->findFile($baseName, null, $this->defaultHtmlExtension);
        if ($path === null) {
            throw new AppException("File not found: {$baseName}");
        }
        return file_get_contents($path);
    }   
    /**
     * Set the default file extension.
     *
     * @return $this
     */
    public function setDefaultExtension(string $extension): self
    {
        $this->defaultExtension = ltrim($extension, '.');

        return $this;
    }

    /**
     * Get the default file extension.
     */
    public function getDefaultExtension(): string
    {
        return $this->defaultExtension;
    }

    /**
     * Clear the file cache.
     *
     * @return $this
     */
    public function clearCache(): self
    {
        $this->fileCache = [];

        return $this;
    }

    /**
     * Handle duplicate files.
     */
    public function handleDuplicates(array $files, string $strategy = 'first'): array
    {
        $result = [];
        $fileNames = [];

        foreach ($files as $file) {
            $name = basename($file);

            if (!isset($fileNames[$name])) {
                $fileNames[$name] = [];
            }

            $fileNames[$name][] = $file;
        }

        foreach ($fileNames as $name => $paths) {
            if (count($paths) === 1) {
                $result[] = $paths[0];
            } else {
                if ($strategy === 'first') {
                    $result[] = $paths[0];
                } elseif ($strategy === 'last') {
                    $result[] = end($paths);
                } elseif ($strategy === 'all') {
                    $result = array_merge($result, $paths);
                }
            }
        }

        return $result;
    }
}