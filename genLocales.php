<?php

$pluginMessages = array();
foreach (scandir(__DIR__) as $filename) {
    if ($filename == "." || $filename == "..")
        continue;
    if (!is_dir($filename))
        return;
    $pluginMessages[$filename] = array();
    $messageCount = 0;
    foreach (glob("$filename/*.php") as $source) {
        $data = file($source);


        foreach ($data as $row) {
            if (strstr($row, "__(")) {
                preg_match("/__\((\"|\')(.*?)(\"|\')/", $row, $matches);
                if (sizeof($matches) > 2) {
                    $messageCount++;
                    $pluginMessages[$filename][$matches[2]] = $matches[2] . "\nfill in here\n\n";
                }
            }
            if (strstr($row, "exp_getMessage(")) {
                preg_match("/exp_getMessage\((\"|\')(.*?)(\"|\')/", $row, $matches);
                if (sizeof($matches) > 2) {
                    $messageCount++;
                    $pluginMessages[$filename][$matches[2]] = $matches[2] . "\nfill in here\n\n";
                }
            }
        }
        if (!is_dir($filename . "/messages"))
            mkdir($filename . "/messages", 775);
    }
    if ($filename !== ".git") {
        echo "Plugin: " . $filename . " -> generated messages count: " . $messageCount . "\n";
        $string = implode("", $pluginMessages[$filename]);

        file_put_contents($filename . "/messages/en.txt", $string);
    }
}
?>
