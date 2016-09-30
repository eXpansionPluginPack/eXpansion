<?php

namespace ManiaLivePlugins\eXpansion\Core\Gui\Windows;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as OkButton;
use ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use ManiaLivePlugins\eXpansion\Helpers\Helper;
use ManiaLivePlugins\eXpansion\ServerStatistics\Gui\Controls\InfoLine;

class InfoWindow extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

    public static $statsAction = -1;

    protected $frame;
    protected $ok;
    protected $stats;
    protected $actionOk;
    protected $button_cpJoin;
    protected $button_addFav;

    /** @var \Maniaplanet\DedicatedServer\Connection */
    protected $connection;

    /** @var \ManiaLive\Data\Storage */
    protected $storage;

    protected function onConstruct()
    {
        parent::onConstruct();
        $login = $this->getRecipient();

        $this->registerScript(new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Core/Gui/Scripts/copyServerLink"));

        $config = \ManiaLive\DedicatedApi\Config::getInstance();
        $this->connection = \ManiaLivePlugins\eXpansion\Helpers\Singletons::getInstance()->getDediConnection();
        $this->storage = \ManiaLive\Data\Storage::getInstance();

        $this->actionOk = $this->createAction(array($this, "Ok"));

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setScale(0.8);
        $this->frame->setPosY(2);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Column(120, 30));
        $this->mainFrame->addComponent($this->frame);
        $version = $this->connection->getVersion();

        $line = new Infoline(5, "Server Login", $this->storage->serverLogin, 0);
        $this->frame->addComponent($line);

        $line = new Infoline(5, "Server version", $version->version, 0);
        $this->frame->addComponent($line);
        $line = new Infoline(5, "Server Build", $version->build, 0);
        $this->frame->addComponent($line);
        $line = new Infoline(5, "Server ApiVersio", $version->apiVersion, 0);
        $this->frame->addComponent($line);

        $line = new Infoline(5, "Server Titlepack", $version->titleId, 0);
        $this->frame->addComponent($line);

        $line = new Infoline(5, "Manialive version", \ManiaLive\Application\VERSION, 0);
        $this->frame->addComponent($line);

        $line = new Infoline(
            5,
            "eXpansion version",
            \ManiaLivePlugins\eXpansion\Core\Core::EXP_VERSION
            . " - " . (date("Y-m-d h:i:s A", Helper::getBuildDate())),
            0
        );
        $this->frame->addComponent($line);

        $line = new Infoline(5, "Php Version", phpversion(), 0);
        $this->frame->addComponent($line);

        $this->frame->addComponent(new \ManiaLib\Gui\Elements\Label(10, 7));

        $line = new Inputbox("join", 77);
        $line->setScale(1.2);
        $line->setLabel("Join link:");
        $line->setText("maniaplanet://#join=" . $this->storage->serverLogin . "@" . $version->titleId);
        $this->frame->addComponent($line);

        $line = new Inputbox("fav", 77);
        $line->setScale(1.2);
        $line->setLabel("Favourite link:");
        $line->setText("maniaplanet://#addfavourite=" . $this->storage->serverLogin . "@" . $version->titleId);
        $this->frame->addComponent($line);

        $this->ok = new OkButton();
        $this->ok->colorize("0d0");
        $this->ok->setText(__("Close", $login));
        $this->ok->setAction($this->actionOk);
        $this->mainFrame->addComponent($this->ok);

        $this->stats = new OkButton(30, 6);
        $this->stats->setText(__("Server Stats", $login));
        $this->stats->setAction(self::$statsAction);
        $this->mainFrame->addComponent($this->stats);

        $this->button_cpJoin = new OkButton(20, 6);
        $this->button_cpJoin->setText(__("Copy", $login));
        $this->button_cpJoin->setId("CopyJoinLink");
        $this->button_cpJoin->setScriptEvents(true);

        $this->mainFrame->addComponent($this->button_cpJoin);

        $this->button_addFav = new OkButton(20, 6);
        $this->button_addFav->setText(__("Add to Fav's", $login));
        $url = 'http://reaby.kapsi.fi/ml/addfavourite.php?login=' . rawurldecode($this->storage->serverLogin);
        $this->button_addFav->setManialink($url);
        $this->mainFrame->addComponent($this->button_addFav);
    }

    public function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $this->ok->setPosition($this->sizeX - 22, -$this->sizeY + 6);

        if (self::$statsAction == -1) {
            $this->stats->setVisibility(false);
        } else {
            $this->stats->setPosition($this->sizeX - 45, -$this->sizeY + 6);
        }

        $this->button_cpJoin->setPosition($this->sizeX - 20, -$this->sizeY + 26);
        $this->button_addFav->setPosition($this->sizeX - 20, -$this->sizeY + 14.5);
    }

    public function ok($login)
    {
        $this->erase($login);
    }

    public function destroy()
    {
        $this->connection = null;
        $this->storage = null;
        $this->destroyComponents();
        parent::destroy();
    }
}
