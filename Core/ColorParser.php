<?php

namespace ManiaLivePlugins\eXpansion\Core;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ColorParser
 *
 * @author oliverde8
 */
class ColorParser extends \ManiaLib\Utils\Singleton{
	
	private $codes = array();
	
	public function parseColors($text) {
		$message = $text;
		foreach ($this->codes as $code => $color)
			$message = str_replace('%'.$code.'%', $color, $message);
		return $message;
	}
	
	public function registerCode($code, $color){
		$this->codes[$code] = $color;
	}
	
}

?>
