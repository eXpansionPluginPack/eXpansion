<?php
$iterator = new RecursiveDirectoryIterator(__DIR__);

foreach ($iterator as $dir) {
    $messagedir = $dir . DIRECTORY_SEPARATOR . "messages";
    $localefiles = glob($messagedir . DIRECTORY_SEPARATOR . "*.txt");

    foreach ($localefiles as $localefile) {
        $buffer = "";
        $filedata = file($localefile);

        foreach ($filedata as $message) {
            // remove newline tags
            $message = str_replace("\r", "", $message);
            $message = str_replace("\n", "", $message);
            // replace escaped string starter literals
            $message = str_replace("\'", "'", $message);
            $message = str_replace('\"', '"', $message);
            // add to buffer with new newline
            $buffer .= $message . "\n";
        }
        echo "Fixing:" . $localefile . "\n";
        file_put_contents($localefile, $buffer, LOCK_EX);
    }
}
