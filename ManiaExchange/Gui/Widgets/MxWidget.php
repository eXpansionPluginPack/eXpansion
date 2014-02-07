<?php

namespace ManiaLivePlugins\eXpansion\ManiaExchange\Gui\Widgets;

use ManiaLivePlugins\eXpansion\ManiaExchange\Config;

class MxWidget extends \ManiaLivePlugins\eXpansion\Gui\Windows\Widget {

    /**
     * @var \Maniaplanet\DedicatedServer\Connection
     */
    private $connection;

    /** @var \ManiaLive\Data\Storage */
    private $storage;
    private $_windowFrame;
    private $_mainWindow;
    private $_minButton;
    private $servername;
    private $btnVisit;
    private $btnAward;
    private $actionVisit;
    private $actionAward;

    protected function onConstruct() {
	parent::onConstruct();
	$this->setName("ManiaExchange Panel");
	$config = Config::getInstance();
	$login = $this->getRecipient();
	$script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Gui\Scripts\TrayWidget");
	$script->setParam('isMinimized', 'True');
	$script->setParam('autoCloseTimeout', '3500');
	$script->setParam('posXMin', -27);
	$script->setParam('posX', -27);
	$script->setParam('posXMax', -4);
	$this->registerScript($script);

	$dedicatedConfig = \ManiaLive\DedicatedApi\Config::getInstance();
	$this->connection = \Maniaplanet\DedicatedServer\Connection::factory($dedicatedConfig->host, $dedicatedConfig->port);

	$this->storage = \ManiaLive\Data\Storage::getInstance();

	$this->actionVisit = $this->createAction(array($this, 'Visit'));
	$this->actionAward = $this->createAction(array($this, 'Award'));

	$this->setScriptEvents(true);

	$this->_windowFrame = new \ManiaLive\Gui\Controls\Frame();
	$this->_windowFrame->setAlign("left", "center");
	$this->_windowFrame->setId("Frame");
	$this->_windowFrame->setScriptEvents(true);

	$this->_mainWindow = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround(60, 10);
	$this->_mainWindow->setId("MainWindow");
	$this->_windowFrame->addComponent($this->_mainWindow);

	$frame = new \ManiaLive\Gui\Controls\Frame();
	$frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
	$frame->setPosX(5);
	$this->_windowFrame->setPosition(0, -9);

	$this->btnVisit = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(6, 6);
	$this->btnVisit->setIcon("Icons64x64_1", "TrackInfo");
	$this->btnVisit->setDescription("Visit the maps Mania-exchange page", 80);
	$this->btnVisit->setAction($this->actionVisit);
	$frame->addComponent($this->btnVisit);

	$this->btnAward = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(6, 6);
	$this->btnAward->setIcon("Icons64x64_1", "OfficialRace");
	$this->btnAward->setDescription("Grant a Mania-exchange award to this map", 80);
	$this->btnAward->setAction($this->actionAward);
	$frame->addComponent($this->btnAward);

	$this->_windowFrame->addComponent($frame);

	$this->_minButton = new \ManiaLib\Gui\Elements\Quad(5, 5);
	$this->_minButton->setScriptEvents(true);
	$this->_minButton->setId("minimizeButton");
	$this->_minButton->setImage($config->iconMx, true);
	$this->_minButton->setAlign("left", "bottom");
	$this->_windowFrame->addComponent($this->_minButton);

	$this->addComponent($this->_windowFrame);
    }

    function onResize($oldX, $oldY) {
	parent::onResize($oldX, $oldY);
	$this->_windowFrame->setSize(35, 12);
	$this->_mainWindow->setSize(35, 6);
	$this->_minButton->setPosition(35 - 4, -2.5);
    }

    function Visit($login) {
	$mxId = $this->getMXid($login);
	if ($mxId === false)
	    return;

	$link = "http://tm.mania-exchange.com/tracks/view/" . $mxId;
	$this->connection->sendOpenLink($login, $link, 0);
    }

    function Award($login) {
	$mxId = $this->getMXid($login);
	if ($mxId === false)
	    return;
	$link = "http://tm.mania-exchange.com/awards/add/" . $mxId;
	$this->connection->sendOpenLink($login, $link, 0);
    }

    function getMXid($login) {
	$query = "http://api.mania-exchange.com/tm/tracks/" . $this->storage->currentMap->uId;

	$ch = curl_init($query);
	curl_setopt($ch, CURLOPT_USERAGENT, "Manialive/eXpansion MXapi [getter] ver 0.1");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$data = curl_exec($ch);
	$status = curl_getinfo($ch);
	curl_close($ch);

	if ($data === false) {
	    $this->connection->chatSendServerMessage('Error receving data from ManiaExchange!');
	    return false;
	}

	if ($status["http_code"] !== 200) {
	    if ($status["http_code"] == 301) {
		$this->connection->chatSendServerMessage('Map not found from ManiaExchange', $login);
		return false;
	    }

	    $this->connection->chatSendServerMessage(sprintf('MX returned http error code: %s', $status["http_code"]), $login);
	    return false;
	}

	$json = \json_decode($data);
	if ($json === false || sizeof($json) == 0) {
	    $this->connection->chatSendServerMessage('Map not found from ManiaExchange', $login);
	    return false;
	}

	return $json[0]->TrackID;
    }

    function destroy() {
	$this->clearComponents();
	parent::destroy();
    }

}

?>
