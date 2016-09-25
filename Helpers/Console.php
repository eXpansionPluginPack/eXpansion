<?php
namespace ManiaLivePlugins\eXpansion\Helpers;

use ManiaLib\Utils\Formatting;

class Console
{

    const black = "\e[0;30m";
    const b_black = "\e[30;1m";

    const red = "\e[0;31m";
    const b_red = "\e[31;1m";

    const green = "\e[0;32m";
    const b_green = "\e[32;1m";

    const yellow = "\e[0;33m";
    const b_yellow = "\e[33;1m";

    const blue = "\e[0;34m";
    const b_blue = "\e[34;1m";

    const magenta = "\e[0;35m";
    const b_magenta = "\e[35;1m";

    const cyan = "\e[0;36m";
    const b_cyan = "\e[36;1m";

    const white = "\e[0;37m";
    const b_white = "\e[37;1m";

    // define aliases for colors
    const error = "\e[37;1m\e[41m";
    const success = self::b_green;
    const normal = self::white;
    const bold = self::b_white;


    /**
     * displays a mesage with section or without without newline
     *
     * **example:**<br>
     * Console::out("your message\n", "info", Console::b_green);
     *
     * @param string $message
     * @param string|null $section
     * @param string $sectionColor
     * @return void
     */
    public static function out($message, $section = null, $sectionColor = self::b_yellow)
    {
        $message = print_r($message, true);
        $msg = "";
        if ($section) {
            $msg = self::white . "[ " . $sectionColor . $section . self::white . " ] " . $message . "\e[0m";
        } else {
            $msg = self::white . $message . self::white . "\e[0m";
        }

        self::eco($msg);
    }

    /**
     * display debug message at console.
     *
     * displays only if debug variable is defined at config without newline.
     * @see Config::$phmodDebug
     *
     * **example:**<br>
     * Console::debug("your message\n", "debug", Console::b_green);
     *
     * @param $message
     * @param null $section
     * @param string $sectionColor
     * @return void
     */
    public static function debug($message, $section = null, $sectionColor = self::b_yellow)
    {
        if (DEBUG) {
            self::out(Console::b_black . trim($message) . "\n", $section, $sectionColor);
        }
    }

    /**
     * function out_error
     *
     * displays an error message without newline
     *
     * **example:**<br>:
     * Console::out_error("your message\n");
     *
     * @param string $message
     */
    public static function out_error($message)
    {
        self::eco(self::white . "[ " . self::b_red . "Error" . "\e[0m" . self::white . " ] " . self::error . $message . "\e[0m\n");
    }

    /**
     * says `[ Ok ]` at console
     *
     * **example:**<br>:
     * Console::ok();
     *
     * @param bool $nl newline
     * @return void
     */
    public static function ok($nl = true)
    {
        self::eco(self::white . "[ " . self::b_green . "Ok" . self::white . " ]\n");
    }

    /**
     * says `[ Success ]` at console
     *
     * **example:**<br>:
     * Console::success();
     *
     * @param bool $nl newline
     */
    public static function success($nl = true)
    {
        self::eco(self::white . "[ " . self::b_green . "Success" . self::white . " ]\n");
    }

    /**
     * says `[ Fail ]` at console
     *
     * **example:**<br>:
     * Console::fail();
     *
     * @param bool $nl newline
     * @return void
     */
    public static function fail($nl = true)
    {
        self::eco(self::white . "[ " . self::b_red . "Fail" . self::white . " ]\n");
    }

    public static function eco($msg)
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $msg = preg_replace("/\e\[(\d{1,2}\;){0,1}\d{1,2}m/", "", $msg);
        }
        echo $msg;
    }


    public static function outTm($string, $return = false)
    {

        // echo $string . "\n";
        $array = array("000" => self::b_black,
            "100" => self::red,
            "010" => self::green,
            "110" => self::yellow,
            "001" => self::blue,
            "011" => self::magenta,
            "101" => self::cyan,
            "111" => self::white,
            "200" => self::b_red,
            "211" => self::red,
            "121" => self::green,
            "020" => self::b_green,
            "021" => self::green,
            "012" => self::cyan,
            "221" => self::b_yellow,
            "220" => self::b_yellow,
            "120" => self::green,
            "210" => self::yellow,
            "112" => self::b_blue,
            "002" => self::b_blue,
            "122" => self::b_cyan,
            "022" => self::b_cyan,
            "202" => self::b_magenta,
            "212" => self::b_magenta,
            "102" => self::magenta,
            "201" => self::b_red,
            "222" => self::b_white,
        );
        $matches = array();
        preg_match_all("/\\$[A-Fa-f0-9]{3}/", $string, $matches);
        $split = preg_split("/\\$[A-Fa-f0-9]{3}/", $string);

        //   print_r($split);
        //   print_r($matches);

        $out = "";
        foreach ($matches[0] as $i => $rgb) {
            $code = self::fix(hexdec($rgb[1]), hexdec($rgb[2]), hexdec($rgb[3]));
            if (array_key_exists($code, $array)) {
                $out .= $array[$code] . Formatting::stripStyles($split[$i + 1]);
            } else {
                $out .= self::white . Formatting::stripStyles($split[$i + 1]);
            }
            $end = Formatting::stripStyles($split[$i + 1]);
        }

        if ($end == Formatting::stripStyles(end($split))) {
            $end = "";
        }
        $out = self::white . Formatting::stripStyles(reset($split)) . $out . $end;

        if ($return) {
            return $out;
        }
        self::eco($out . "\n");
    }

    public static function fix($r, $g, $b)
    {
        $out = "111";
        // black/gray/white
        if ($r == $g && $g == $b && $b == $r) {
            if ($r >= 0 && $r < 5) {
                $out = "000";
            }
            if ($r >= 5 && $r < 13) {
                $out = "111";
            }
            if ($r >= 13 && $r <= 16) {
                $out = "222";
            }
        } else {
            $out = self::convert($r) . self::convert($g) . self::convert($b);
        }
        return $out;
    }

    public static function convert($number)
    {
        $out = "0";

        if ($number >= 9 && $number <= 16) {
            $out = "2";
        }
        if ($number >= 3 && $number < 9) {
            $out = "1";
        }
        if ($number >= 0 && $number < 3) {
            $out = "0";
        }
        return $out;
    }
}