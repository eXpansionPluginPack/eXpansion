<?php

namespace ManiaLivePlugins\eXpansion\Core;

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
            $message = str_replace('#' . $code . '#', $color, $message);
        return $message;
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
