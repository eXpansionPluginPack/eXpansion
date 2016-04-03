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

    public abstract function check($data);

    public abstract function getErrorMsg();
}

?>
