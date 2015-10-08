<?php

if (PHP_SAPI != "cli") {
    die();
}

if (!function_exists("redCLI")) {
    function redCLI($string)
    {
        return "\033[0;31m" . $string . "\033[0m";
    }
}


$files = array_merge(["./src/Config/", "./src/data/", "./src/model/", "./src"], glob("./src/Config/*"), glob("./src/data/*"), glob("./src/model/*"), glob("./src/*"), glob("./*"));

$badFiles = [];
foreach ($files as $file) {
    $permissions = substr(sprintf('%o', fileperms($file)), -4);
    if ($permissions != "0755" && $permissions != "0644") {
        $badFiles[$file] = $permissions;
    }
    if (strpos("TheScript.php", $file) !== false) {
        if ($permissions != "0755") {
            $badFiles[$file] = $permissions;
        }
    }
}
if (!empty($badFiles)) {
    $return = "You need to give these files read, and write permissions, and TheScript.php needs execute permissions!
     After navigating to the folder containing TheScript.php, use:" . PHP_EOL .
        "`find * -type d -print0 | xargs -0 chmod 0755; find . -type f -print0 | xargs -0 chmod 0644; chmod 0755 TheScript.php;`" . PHP_EOL .
        "on the command line or set all directories to permission 0755, all of the files to 0644, and TheScript.php to 0755, " . PHP_EOL .
        "or contact your developer" . PHP_EOL .
        "Problem files: " . PHP_EOL;
    foreach ($badFiles as $file => $permission) {
        $return = $return . escapeshellarg(escapeshellcmd($file)) . ": " .
            escapeshellarg(escapeshellcmd($permission)) . PHP_EOL;
    }
    if (isset($shouldReturn)) {
        return $return;
    } else {
        echo redCLI($return);
    }
} else {
    if (isset($shouldReturn)) {
        return true;
    } else {
        echo "All file permissions OK!" . PHP_EOL;
    }
}