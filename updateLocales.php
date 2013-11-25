<?php

$iterator = new RecursiveDirectoryIterator(__DIR__);
$newMessagesFileName = "en2.txt";

foreach ($iterator as $dir) {
    $messagedir = $dir . DIRECTORY_SEPARATOR . "messages";
    // new message begins

    if (is_dir($messagedir)) {
	echo $dir . "\n";
	$newmessages = array(); // this hold the new messages
	$filedata = file($messagedir . DIRECTORY_SEPARATOR . $newMessagesFileName);
	foreach ($filedata as $message) {
	    $message = str_replace("\r", "", $message);
	    $message = str_replace("\n", "", $message);
	    if (trim($message) == "")
		continue;
	    if (!array_key_exists($message, $newmessages))
		$newmessages[$message] = $message;
	}
	print_r($newmessages);


	$localefiles = glob($messagedir . DIRECTORY_SEPARATOR . "*.txt");
	foreach ($localefiles as $localefile) {
	    $pluginMessages = array();
	    $difference = array();
	    $filename = explode(DIRECTORY_SEPARATOR, $localefile);
	    $filename = end($filename);
	    if ($filename == $newMessagesFileName)
		continue;

	    $filedata = file($localefile);

	    for ($i = 0; $i < count($filedata); $i +=3) {
		$message = $filedata[$i];
		$message = str_replace("\r", "", $message);
		$message = str_replace("\n", "", $message);
		if (trim($message) == "")
		    continue;

		if (!array_key_exists($message, $pluginMessages))
		    $pluginMessages[$message] = $message;
	    }

	    $old = 0;
	    $new = 0;
	    $outputBuffer = "";
	    foreach ($newmessages as $newmessage) {
		if (array_key_exists($newmessage, $pluginMessages)) {
		    $old++;
		} else {
		    $new++;
		    //$difference[$newmessage] = "*!*" . $newmessage;
		    $outputBuffer .= $newmessage . "\n" . "#translate# " . $newmessage . "\n\n";
		}
	    }
	    echo $filename . "  old: " . $old . " new:" . $new . "\n";
	    file_put_contents($localefile, $outputBuffer, FILE_APPEND);
	}
	break;
    }
}

