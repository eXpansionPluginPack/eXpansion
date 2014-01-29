<?php

namespace ManiaLivePlugins\eXpansion\Core\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Core\Core;

/**
 * Description of widget_netstat
 *
 * @author Petri
 */
class Widget_Netstat extends \ManiaLivePlugins\eXpansion\Gui\Windows\Widget {

    private $frame;

    /** @var \ManiaLive\Data\Storage */
    private $storage;

    protected function onConstruct() {
	parent::onConstruct();
	$this->setName("Network Status Widget");
	$this->storage = \ManiaLive\Data\Storage::getInstance();

	$this->frame = new \ManiaLive\Gui\Controls\Frame(0, -3);
	$this->frame->setLayout(new \ManiaLib\Gui\Layouts\Column());
	$this->addComponent($this->frame);

	$label = new \ManiaLib\Gui\Elements\Label(60);
	$label->setAlign("left", "top");
	$label->setPosX(12);
	$label->setText('$fffNetwork Status');
	$this->addComponent($label);

	foreach (Core::$netStat as $login => $stat) {
	    if ($stat->updateLatency >= 150 || $stat->updatePeriod >= 400) {
		$frame = new \ManiaLive\Gui\Controls\Frame();
		$frame->setLayout(new \ManiaLib\Gui\Layouts\Line());

		$player = new \ManiaLib\Gui\Elements\Label(35, 6);
		if (isset($this->storage->players[$login])) {
		    $player->setText($this->storage->players[$login]->nickName);
		} else {
		    $player->setText($login);
		}
		$frame->addComponent($player);

		$status = new \ManiaLib\Gui\Elements\Label(16,6);
		$color = '$f00';
		if ($stat->updateLatency < 300) {
		    $color = '$0f0';
		}
		$status->setText($color . $stat->updateLatency . "ms");
		$frame->addComponent($status);

		$status = new \ManiaLib\Gui\Elements\Label();
		$color = '$f00';
		if ($stat->updatePeriod < 600) {
		    $color = '$0f0';
		}
		$status->setText($color . $stat->updatePeriod );
		$frame->addComponent($status);
		
		$this->frame->addComponent($frame);
	    }
	}
    }

}

?>
