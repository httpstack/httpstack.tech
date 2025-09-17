<?php
define("DOC_ROOT", "/var/www/html");
define("APP_ROOT", DOC_ROOT . "/HttpStack/App");
define("BASE_URI", "http://localhost/");


//make this into a directroy loop so it get gets all libs from 
//an array of folders in the file loadder or something
require_once(DOC_ROOT . "/HttpStack/App/util/helpers.php");



/**
 * Recursively includes all .php files in a given directory.
 * 
 * This is useful for loading procedural helper files that don't follow class-based
 * autoloading standards. It's generally not recommended for loading classes,
 * as `spl_autoload_register` provides more efficient, on-demand loading.
 *
 * @param string $dir The directory to scan.
 */
function require_all_files_in(string $dir): void
{
    /**
     * Normalize a file path by removing redundant separators.
     *
     * @param string $path The file path to normalize.
     * @return string The normalized file path.
     */

    $realDir = realpath($dir);
    if (!$realDir || !is_dir($realDir)) {
        return;
    }

    // Use glob to find all php files in the current directory
    foreach (glob($realDir . '/*.php') as $file) {
        require_once $file;
    }

    // Use glob to find all subdirectories and recurse into them
    foreach (glob($realDir . '/*', GLOB_ONLYDIR) as $subdir) {
        require_all_files_in($subdir);
    }
}

// An array of base directories for autoloading.
// The autoloader will look for classes in these directories, maintaining the namespace structure.
$autoload_paths = [
    DOC_ROOT,
    // You can add more paths here, for example:
    // DOC_ROOT . '/libs',
];


spl_autoload_register(function ($className) {
    // Convert namespace backslashes to directory separators
    $class_file = str_replace('\\', '/', $className) . '.php';

    $file = DOC_ROOT . '/' . $class_file;

    // Normalize the path
    $file = str_replace(['\\', '//'], '/', $file);
    $file = normalize_path($file);
    if (file_exists($file)) {
        require_once $file;
        return; // Stop searching after the class is found
    }

    // If the class is not found in any of the paths:
    error_log("Autoload failed for class: $className"); // Log the error
    // or
    // throw new Exception("Class not found: $className"); // Throw an exception
});
