<?php

/*
 * Copyright (C) 2014 Reaby
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace ManiaLivePlugins\eXpansion\Helpers;

/**
 * Description of Maniascript
 *
 * @author Reaby
 */
class Maniascript
{

    /**
     *    converts object, string or array to maniascript array
     *
     * @param mixed $data
     */
    public static function stringifyAsList($data)
    {

        if (is_object($data)) {
            $data = (array)$data;
        }
        if (!is_array($data)) {

            return '[' . self::convertType($data) . ']';
        }
        $returnBuffer = "[";
        $returnBuffer .= implode(",", array_map(array(__CLASS__, "convertType"), $data));
        $returnBuffer .= ']';

        return $returnBuffer;
    }

    /**
     *    converts object, string or array to maniascript array
     *
     * @todo implement the converter
     *
     * @param mixed $data
     */
    public static function stringifyAsArray($data)
    {
        throw new Exception("not implemented yet");
    }

    /**
     * Converts mixed php variable types to maniascript equilable
     * supported php->maniascript types:
     *
     * String, Boolean, Integer, Float, Null
     *
     * @param  String|Boolean|Integer|Float|Null $var
     *
     * @return string|numeric
     */
    public static function convertType($var)
    {
        if (is_null($var)) {
            return 'Null';
        }
        if (is_float($var)) {
            return strval(number_format((float)$var, 2, '.', ''));
        }
        if (is_int($var)) {
            return $var;
        }
        if (is_bool($var)) {
            if ($var) {
                return 'True';
            }

            return 'False';
        }

        if (is_string($var)) {
            return '"' . addslashes(self::fixString($var)) . '"';
        }
    }

    /**
     * Cleans the string for manialink or maniascript purposes.
     *
     * @param string $string The string to clean
     * @param bool $multiline
     * @return string cleaned up string
     */
    public static function fixString($string, $multiline = false)
    {

        $out = str_replace("\r", '', $string);
        if (!$multiline) {
            $out = str_replace("\n", '', $out);
        }
        $out = str_replace('"', "'", $out);
        $out = str_replace('\\', '\\\\', $out);
        $out = str_replace('-', '–', $out);

        return $out;
    }

    /**
     * convert php numeric value to maniascript Real
     *
     * @param numeric $var
     *
     * @return int|float
     */
    public static function getReal($var)
    {
        if (is_numeric($var)) {
            return strval(number_format((float)$var, 2, '.', ''));
        }
    }

    /**
     * convert php boolean to maniascript boolan
     *
     * @param bool $boolean
     *
     * @return string
     */
    public static function getBoolean($boolean)
    {
        if ($boolean) {
            return "True";
        }

        return "False";
    }

}
