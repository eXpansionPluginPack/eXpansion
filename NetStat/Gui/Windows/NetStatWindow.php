<?php

namespace ManiaLivePlugins\eXpansion\Netstat\Gui\Windows;

use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Layouts\Column;
use ManiaLib\Gui\Layouts\Line;
use ManiaLive\Data\Storage;
use ManiaLive\Event\Dispatcher;
use ManiaLive\Features\Tick\Event as TickEvent;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\Core\Core;
use ManiaLivePlugins\eXpansion\Gui\Windows\Window;

/**
 * Description of widget_netstat
 *
 * @author Petri
 */
class NetStatWindow extends Window
{

    protected $frame;

    /** @var Storage */
    private $storage;
    private $lastUpdate = 0;

    protected function onConstruct()
    {
	parent::onConstruct();
	Dispatcher::register(TickEvent::getClass(), $this);

	$this->setTitle("Network Lag");

	$this->storage = Storage::getInstance();

	$this->frame = new Frame(5, -2);
	$this->frame->setLayout(new \ManiaLib\Gui\Layouts\VerticalFlow(50, 100));
	$this->addComponent($this->frame);
	$this->lastUpdate = time();
    }

    public function onTick()
    {
	if ($this->lastUpdate + 5 < time()) {
	    $this->redraw($this->getRecipient());
	    $this->lastUpdate = time();
	}
    }

    protected function onDraw()
    {
	parent::onDraw();
	$this->frame->clearComponents();

	$netstat = \ManiaLivePlugins\eXpansion\NetStat\NetStat::$netStat;
	
	\ManiaLivePlugins\eXpansion\Helpers\ArrayOfObj::asortAsc($netstat, "login");
	$index = 0;

	foreach ($netstat as $login => $stat) {	    
	    if ($index > 50) {
		continue;
	    }
	    $line = new Frame();
	    $line->setSize(70, 4);
	    $line->setLayout(new Line());

	    $player = new Label(35, 6);
	    if (isset($this->storage->players[$login])) {
		$player->setText(($index + 1) . ". " . $this->storage->players[$login]->nickName);
	    } else {
		$player->setText(($index + 1) . ". " . $login);
	    }
	    $line->addComponent($player);

	    $status = new Label(16, 6);
	    $color = '$f00';
	    if ($stat->updateLatency < 300) {
		$color = '$f90';
	    }

	    if ($stat->updateLatency < 600) {
		$color = '$0f0';
	    }

	    $status->setText($color . $stat->updateLatency . "ms");
	    $line->addComponent($status);

	    $status = new Label(20, 6);
	    $status->setText('$fff' . $stat->ipAddress);
	    //$line->addComponent($status);

	    $kick = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();
	    $kick->setScale(0.5);
	    $kick->setPosY(-1.5);
	    $kick->setText(__('Kick', $login));
	    $action = $this->createAction(array($this, 'kick'), $login);
	    $kick->setAction($action);
	    $line->addComponent($kick);

	    $this->frame->addComponent($line);
	    $index++;
	}
    }

    public function kick($login, $kickLogin) {
	print $login . " -> ".  $kickLogin;
	$adminGroup = \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::getInstance();
	$adminGroup->adminCmd($login, "kick " .$kickLogin. " \"Network Lag was too big\"");
    }

    public function destroy()
    {
	parent::destroy();
	Dispatcher::unregister(TickEvent::getClass(), $this);
    }

}

?>
