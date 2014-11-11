<?php
/**
 * @copyright   Copyright (c) 2009-2012 NADEO (http://www.nadeo.com)
 * @license     http://www.gnu.org/licenses/lgpl.html LGPL License 3
 * @version     $Revision: $:
 * @author      $Author: $:
 * @date        $Date: $:
 */

namespace ManiaLivePlugins\eXpansion\Helpers\GbxReader;

class Author extends AbstractStructure
{
	public $login;
	public $nickname;
	public $zone;
	public $extra;
	
	final static function fetch($fp)
	{
		self::ignore($fp, 4);
		$author = new self;
		$author->login = self::fetchString($fp);
		$author->nickname = self::fetchString($fp);
		$author->zone = self::fetchString($fp);
		$author->extra = self::fetchString($fp);
		return $author;
	}
}

?>
