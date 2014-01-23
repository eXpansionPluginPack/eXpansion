<?php

namespace ManiaLivePlugins\eXpansion\Core\Gui\Windows;

use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as OkButton;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Checkbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Ratiobutton;
use \ManiaLivePlugins\eXpansion\Core\Gui\Controls\InfoLine;

class InfoWindow extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {

    protected $frame;
    protected $ok;
    protected $actionOk;

    /** @var \Maniaplanet\DedicatedServer\Connection */
    private $connection;

    /** @var \ManiaLive\Data\Storage */
    private $storage;

    protected function onConstruct() {
	parent::onConstruct();
	$login = $this->getRecipient();

	$config = \ManiaLive\DedicatedApi\Config::getInstance();
	$this->connection = \Maniaplanet\DedicatedServer\Connection::factory($config->host, $config->port);
	$this->storage = \ManiaLive\Data\Storage::getInstance();

	$this->actionOk = $this->createAction(array($this, "Ok"));

	$this->frame = new \ManiaLive\Gui\Controls\Frame();
	$this->frame->setLayout(new \ManiaLib\Gui\Layouts\Column(120, 30));
	$this->mainFrame->addComponent($this->frame);
	$version = $this->connection->getVersion();

	$line = new Infoline("Server Login: " . $this->storage->serverLogin);
	$this->frame->addComponent($line);

	$line = new Infoline("Server version: " . $version->version);
	$this->frame->addComponent($line);
	$line = new Infoline("Server Build: " . $version->build);
	$this->frame->addComponent($line);
	$line = new Infoline("Server ApiVersio: " . $version->apiVersion);
	$this->frame->addComponent($line);

	$line = new Infoline("Server Titlepack: " . $version->titleId);
	$this->frame->addComponent($line);

        $line = new Infoline("Manialive version: " . \ManiaLive\Application\VERSION);
	$this->frame->addComponent($line);

	$line = new Infoline("eXpansion version: " . \ManiaLivePlugins\eXpansion\Core\Core::EXP_VERSION);
	$this->frame->addComponent($line);

	$line = new Infoline("");
	$this->frame->addComponent($line);

	$line = new Inputbox("join", 90);
	$line->setLabel("Join link:");
	$line->setText("maniaplanet://#join=" . $this->storage->serverLogin . "@" . $version->titleId);
	$this->frame->addComponent($line);

	$line = new Inputbox("fav", 90);
	$line->setLabel("Favourite link:");
	$line->setText("maniaplanet://#addfavourite=" . $this->storage->serverLogin . "@" . $version->titleId);
	$this->frame->addComponent($line);

	$this->ok = new OkButton();
	$this->ok->colorize("0d0");
	$this->ok->setText(__("Apply", $login));
	$this->ok->setAction($this->actionOk);
	$this->mainFrame->addComponent($this->ok);
    }

    function onResize($oldX, $oldY) {
	parent::onResize($oldX, $oldY);
	$this->ok->setPosition($this->sizeX - 20, -$this->sizeY + 6);
    }

    function Ok($login) {
	$this->erase($login);
    }

    function destroy() {
	$this->connection = null;
	$this->storage = null;
	$this->clearComponents();
	parent::destroy();
    }

}

?>
