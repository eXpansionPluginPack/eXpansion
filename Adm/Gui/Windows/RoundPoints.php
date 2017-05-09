<?php

namespace ManiaLivePlugins\eXpansion\Adm\Gui\Windows;

use ManiaLive\Data\Storage;
use ManiaLivePlugins\eXpansion\Adm\Adm;
use ManiaLivePlugins\eXpansion\Adm\Gui\Controls\CustomPointctrl;
use ManiaLivePlugins\eXpansion\Adm\Gui\Controls\CustomPointEntry;
use ManiaLivePlugins\eXpansion\Adm\Structures\CustomPoint;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button as OkButton;
use ManiaLivePlugins\eXpansion\Gui\Elements\Pager;
use ManiaLivePlugins\eXpansion\Gui\Windows\Window;
use ManiaLivePlugins\eXpansion\Helpers\Singletons;
use Maniaplanet\DedicatedServer\Connection;

class RoundPoints extends Window
{
    /**
     * @var Adm
     */
    public static $plugin;

    /** @var  Pager */
    protected $pager;

    /** @var Connection */
    protected $connection;

    /** @var Storage */
    protected $storage;

    protected $items = array();

    /** @var  OkButton */
    protected $cancel;

    protected $actionCancel;

    protected $rpoints = array();

    protected function onConstruct()
    {
        parent::onConstruct();
        $login = $this->getRecipient();

        $this->connection = Singletons::getInstance()->getDediConnection();
        $this->storage = Storage::getInstance();

        $this->pager = new Pager();
        $this->mainFrame->addComponent($this->pager);

        $this->actionCancel = $this->createAction(array($this, "cancel"));


        $this->cancel = new OkButton();
        $this->cancel->setText(__("Close", $login));
        $this->cancel->setAction($this->actionCancel);
        $this->mainFrame->addComponent($this->cancel);


        $this->rpoints[] = new CustomPoint('Formula 1 GP New', array(25, 18, 15, 12, 10, 8, 6, 4, 2, 1));
        $this->rpoints[] = new CustomPoint('Formula 1 GP Old', array(10, 8, 6, 5, 4, 3, 2, 1));
        $this->rpoints[] = new CustomPoint('MotoGP', array(25, 20, 16, 13, 11, 10, 9, 8, 7, 6, 5, 4, 3, 2, 1));
        $this->rpoints[] = new CustomPoint(
            'MotoGP + 5',
            array(30, 25, 21, 18, 16, 15, 14, 13, 12, 11, 10, 9, 8, 7, 6, 5, 4, 3, 2, 1)
        );
        $this->rpoints[] = new CustomPoint(
            'Formula ET Season 1',
            array(12, 10, 9, 8, 7, 6, 5, 4, 4, 3, 3, 3, 2, 2, 2, 1)
        );
        $this->rpoints[] = new CustomPoint(
            'Formula ET Season 2',
            array(15, 12, 11, 10, 9, 8, 7, 6, 6, 5, 5, 4, 4, 3, 3, 3, 2, 2, 2, 1)
        );
        $this->rpoints[] = new CustomPoint(
            'Formula ET Season 3',
            array(15, 12, 11, 10, 9, 8, 7, 6, 6, 5, 5, 4, 4, 3, 3, 3, 2, 2, 2, 2, 1)
        );
        $this->rpoints[] = new CustomPoint(
            'Champ Car World Series',
            array(31, 27, 25, 23, 21, 19, 17, 15, 13, 11, 10, 9, 8, 7, 6, 5, 4, 3, 2, 1)
        );
        $this->rpoints[] = new CustomPoint('Superstars', array(20, 15, 12, 10, 8, 6, 4, 3, 2, 1));
        $this->rpoints[] = new CustomPoint('Simple 5', array(5, 4, 3, 2, 1));
        $this->rpoints[] = new CustomPoint('Simple 10', array(10, 9, 8, 7, 6, 5, 4, 3, 2, 1));

    }

    protected function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $this->pager->setSize($this->sizeX, $this->sizeY - 8);
        $this->cancel->setPosition($this->sizeX - 20, -$this->sizeY + 6);
    }

    protected function onShow()
    {
        $this->populateList();
    }

    public function populateList()
    {
        foreach ($this->items as $item) {
            $item->erase();
        }
        $this->pager->clearItems();
        $this->items = array();

        $login = $this->getRecipient();
        $x = 0;

        $points = implode(",", $this->connection->getRoundCustomPoints());
        $this->items[$x] = new CustomPointEntry(
            $x,
            $points,
            $this,
            $login,
            $this->sizeX
        );
        $this->pager->addItem($this->items[$x]);
        $x++;

        foreach ($this->rpoints as $points) {
            $this->items[$x] = new CustomPointctrl(
                $x,
                $points,
                $this,
                $login,
                $this->sizeX
            );
            $this->pager->addItem($this->items[$x]);
            $x++;
        }
    }

    public function setPoints($login, $points, $entry)
    {
        if ($points === null) {
            $intPoints = array();
            if (!empty($entry['customPoints'])) {
                $points = explode(",", $entry['customPoints']);
                rsort($points, SORT_NUMERIC);
                foreach ($points as $p) {
                    $intPoints[] = intval($p);
                }
                self::$plugin->setPoints($login, $intPoints);
            }
        } else {
            self::$plugin->setPoints($login, $points);
        }
        $this->erase($login);
    }

    public function cancel($login)
    {
        $this->erase($login);
    }

    public function destroy()
    {
        foreach ($this->items as $item) {
            $item->erase();
        }

        $this->items = array();
        $this->pager->destroy();
        $this->cancel->destroy();
        $this->connection = null;
        $this->storage = null;
        $this->destroyComponents();
        parent::destroy();
    }
}
