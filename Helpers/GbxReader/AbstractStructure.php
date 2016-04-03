<?php
/**
 * @copyright   Copyright (c) 2009-2012 NADEO (http://www.nadeo.com)
 * @license     http://www.gnu.org/licenses/lgpl.html LGPL License 3
 * @version     $Revision: $:
 * @author      $Author: $:
 * @date        $Date: $:
 */

namespace ManiaLivePlugins\eXpansion\Helpers\GbxReader;

abstract class AbstractStructure
{
    static private $lookbackStrings = array();

    static function fetch($fp)
    {
        throw new \LogicException('This method has to be defined in subclasses');
    }

    final static function ignore($fp, $length)
    {
        fread($fp, $length);
    }

    final static function fetchRaw($fp, $length)
    {
        return fread($fp, $length);
    }

    final static function fetchByte($fp)
    {
        $byte = unpack('C', fread($fp, 1));
        return $byte[1];
    }

    final static function fetchShort($fp)
    {
        $short = unpack('v', fread($fp, 2));
        return $short[1];
    }

    final static function fetchLong($fp)
    {
        $long = unpack('V', fread($fp, 4));
        return $long[1];
    }

    final static function fetchFloat($fp)
    {
        $float = unpack('f', fread($fp, 4));
        return $float[1];
    }

    final static function fetchFloat2($fp)
    {
        return array(self::fetchFloat($fp), self::fetchFloat($fp));
    }

    final static function fetchFloat3($fp)
    {
        return array(self::fetchFloat($fp), self::fetchFloat($fp), self::fetchFloat($fp));
    }

    final static function fetchChecksum($fp)
    {
        $checksum = unpack('H64', fread($fp, 32));
        return $checksum[1];
    }

    final static function fetchString($fp)
    {
        $length = self::fetchLong($fp);
        return $length ? fread($fp, $length) : '';
    }

    final static function fetchLookbackString($fp)
    {
        // Ignoring version for first lookback string
        if (empty(self::$lookbackStrings))
            self::ignore($fp, 4);

        $index = self::fetchLong($fp) & 0x3fffffff;
        if ($index)
            return self::$lookbackStrings[$index - 1];

        self::$lookbackStrings[] = $string = self::fetchString($fp);
        return $string;
    }

    final static function clearLookbackStrings()
    {
        self::$lookbackStrings = array();
    }

    final static function fetchDate($fp)
    {
        $date = unpack('v4', fread($fp, 8));
        // create an int64 string representing the number of 100-nanoseconds since 01/01/1601 00:00:00
        $date = array_reduce(array_reverse($date), function (&$res, $value) {
            return bcadd($value, bcmul($res, '65536'));
        }, '0');
        // convert it to a number of seconds
        $date = bcdiv($date, '10000000');
        // substract the difference with EPOCH to get a Unix timestamp
        $date = bcsub($date, '11644473600');
        // return the DateTime object
        return new \DateTime('@' . $date);
    }
}

?>
