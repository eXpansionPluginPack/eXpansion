<?php
/**
 * @copyright   Copyright (c) 2009-2012 NADEO (http://www.nadeo.com)
 * @license     http://www.gnu.org/licenses/lgpl.html LGPL License 3
 * @version     $Revision: $:
 * @author      $Author: $:
 * @date        $Date: $:
 */

namespace ManiaLivePlugins\eXpansion\Helpers\GbxReader;

class IncludedPack extends AbstractStructure
{
    public $name;
    public $author;
    public $manialink;
    public $creationDate;
    public $checksum;

    final static function fetch($fp)
    {
        $includedPack = new self;
        $includedPack->checksum = self::fetchChecksum($fp);
        $includedPack->name = self::fetchString($fp);
        $includedPack->author = Author::fetch($fp);
        $includedPack->manialink = self::fetchString($fp);
        $includedPack->creationDate = self::fetchChecksum($fp);

        return $includedPack;
    }
}

?>
