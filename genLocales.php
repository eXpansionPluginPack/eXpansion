<?php

$pluginMessages = array();

$Directory = new RecursiveDirectoryIterator(__DIR__);
$Iterator = new RecursiveIteratorIterator($Directory);
$files = new RegexIterator($Iterator, "/.php/");

$pluginMessages = array();
$totalMessages = 0;

foreach ($files as $data2) {
    $filename = $data2->getPathname();
    if ($data2->getPath() == __DIR__) continue;

    $messageCount = 0;

    $plugin = str_replace(__DIR__ . DIRECTORY_SEPARATOR, "", $data2->getPath());
    $plugin = explode(DIRECTORY_SEPARATOR, $plugin);
    $plugin = $plugin[0];

    if (!array_key_exists($plugin, $pluginMessages))
        $pluginMessages[$plugin] = array();

    $data = file($filename);
    foreach ($data as $row) {
        if (strstr($row, "__(")) {
            preg_match('/__\(\"(([^\\\"]|\\.)*)\"/', $row, $matches);
            if (sizeof($matches) > 1) {
                $messageCount++;
                $pluginMessages[$plugin][$matches[1]] = $matches[1] . "\n" . $matches[1] . "\n\n";
            }

            preg_match('/__\(\'(([^\']|.)*?)\'\)/', $row, $matches);
            if (sizeof($matches) > 1) {
                $messageCount++;
                $pluginMessages[$plugin][$matches[1]] = $matches[1] . "\n" . $matches[1] . "\n\n";
            }
        }
        if (strstr($row, "exp_getMessage(")) {
            preg_match('/exp_getMessage\(\"(([^\\\"]|\\.)*)\"/', $row, $matches);
            if (sizeof($matches) > 1) {
                $messageCount++;
                $pluginMessages[$plugin][$matches[1]] = $matches[1] . "\n" . $matches[1] . "\n\n";
            }
            preg_match('/exp_getMessage\(\'(([^\']|.)*?)\'\)/', $row, $matches);
            if (sizeof($matches) > 1) {
                $messageCount++;
                $pluginMessages[$plugin][$matches[1]] = $matches[1] . "\n" . $matches[1] . "\n\n";
            }
        }
    }

    if (!is_dir(__DIR__ . "/" . $plugin . "/messages"))
        mkdir(__DIR__ . "/" . $plugin . "/messages", 755);

    echo "Plugin: " . $plugin . " -> generated messages count: " . $messageCount . "\n";
    $totalMessages += $messageCount;
    $string = implode("", $pluginMessages[$plugin]);
    file_put_contents($plugin . "/messages/en.txt", $string);
}

print "\nTotal Message count: " . $totalMessages;

?>
