<?php

namespace ManiaLivePlugins\eXpansion\Core;
use ManiaLivePlugins\eXpansion\Core\Config;

/**
 * ColorParser - Singleton
 *
 * @author oliverde8
 */
class ColorParser extends \ManiaLib\Utils\Singleton {

    /**
     *  @type array
     */
    private $codes = array();

    /**
     * ParseColors(string $text)
     * Parses the colortokens within a string and returns new string with color codes.
     *
     * @param string $text
     * @return string
     */
    public function parseColors($text) {
        $message = $text;
        foreach ($this->codes as $code => $color)
            $message = str_replace('#' . $code . '#', '$z$s'.$color, $message);
        return $message;
    }

    /**
     * LoadColors()
     * Loads the colors to colorparser class
     * 
     * @return void 
     */
   public function __construct() {
	foreach (Config::getInstance() as $name => $value) {
	    $names = explode("_", $name);
	    $name = array_shift($names);
	    if ($name == "Colors") {
		$this->registerCode(implode("_", $names), $value);
	    }
	}
    }
    
    /**
     *
     * @param string $token
     * @param string $color
     */
    public function registerCode($token, $color) {
        $this->codes[$token] = $color;
    }

}

?>
