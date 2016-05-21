<?php

/**
 * @copyright   Copyright (c) 2009-2012 NADEO (http://www.nadeo.com)
 * @license     http://www.gnu.org/licenses/lgpl.html LGPL License 3
 * @version     $Revision: $:
 * @author      $Author: $:
 * @date        $Date: $:
 */

namespace ManiaLivePlugins\eXpansion\Helpers\GbxReader;

class Map extends FileStructure
{

    public $uid;

    public $name;

    public $environment;

    public $mood;

    public $type;

    public $author;

    public $authorTime;

    public $goldTime;

    public $silverTime;

    public $bronzeTime;

    public $authorScore;

    public $displayCost;

    public $nbLaps;

    public $thumbnail;

    public $comments;

    public $playerModel = "defaultCar";

    public $fileName;

    final public static function fetch($fp)
    {
        $map = new self;
        self::ignore($fp, 3);
        $version = self::fetchShort($fp);
        $storage = array(
            self::fetchByte($fp),
            self::fetchByte($fp),
            self::fetchByte($fp),
            self::fetchByte($fp),
        );
        $classId = self::fetchLong($fp);
        if ($classId != 0x03043000)
            throw new \InvalidArgumentException('File is not a map');
        $headerSize = self::fetchLong($fp);
        $nbChunks = self::fetchLong($fp);
        $chunkInfos = array();
        for (; $nbChunks > 0; --$nbChunks)
            $chunkInfos[self::fetchLong($fp)] = self::fetchLong($fp);

        $wantedChunks = array(0x03043005, 0x03043007, 0x03043008);
        foreach ($chunkInfos as $chunkId => $chunkSize) {
            if (in_array($chunkId, $wantedChunks)) {
                $func = 'fetchChunk' . str_pad(dechex($chunkId & 0xfff), 3, '0', STR_PAD_LEFT);
                self::$func($map, $fp);
                $wantedChunks = array_diff($wantedChunks, array($chunkId));
                if (!$wantedChunks)
                    break;
            } else {
                self::ignore($fp, $chunkSize & 0x7fffffff);
            }
        }

        return $map;
    }

    final public static function check($filename)
    {
        $fp = fopen($filename, 'rb');

        $magic = self::fetchRaw($fp, 3);
        self::ignore($fp, 6);
        $classId = self::fetchLong($fp);

        fclose($fp);

        return $magic == 'GBX' && $classId == 0x03043000;
    }

    private static function fetchChunk005(Map $map, $fp)
    {
        $header = @simplexml_load_string(self::fetchString($fp));
        if (is_object($header)) {
            $map->uid = (string)$header->ident->attributes()->uid;

            $map->name = (string)$header->ident->attributes()->name;
            if (!$map->author) {
                $map->author = new Author();
                $map->author->login = (string)$header->ident->attributes()->author;
                $map->author->zone = (string)$header->ident->attributes()->authorzone;
            }

            $map->environment = (string)$header->desc->attributes()->envir;
            $map->mood = (string)$header->desc->attributes()->mood;
            $map->type = (string)$header->desc->attributes()->type;
            if ($map->type == 'Script')
                $map->type = (string)$header->desc->attributes()->maptype;
            $map->displayCost = (int)$header->desc->attributes()->displaycost;
            $map->nbLaps = (int)$header->desc->attributes()->nblaps;

            $map->authorTime = (int)$header->times->attributes()->authortime;
            $map->authorScore = (int)$header->times->attributes()->authorscore;
            $map->goldTime = (int)$header->times->attributes()->gold;
            $map->silverTime = (int)$header->times->attributes()->silver;
            $map->bronzeTime = (int)$header->times->attributes()->bronze;
            if (property_exists($header, "playermodel")) {
                $map->playerModel = (string)$header->playermodel->attributes()->id;

                if (trim($map->playerModel) == "") {
                    $map->playerModel = $map->environment . "Car";
                }
            } else {
                $map->playerModel = $map->environment . "Car";
            }
        }
    }

    private static function fetchChunk007(Map $map, $fp)
    {
        $haveThumbnail = self::fetchLong($fp);
        if ($haveThumbnail) {
            $thumbsize = self::fetchLong($fp);
            self::ignore($fp, strlen('<Thumbnail.jpg>'));
            if ($thumbsize) {
                if (!extension_loaded('gd'))
                    self::ignore($fp, $thumbsize);
                else {
                    $mirroredThumbnail = imagecreatefromstring(fread($fp, $thumbsize));
                    $width = imagesx($mirroredThumbnail);
                    $height = imagesy($mirroredThumbnail);
                    $map->thumbnail = imagecreatetruecolor($width, $height);
                    foreach (range($height - 1, 0) as $oldY => $newY)
                        imagecopy($map->thumbnail, $mirroredThumbnail, 0, $newY, 0, $oldY, $width, 1);
                }
            }
            self::ignore($fp, strlen('</Thumbnail.jpg>'));
            self::ignore($fp, strlen('<Comments>'));
            $map->comments = self::fetchString($fp);
            self::ignore($fp, strlen('</Comments>'));
        }
    }

    private static function fetchChunk008(Map $map, $fp)
    {
        $version = self::fetchLong($fp);
        $map->author = Author::fetch($fp);
    }

}
