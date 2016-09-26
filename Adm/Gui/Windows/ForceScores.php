<?php

namespace ManiaLivePlugins\eXpansion\Adm\Gui\Windows;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as OkButton;
use ManiaLivePlugins\eXpansion\Helpers\Storage;

class ForceScores extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

    protected $pager;

    /** @var \Maniaplanet\DedicatedServer\Connection */
    protected $connection;

    /** @var \ManiaLive\Data\Storage */
    protected $storage;

    protected $items = array();

    protected $ok;

    protected $cancel;

    protected $actionOk;

    protected $actionCancel;

    protected $buttonframe;

    protected $btn_clearScores;
    protected $btn_resetSkip;
    protected $btn_resetRes;

    public static $mainPlugin;

    protected function onConstruct()
    {
        parent::onConstruct();
        $login = $this->getRecipient();

        $this->connection = \ManiaLivePlugins\eXpansion\Helpers\Singletons::getInstance()->getDediConnection();
        $this->storage = \ManiaLive\Data\Storage::getInstance();

        $this->pager = new \ManiaLivePlugins\eXpansion\Gui\Elements\Pager();
        $this->mainFrame->addComponent($this->pager);
        $this->actionOk = $this->createAction(array($this, "ok"));
        $this->actionCancel = $this->createAction(array($this, "cancel"));

        $this->buttonframe = new \ManiaLive\Gui\Controls\Frame(40, 2);
        $line = new \ManiaLib\Gui\Layouts\Line();
        $line->setMargin(2, 1);
        $this->buttonframe->setLayout($line);
        $this->mainFrame->addComponent($this->buttonframe);


        $this->btn_clearScores = new OkButton(32, 6);
        $this->btn_clearScores->setAction($this->createAction(array($this, "resetScores")));
        $this->btn_clearScores->setText(__("Reset scores", $login));
        $this->buttonframe->addComponent($this->btn_clearScores);

        $this->btn_resetSkip = new OkButton(32, 6);
        $this->btn_resetSkip->setAction($this->createAction(array($this, "resetSkip")));
        $this->btn_resetSkip->setText(__("Skip & reset", $login));
        $this->buttonframe->addComponent($this->btn_resetSkip);

        $this->btn_resetRes = new OkButton(32, 6);
        $this->btn_resetRes->setAction($this->createAction(array($this, "resetRes")));
        $this->btn_resetRes->setText(__("Restart & reset", $login));
        $this->buttonframe->addComponent($this->btn_resetRes);


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

    protected function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $this->pager->setSize($this->sizeX, $this->sizeY - 8);
        $this->pager->setStretchContentX($this->sizeX);
        $this->ok->setPosition($this->sizeX - 50, -$this->sizeY + 6);
        $this->cancel->setPosition($this->sizeX - 24, -$this->sizeY + 6);
    }

    protected function onShow()
    {
        $this->populateList();
    }

    public function populateList()
    {
        foreach ($this->items as $item)
            $item->erase();
        $this->pager->clearItems();
        $this->items = array();

        $x = 0;

        /** @var Storage $expStorage */
        $expStorage = Storage::getInstance();
        $rankings = $expStorage->getCurrentRanking();

        foreach ($rankings as $player) {
            $this->items[$x] = new \ManiaLivePlugins\eXpansion\Adm\Gui\Controls\PlayerScore($x, $player, $this->sizeX - 8);
            $this->pager->addItem($this->items[$x]);
            $x++;
        }
    }

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

    public function resetSkip($login)
    {
        $ag = \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::getInstance();
        $ag->adminCmd($login, "rskip");
        $this->Erase($login);
    }

    public function resetRes($login)
    {
        $ag = \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::getInstance();
        $ag->adminCmd($login, "rres");
        $this->Erase($login);
    }

    public function cancel($login)
    {
        $this->Erase($login);
    }

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
