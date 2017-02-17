<?php
namespace ManiaLivePlugins\eXpansion\AutoUpdate;

use ManiaLib\Utils\Singleton;

/**
 * Description of Config
 *
 * @author Petri
 */
class Config extends Singleton
{
    public $autoCheckUpdates = false;
    public $useGit = true;
    public $branchName = "nightly";
}
