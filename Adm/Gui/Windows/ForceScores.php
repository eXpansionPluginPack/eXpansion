<?php

namespace ManiaLivePlugins\eXpansion\Adm\Gui\Windows;

use ManiaLib\Gui\Layouts\Line;
use ManiaLive\Data\Storage as MlStorage;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\Adm\Adm;
use ManiaLivePlugins\eXpansion\Adm\Gui\Controls\PlayerScore;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button;
use ManiaLivePlugins\eXpansion\Gui\Elements\Pager;
use ManiaLivePlugins\eXpansion\Gui\Windows\Window;
use ManiaLivePlugins\eXpansion\Helpers\Singletons;
use ManiaLivePlugins\eXpansion\Helpers\Storage;

class ForceScores extends Window
{
    /** @var Pager */
    protected $pager;

    /** @var \Maniaplanet\DedicatedServer\Connection */
    protected $connection;

    /** @var MlStorage */
    protected $storage;

    protected $items = array();
    /** @var  Button */
    protected $ok;
    /** @var  Button */
    protected $cancel;

    protected $actionOk;

    protected $actionCancel;
    /** @var  Frame */
    protected $buttonframe;
    /** @var  Button */
    protected $btn_clearScores;
    /** @var  Button */
    protected $btn_resetSkip;
    /** @var  Button */
    protected $btn_resetRes;
    /** @var  Adm */
    public static $mainPlugin;

    /**
     *
     */
    protected function onConstruct()
    {
        parent::onConstruct();
        $login = $this->getRecipient();

        $this->connection = Singletons::getInstance()->getDediConnection();
        $this->storage = MlStorage::getInstance();

        $this->pager = new Pager();
        $this->mainFrame->addComponent($this->pager);
        $this->actionOk = $this->createAction(array($this, "ok"));
        $this->actionCancel = $this->createAction(array($this, "cancel"));

        $this->buttonframe = new Frame(40, 2);
        $line = new Line();
        $line->setMargin(2, 1);
        $this->buttonframe->setLayout($line);
        $this->mainFrame->addComponent($this->buttonframe);


        $this->btn_clearScores = new Button();
        $this->btn_clearScores->setAction($this->createAction(array($this, "resetScores")));
        $this->btn_clearScores->setText(__("Reset scores", $login));
        $this->buttonframe->addComponent($this->btn_clearScores);

        $this->btn_resetSkip = new Button();
        $this->btn_resetSkip->setAction($this->createAction(array($this, "resetSkip")));
        $this->btn_resetSkip->setText(__("Skip & reset", $login));
        $this->buttonframe->addComponent($this->btn_resetSkip);

        $this->btn_resetRes = new Button();
        $this->btn_resetRes->setAction($this->createAction(array($this, "resetRes")));
        $this->btn_resetRes->setText(__("Restart & reset", $login));
        $this->buttonframe->addComponent($this->btn_resetRes);


        $this->ok = new Button();
        $this->ok->colorize("0d0");
        $this->ok->setText(__("Apply", $login));
        $this->ok->setAction($this->actionOk);
        $this->mainFrame->addComponent($this->ok);

        $this->cancel = new Button();
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
        $this->pager->setSize($this->sizeX, $this->sizeY - 8);
        $this->pager->setStretchContentX($this->sizeX);
        $this->ok->setPosition($this->sizeX - 50, -$this->sizeY + 6);
        $this->cancel->setPosition($this->sizeX - 24, -$this->sizeY + 6);
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

        /** @var Storage $expStorage */
        $expStorage = Storage::getInstance();
        $rankings = $expStorage->getCurrentRanking();

        foreach ($rankings as $player) {
            $this->items[$x] = new PlayerScore(
                $x,
                $player,
                $this->sizeX - 8
            );
            $this->pager->addItem($this->items[$x]);
            $x++;
        }
    }

    /**
     * @param $login
     * @param $scores
     */
    public function ok($login, $scores)
    {
        $outScores = array();

        foreach ($scores as $id => $val) {
            $outScores[] = array("PlayerId" => intval($id), "Score" => intval($val));
        }

        $this->connection->forceScores($outScores, true);
        self::$mainPlugin->forceScoresOk();
        $this->erase($login);
    }

    /**
     * @param $login
     */
    public function resetScores($login)
    {
        /** @var Storage $expStorage */
        $expStorage = Storage::getInstance();
        $rankings = $expStorage->getCurrentRanking();

        $outScores = array();
        foreach ($rankings as $rank) {
            $outScores[] = array("PlayerId" => intval($rank->playerId), "Score" => 0);
        }
        $this->connection->forceScores($outScores, true);
        self::$mainPlugin->forceScoresOk();

        $this->populateList();
        $this->RedrawAll();
    }

    /**
     * @param $login
     */
    public function resetSkip($login)
    {
        $ag = AdminGroups::getInstance();
        $ag->adminCmd($login, "rskip");
        $this->Erase($login);
    }

    /**
     * @param $login
     */
    public function resetRes($login)
    {
        $ag = AdminGroups::getInstance();
        $ag->adminCmd($login, "rres");
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
            $item->erase();
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
