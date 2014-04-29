<?php

if (!isset($argv[1])) {
    echo "Need to pass language you want to generate in argument !!";
    die(1);
}
$lang = $argv[1];


$zip = new ZipArchive();
if ($lang == "*") {
    $zip->open('translations.zip', ZipArchive::CREATE);
} else {
    $zip->open('translations_' . $lang . '.zip', ZipArchive::CREATE);
}


$dirs = scandir(".");
foreach ($dirs as $dir) {
    if ($lang == "*") {
	$options  = array();
	$zip->addGlob($dir . '/messages/*.txt', GLOB_BRACE, $options);
    } else {
	$localName = $dir . '/messages/' . $lang . '.txt';
	if (file_exists($localName)) {
	    echo $localName . "\n";
	    $zip->addFile($localName, $localName);
	}
    }
}

$zip->close();
