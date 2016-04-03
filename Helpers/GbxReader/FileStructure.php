<?php
/**
 * @copyright   Copyright (c) 2009-2012 NADEO (http://www.nadeo.com)
 * @license     http://www.gnu.org/licenses/lgpl.html LGPL License 3
 * @version     $Revision: $:
 * @author      $Author: $:
 * @date        $Date: $:
 */

namespace ManiaLivePlugins\eXpansion\Helpers\GbxReader;

abstract class FileStructure extends AbstractStructure
{
    final static function read($filename)
    {
        $fp = fopen($filename, 'rb');
        $structure = static::fetch($fp);
        fclose($fp);

        return $structure;
    }
}

?>
