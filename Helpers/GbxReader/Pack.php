<?php
/**
 * @copyright   Copyright (c) 2009-2012 NADEO (http://www.nadeo.com)
 * @license     http://www.gnu.org/licenses/lgpl.html LGPL License 3
 * @version     $Revision: $:
 * @author      $Author: $:
 * @date        $Date: $:
 */

namespace ManiaLivePlugins\eXpansion\Helpers\GbxReader;

class Pack extends FileStructure
{
    public $type;
    public $uid;
    public $author;
    public $manialink;
    public $creationDate;
    public $description;
    public $creationBuildInfo;
    public $includedPacks = array();
    public $checksum;
    public $flags;

    final public static function fetch($fp)
    {
        $pack = new self;
        self::ignore($fp, 8);
        $version = self::fetchLong($fp);
        $pack->checksum = self::fetchChecksum($fp);
        $pack->flags = self::fetchLong($fp);
        $pack->author = Author::fetch($fp);
        $pack->manialink = self::fetchString($fp);
        $pack->creationDate = self::fetchDate($fp);
        $pack->description = self::fetchString($fp);
        if ($version >= 12) {
            $header = self::fetchString($fp);
            $pack->uid = self::fetchString($fp);
        }
        $pack->type = self::fetchString($fp);
        $pack->creationBuildInfo = self::fetchString($fp);
        self::ignore($fp, 16);

        $nbIncludedPacks = self::fetchLong($fp);
        for (; $nbIncludedPacks > 0; --$nbIncludedPacks) {
            $pack->includedPacks[] = IncludedPack::fetch($fp);
        }

        return $pack;
    }
}
