<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups\types;

/**
 * Description of absChecker
 *
 * @author oliverde8
 * @abstract
 */
abstract class absChecker extends \ManiaLib\Utils\Singleton
{

    abstract public function check($data);

    abstract public function getErrorMsg();
}
