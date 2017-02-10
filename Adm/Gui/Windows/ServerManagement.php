<?php

namespace ManiaLivePlugins\eXpansion\Adm\Gui\Windows;

use ManiaLib\Gui\Layouts\Line;
use ManiaLive\Application\Application;
use ManiaLive\Data\Storage;
use ManiaLive\Gui\ActionHandler;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use ManiaLivePlugins\eXpansion\Gui\Gui;
use ManiaLivePlugins\eXpansion\Gui\Windows\Window;
use ManiaLivePlugins\eXpansion\Helpers\Singletons;
use Maniaplanet\DedicatedServer\Connection;

/**
 * Server Controlpanel Main window
 *
 * @author Petri
 */
class ServerManagement extends Window
{

    /** @var Connection */
    protected $connection;

    /** @var Storage */
    protected $storage;
    /** @var  Frame */
    protected $frame;
    /** @var  myButton */
    protected $closeButton;

    protected $actions;
    /** @var  myButton */
    protected $btn1;
    /** @var  myButton */
    protected $btn2;

    protected function onConstruct()
    {
        parent::onConstruct();

        $this->connection = Singletons::getInstance()->getDediConnection();
        $this->storage = Storage::getInstance();

        $this->frame = new Frame();
        $this->frame->setLayout(new Line());

        $this->actions = new \stdClass();
        $this->actions->close = $this->createAction(array($this, "close"));

        $this->actions->stopServerf = $this->createAction(array($this, "stopServer"));
        $this->actions->stopServer = Gui::createConfirm($this->actions->stopServerf);
        $this->actions->stopManialivef = $this->createAction(array($this, "stopManialive"));
        $this->actions->stopManialive = Gui::createConfirm(
            $this->actions->stopManialivef
        );

        $this->btn1 = new myButton(40, 6);
        $this->btn1->setText(__("Stop Server", $this->getRecipient()));
        $this->btn1->setAction($this->actions->stopServer);
        $this->btn1->colorize("d00");
        $this->frame->addComponent($this->btn1);

        $this->btn2 = new myButton(40, 6);
        $this->btn2->setText(__("Stop Manialive", $this->getRecipient()));
        $this->btn2->setAction($this->actions->stopManialive);
        $this->btn2->colorize("d00");
        $this->frame->addComponent($this->btn2);


        $this->addComponent($this->frame);

        $this->closeButton = new myButton(30, 6);
        $this->closeButton->setText(__("Cancel", $this->getRecipient()));
        $this->closeButton->setAction($this->actions->close);
        $this->addComponent($this->closeButton);
    }

    protected function onDraw()
    {

        $this->btn1->setVisibility(
            AdminGroups::hasPermission($this->getRecipient(), Permission::SERVER_STOP_DEDICATED)
        );
        $this->btn2->setVisibility(
            AdminGroups::hasPermission($this->getRecipient(), Permission::SERVER_STOP_MANIALIVE)
        );

        parent::onDraw();
    }

    public function stopServer()
    {
        $this->connection->stopServer();
    }

    public function stopManialive()
    {
        $this->connection->chatSendServerMessage("[Notice] Stopping eXpansion...");
        $this->connection->sendHideManialinkPage();
        Application::getInstance()->kill();
    }

    public function close()
    {
        $this->Erase($this->getRecipient());
    }

    protected function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $this->frame->setPosition(2, -6);
        $this->closeButton->setPosition($this->sizeX - 28, -($this->sizeY - 6));
    }

    public function destroy()
    {
        ActionHandler::getInstance()->deleteAction($this->actions->close);
        ActionHandler::getInstance()->deleteAction($this->actions->stopServer);
        ActionHandler::getInstance()->deleteAction($this->actions->stopServerf);
        ActionHandler::getInstance()->deleteAction($this->actions->stopManialive);
        ActionHandler::getInstance()->deleteAction($this->actions->stopManialivef);
        $this->closeButton->destroy();
        $this->btn1->destroy();
        $this->btn2->destroy();
        unset($this->actions);
        parent::destroy();
    }
}
