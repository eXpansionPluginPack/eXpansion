<?php

namespace ManiaLivePlugins\eXpansion\Core;

/**
 * ColorParser - Singleton
 * Replaces specific text with color codes as configured
 *
 * @author oliverde8
 */
class ColorParser extends \ManiaLib\Utils\Singleton
{

    /**
     * @type array
     */
    private $codes = array();

    /**
     * ParseColors(string $text)
     * Parses the colortokens within a string and returns new string with color codes.
     *
     * @param string $text
     *
     * @return string
     */
    public function parseColors($text)
    {
        $message = $text;
        foreach ($this->codes as $code => $obj) {
            $key     = $obj[1];
            $message = str_replace('#' . $code . '#', '$z$s' . $obj[0]->$key, $message);
        }

        return $message;
    }

    /**
     * Loads the default color codes that are defined in the config
     */
    public function __construct()
    {
        foreach (Config::getInstance() as $name => $value) {
            $key   = $name;
            $names = explode("_", $name);
            $name  = array_shift($names);
            if ($name == "Colors") {
                $this->registerCode(implode("_", $names), Config::getInstance(), $key);
            }
        }
    }

    /**
     * Register a new color code
     *
     * @param string $token The key for the color code
     * @param string $obj The object that contains the variable to use(allows the color code to be changed live)
     * @param string $key The key in the object fhat contains this color code
     */
    public function registerCode($token, $obj, $key)
    {
        $this->codes[$token] = array($obj, $key);
    }

}

?>
