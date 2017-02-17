<?php
namespace ManiaLivePlugins\eXpansion\Core;

use ManiaLib\Utils\Singleton;

/**
 * ColorParser - Singleton
 * Replaces specific text with color codes as configured
 *
 * @author oliverde8
 */
class ColorParser extends Singleton
{

    /**
     * Color codes
     *
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
        foreach ($this->codes as $code => $obj) {
            $key = $obj[1];
            $text = str_replace('#' . $code . '#', '$z$s' . $obj[0]->$key, $text);
        }

        return $text;
    }

    /**
     * getColor(string $value)
     * returns tm color code for a color token.
     *
     * @param string $value example: $value = $ColorParser->getColor("#record#");
     *
     * @return string color code, example "$fff"
     */
    public function getColor($value)
    {
        $color = "";
        $value = str_replace("#", "", $value);
        if (array_key_exists($value, $this->codes)) {
            $obj = $this->codes[$value];
            $key = $obj[1];
            $color = $obj[0]->$key;
        }

        return $color;
    }

    /**
     * Loads the default color codes that are defined in the config
     */
    public function __construct()
    {
        foreach (Config::getInstance() as $name => $value) {
            $key = $name;
            $names = explode("_", $name);
            $name = array_shift($names);
            if ($name == "Colors") {
                $this->registerCode(implode("_", $names), Config::getInstance(), $key);
            }
        }
    }

    /**
     * Register a new token and color code
     *
     * usage at plugin:
     * $this->registerCode("server", Config::getInstance(), "Color_server");
     *
     * @param String $token The key for the color code
     * @param Config $obj The configuration object that contains the variable to use(allows the color code to be
     *                      changed live)
     * @param String $key The key in the object that contains this color code
     */
    public function registerCode($token, $obj, $key)
    {
        $this->codes[$token] = array($obj, $key);
    }
}
