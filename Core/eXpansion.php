<?php

namespace ManiaLivePlugins\eXpansion\Core;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of eXpansion
 *
 * @author oliverde8
 */
class eXpansion extends \ManiaLib\Utils\Singleton{
	
	private $config;
	private $colorParser;
	
	public function __construct() {
		$this->config = Config::getInstance();
		
		$this->colorParser = ColorParser::getInstance();
		$this->loadColors();
		
	}
	
	
	private function loadColors(){
		foreach($this->config as $name => $value){
			$names = explode("_", $name);
			$name = array_shift($names);
			if($name == "Colors"){
				$this->colorParser->registerCode(implode("_", $names), $value);
			}
		}
	}
	
	public function parseColors($msg){
		return $this->colorParser->parseColors($msg);
	}
	
	public function getColorParser() {
		return $this->colorParser;
	}
}

?>
