<?php

$iterator = new RecursiveDirectoryIterator(__DIR__);
$newMessagesFileName = "diff.txt";

foreach ($iterator as $dir) {
	$messagedir = $dir . DIRECTORY_SEPARATOR . "messages";
	// new message begins

	if (is_dir($messagedir)) {
		echo $dir . "\n";
		$newmessages = array(); // this hold the new messages
		$filedata = file($messagedir . DIRECTORY_SEPARATOR . $newMessagesFileName);
		foreach ($filedata as $message) {
			$message = fixMessage($message);
			if (trim($message) == "")
				continue;
			if (!array_key_exists($message, $newmessages))
				$newmessages[$message] = $message;
		}

		$localefiles = glob($messagedir . DIRECTORY_SEPARATOR . "*.txt");
		foreach ($localefiles as $localefile) {
			$pluginMessages = array();
			$difference = array();
			$filename = explode(DIRECTORY_SEPARATOR, $localefile);
			$filename = end($filename);
			if ($filename == $newMessagesFileName)
				continue;

			$filedata = file($localefile, FILE_IGNORE_NEW_LINES);

			for ($i = 0; $i < count($filedata); $i +=3) {
				$message = fixMessage($filedata[$i]);
				if (trim($message) == "")
					continue;

				$translation = fixMessage($filedata[$i + 1]);

				if (!array_key_exists($message, $pluginMessages))
					$pluginMessages[$message] = $translation;
			}

			$old = 0;
			$new = 0;
			$lastRow = end($filedata);
			$lastRow = str_replace("\r", "", $lastRow);
			$lastRow = str_replace("\n", "", $lastRow);

			$outputBuffer = "";
			if (trim($lastRow) != "")
				$outBuffer = "\r\n";

			foreach ($newmessages as $newmessage) {
				$newmessage = fixMessage($newmessage);
				
				if (array_key_exists($newmessage, $pluginMessages)) {
					$old++;
				}
				else {
					$new++;
					//$difference[$newmessage] = "*!*" . $newmessage;
					if ($filename == "en.txt") {
						$pluginMessages[$newmessage] = $newmessage;
					}
					else {
						$pluginMessages[$newmessage] = "#translate# " . $newmessage;
					}
				}
			}

			echo $filename . "  old: " . $old . " new:" . $new . "\n";
			// save file
			foreach ($pluginMessages as $key => $value) {
				$outputBuffer .= $key . "\r\n" . $value . "\r\n\r\n";
			}
			file_put_contents($localefile, $outputBuffer);
		}
		echo "Removing the temporarily diff file..\n";
		unlink($messagedir . DIRECTORY_SEPARATOR . $newMessagesFileName);
	}
}

function fixMessage($message)
{
	$message = rtrim($message,"\r\n");
	$message = str_replace("\'", "'", $message);
	$message = str_replace('\"', '"', $message);
	return $message;
}
