<?php

namespace ManiaLivePlugins\eXpansion\Adm\Gui\Windows;

use ManiaLive\Data\Storage;
use ManiaLivePlugins\eXpansion\Adm\Gui\Controls\ScriptSetting;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button as OkButton;
use ManiaLivePlugins\eXpansion\Gui\Elements\Pager;
use ManiaLivePlugins\eXpansion\Gui\Windows\Window;
use ManiaLivePlugins\eXpansion\Helpers\Singletons;
use Maniaplanet\DedicatedServer\Connection;

class ScriptSettings extends Window
{
    /** @var  Pager */
    protected $pager;

    /** @var Connection */
    protected $connection;

    /** @var Storage */
    protected $storage;

    protected $items = array();
    /** @var  OkButton */
    protected $ok;
    /** @var  OkButton */
    protected $cancel;

    protected $actionOk;
    protected $actionCancel;

    /**
     *
     */
    protected function onConstruct()
    {
        parent::onConstruct();
        $login = $this->getRecipient();

        $this->connection = Singletons::getInstance()->getDediConnection();
        $this->storage = Storage::getInstance();

        $this->pager = new Pager();
        $this->pager->setPosX(5);
        $this->addComponent($this->pager);
        $this->actionOk = $this->createAction(array($this, "ok"));
        $this->actionCancel = $this->createAction(array($this, "cancel"));

        $this->ok = new OkButton();
        $this->ok->colorize("0d0");
        $this->ok->setText(__("Apply", $login));
        $this->ok->setAction($this->actionOk);
        $this->mainFrame->addComponent($this->ok);

        $this->cancel = new OkButton();
        $this->cancel->setText(__("Cancel", $login));
        $this->cancel->setAction($this->actionCancel);
        $this->mainFrame->addComponent($this->cancel);
    }

    /**
     * @param $oldX
     * @param $oldY
     */
    protected function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $this->pager->setSize($this->sizeX - 5, $this->sizeY - 8);
        $this->pager->setStretchContentX($this->sizeX);
        $this->ok->setPosition($this->sizeX - 46, -$this->sizeY + 6);
        $this->cancel->setPosition($this->sizeX - 20, -$this->sizeY + 6);
    }

    /**
     *
     */
    protected function onShow()
    {
        $this->populateList();
    }

    /**
     *
     */
    public function populateList()
    {
        foreach ($this->items as $item) {
            $item->erase();
        }
        $this->pager->clearItems();
        $this->items = array();

        $x = 0;
        $settings = $this->connection->getModeScriptSettings();

        foreach ($settings as $var => $setting) {
            $this->items[$x] = new ScriptSetting(
                $x,
                $var,
                $setting,
                $this->sizeX
            );
            $this->pager->addItem($this->items[$x]);
            $x++;
        }
    }

    /**
     * @param $login
     * @param $settings
     */
    public function ok($login, $settings)
    {

        foreach ($this->items as $item) {
            if ($item->checkBox !== null) {
                $settings[$item->settingName] = $item->checkBox->getStatus();
            } else {
                settype($settings[$item->settingName], $item->type);
            }
        }

        $this->connection->setModeScriptSettings($settings);

        $this->Erase($login);
    }

    /**
     * @param $login
     */
    public function cancel($login)
    {
        $this->Erase($login);
    }

    /**
     *
     */
    public function destroy()
    {
        foreach ($this->items as $item) {
            $item->destroy();
        }

        $this->items = array();
        $this->pager->destroy();
        $this->ok->destroy();
        $this->cancel->destroy();
        $this->connection = null;
        $this->storage = null;
        $this->destroyComponents();
        parent::destroy();
    }
}
