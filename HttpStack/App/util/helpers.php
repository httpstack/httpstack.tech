<?php
if (!function_exists("app")) {
    function app()
    {
        if (isset($GLOBALS["app"])) {
            return $GLOBALS["app"];
        }
    }
}
if (!function_exists(function: "consoleLog")) {
    function consoleLog(string $text): void
    {
        //echo "<script> console.log(\'$text\');</script>";
    }
}
if (!function_exists("box")) {
    function box(string $make = null, array $params = [])
    {
        // if u pass an abstract to box("myTool") it will return the resolved data
        // otherwise it just returns the container;
        return $make ?
            // fetch container
            // make abstract and return result
            app()->getContainer()->make($make, $params) :
            //fetch container
            app()->getContainer();
    }
}
if (!function_exists("appPath")) {
    function appPath(string|null $path): string|array
    {
        if ($path) {
            return app()->getSettings()["appPaths"][$path];
        }
        return app()->getSettings()["appPaths"];
    }
}
if (!function_exists("config")) {
    function config(string|null $key): string|array
    {
        if ($key) {
            return app()->getSettings()[$key];
        }
        return app()->getSettings();
    }
}
if (!function_exists("flog")) {
    function flog(string $type, mixed $message, string $level = "info")
    {
        //log to file
        $file = "/" . $type . ".log";
        $logFile = appPath("logs") . $file;
        $date = date("Y-m-d H:i:s");
        if (is_array($message) || is_object($message)) {
            $message = json_encode($message);
        }
        //format the messege to look better in file with sprintf
        $message = sprintf("[%s] [%s] %s", $date, $level, $message);
        //write to log file
        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile), 0755, true);
        }
        file_put_contents($logFile, $message . "\n", FILE_APPEND);
        // Optionally, you can also log to the console for debugging
        if (app()->debug) {
            echo "<script>console.log('[$date] [$level] $message');</script>";
        }
        // You can also log to the PHP error log
        error_log($message);
        // If you want to log to a specific file, uncomment the line below
        file_put_contents($logFile, "[$date] [$level] $message\n", FILE_APPEND);
    }
}
function dd(mixed $data)
{
    $debug = app()->debug;

    if ($debug) {

        echo "<hr/><pre>";
        print_r($data);
        echo "</pre><hr/>";
    }
}
