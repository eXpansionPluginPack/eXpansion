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
	/**
         * Config class
         * @var \ManiaLivePlugins\eXpansion\Core\Config
         */
	private $config;
        /**
         * Colorparser class     
         * @var \ManiaLivePlugins\eXpansion\Core\Colorparser
         */
	private $colorParser;
	
	public function __construct() {
		$this->config = Config::getInstance();
		
		$this->colorParser = ColorParser::getInstance();
		$this->loadColors();
		
	}
	
	/**
         * LoadColors()
         * Loads the colors to colorparser class
         * 
         * @return void 
         */
	private function loadColors(){
		foreach($this->config as $name => $value){
			$names = explode("_", $name);
			$name = array_shift($names);
			if($name == "Colors"){
				$this->colorParser->registerCode(implode("_", $names), $value);
			}
		}
	}
	/**
         * parseColors(string $msg)
         * Returns the color tokenized string with parsed colors.
         * 
         * @param string $msg
         * @return string
         */
	public function parseColors($msg){
		return $this->colorParser->parseColors($msg);
	}
	
        /**
         * getColorParser()
         * Returns intance of colorParser;
         * 
         * @return \ManiaLivePlugins\eXpansion\Core\Colorparser
         */
        public function getColorParser() {
		return $this->colorParser;
	}
}

?>
