<?php

namespace ManiaLivePlugins\eXpansion\ForceSkin;

use ManiaLive\Utilities\Console;

/**
 * ForceSkin
 * A plugin to enable custom graphics to be forced on server
 *
 *  * @author Reaby
 */
class ForceSkin extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    /** @var Config */
    public function exp_onInit() {
	$this->config = Config::getInstance();
    }

    public function exp_onReady() {
	$this->forceSkins();
    }

    private function forceSkins() {
	try {
	    $this->console("Enabling forced skins");
	    $this->connection->setForcedSkins($this->getSkins());
	} catch (\Exception $e) {
	    $this->console("[eXp\ForceSkins] error while forcing a skin:" . $e->getMessage());
	    return;
	}
    }

    private function getSkins() {
	try {
	    $skin = new \Maniaplanet\DedicatedServer\Structures\Skin();
	    $skin->name = $this->config->name;
	    $skin->orig = "";
	    $skin->url = $this->config->skinUrl;
	    $skin->checksum = "";
	    return array($skin);
	} catch (\Exception $e) {

	    echo "error:" . $e->getMessage();
	    return array(new \Maniaplanet\DedicatedServer\Structures\Skin());
	}
    }

}
