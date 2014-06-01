<?php

namespace ManiaLivePlugins\eXpansion\AutoUpdate;

/**
 * Description of Config
 *
 * @author Petri
 */
class Config extends \ManiaLib\Utils\Singleton{
    public $autoCheckUpdates = false;
    public $useGit = true;
    public $branchName = "master";
}
