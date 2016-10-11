<?php

$pluginMessages = array();

$Directory = new RecursiveDirectoryIterator(__DIR__);
$Iterator = new RecursiveIteratorIterator($Directory);
$files = new RegexIterator($Iterator, "/.php/");

$pluginMessages = array();
$totalMessages = 0;

foreach ($files as $data2) {
    $filename = $data2->getPathname();
    if ($data2->getPath() == __DIR__) {
        continue;
    }

    if (strstr($data2->getPath(), ".") ) {
        continue;
    }
	
    $messageCount = 0;

    $plugin = str_replace(__DIR__ . DIRECTORY_SEPARATOR, "", $data2->getPath());
    $plugin = explode(DIRECTORY_SEPARATOR, $plugin);
    $plugin = $plugin[0];

    if (!array_key_exists($plugin, $pluginMessages)) {
        $pluginMessages[$plugin] = array();
    }

    $row = file_get_contents($filename);
    //foreach ($data as $row) {
        
			$matches = "";          
			preg_match_all('/__\((?:\'|")(?P<matches>.*?)(?:\'|")*\)/s', $row, $matches);
            if (sizeof($matches) > 1) {
				processMatch($plugin, $matches);
            }
        
  			preg_match_all('/eXpGetMessage\((?:\'|")(?P<matches>.*?)(?:\'|")*\)/s', $row, $matches);
	
            if (sizeof($matches) > 1) {
                processMatch($plugin, $matches);
            }        
       

    if (!is_dir(__DIR__ . "/" . $plugin . "/messages")) {
        mkdir(__DIR__ . "/" . $plugin . "/messages", 755);
    }

    echo $plugin . ":" . $filename . " -> generated messages count: " . $messageCount . "\n";
    $totalMessages += $messageCount;
    $string = implode("", $pluginMessages[$plugin]);
    file_put_contents($plugin . "/messages/diff.txt", $string);

}

function processMatch($plugin, $matches) {
	global $pluginMessages, $messageCount;	
	foreach ($matches['matches'] as $match) {
		
		$match = str_replace("\n", "", $match);
		$match = str_replace("\r", "", $match);
		$match = str_replace("\'", "¤", $match);
		$match = str_replace('\"', '½', $match);
		//print_r($match);
		
		preg_match_all('/(?:\'|")(?P<matches>.*?)(?:\'|")/s', '"'.$match.'"', $matches2);
		    if (sizeof($matches2) > 1) {
			$out = implode("", $matches2['matches']);
			$out = str_replace("¤", "'", $out);
			$out = str_replace("½", '"', $out);
			}
			
		$pluginMessages[$plugin][$out] = $out . "\n" . $out . "\n\n";
		$messageCount++;	
	 } 
	
}



foreach ($pluginMessages as $key => $messages) {
    echo "\n$key messages count: " . sizeof($messages);
}
print "\nTotal Message count: " . $totalMessages;




