<?php

namespace ManiaLivePlugins\eXpansion\ESLcup\Gui\Widgets;

/**
 * Description of Scoretable
 *
 * @author Reaby
 */
class Scoretable extends \ManiaLive\Gui\Window
{

    protected $background;
    protected $rankingslabel;
    protected $pointslimit;
    protected $gamemode;
    protected $next;
    protected $prev;
    protected $frame;
    private $page = 0;
    private $itemsOnPage = 16;
    private $scores = array();
    private $limit = -1;
    private $winners = array();
    private $actionNext;
    private $actionPrev;

    protected function onConstruct()
    {
        $this->sizeX = 165;
        $this->sizeY = 90;

        $this->actionNext = $this->createAction(array($this, "next"));
        $this->actionPrev = $this->createAction(array($this, "prev"));

        $this->background = new \ManiaLib\Gui\Elements\Quad($this->sizeX, $this->sizeY);
        $this->background->setStyle("Bgs1InRace");
        $this->background->setSubStyle("BgList");
        $this->addComponent($this->background);
        $this->background->setPosY(4);

        $this->rankingslabel = new \ManiaLib\Gui\Elements\Label(120, 6);
        $this->rankingslabel->setText(__("Score Rankings"));
        $this->rankingslabel->setTextColor("fff");
        $this->rankingslabel->setTextSize(4);
        $this->rankingslabel->setPosition($this->sizeX / 2, 3);
        $this->rankingslabel->setAlign("center");
        $this->addComponent($this->rankingslabel);

        $this->pointslimit = new \ManiaLib\Gui\Elements\Label(30, 6);
        $this->pointslimit->setTextColor("fff");
        $this->pointslimit->setTextSize(1);
        $this->pointslimit->setPosition($this->sizeX / 2, -$this->sizeY);
        $this->pointslimit->setAlign("center");
        $this->addComponent($this->pointslimit);

        $this->gamemode = new \ManiaLib\Gui\Elements\Label(30, 6);
        $this->gamemode->setTextColor("fff");
        $this->gamemode->setTextSize(1);
        $this->gamemode->setText('$sGame Mode: ESL cup');
        $this->gamemode->setPosition($this->sizeX / 2, -$this->sizeY - 4);
        $this->gamemode->setAlign("center");
        $this->addComponent($this->gamemode);

        $this->next = new \ManiaLib\Gui\Elements\Quad(8, 8);
        $this->next->setAction($this->actionNext);
        $this->next->setStyle("Icons64x64_1");
        $this->next->setSubStyle("ArrowNext");
        $this->next->setPosition($this->sizeX - 9, 5);
        $this->addComponent($this->next);

        $this->prev = new \ManiaLib\Gui\Elements\Quad(8, 8);
        $this->prev->setAction($this->actionPrev);
        $this->prev->setStyle("Icons64x64_1");
        $this->prev->setSubStyle("ArrowPrev");
        $this->prev->setPosition($this->sizeX - 16, 5);
        $this->addComponent($this->prev);

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\VerticalFlow());
        $this->frame->setSize(120, 80);
        $this->frame->setPosition(2, -3);
        $this->addComponent($this->frame);
    }

    public function next($login)
    {

        $newstart = ($this->page + 1) * $this->itemsOnPage;
        if ($newstart < count($this->scores)) {
            $this->page++;
        }

        $this->redraw($login);
    }

    public function prev($login)
    {
        $this->page--;
        if ($this->page < 0) {
            $this->page = 0;
        }

        $this->redraw($login);
    }

    protected function onDraw()
    {
        $this->next->setHidden(false);
        $this->prev->setHidden(false);

        $newstart = ($this->page + 1) * $this->itemsOnPage;
        if ($newstart > count($this->scores)) {
            $this->next->setHidden(true);
        }

        if ($this->page == 0) {
            $this->prev->setHidden(true);
        }

        $this->pointslimit->setText('$sPoints Limit: ' . $this->limit);
        $this->frame->clearComponents();
        $x = 1;
        $items = array();
        $isWinner = array();

        // add winners to top
        foreach ($this->winners as $place => $winner) {
            $isWinner[] = $winner->login;
            $items[] = new \ManiaLivePlugins\eXpansion\ESLcup\Gui\Controls\CupScoreTableItem($x, $winner, $x);
            $x++;
        }
        // add other players bottom
        foreach ($this->scores as $scoreitem) {
            if (!in_array($scoreitem->login, $isWinner)) {
                $items[] = new \ManiaLivePlugins\eXpansion\ESLcup\Gui\Controls\CupScoreTableItem($x, $scoreitem, -1);
                $x++;
            }
        }

        foreach ($items as $component) {
            $start = $this->page * $this->itemsOnPage;
            $limit = $start + $this->itemsOnPage + 1;
            if ($x > $start && $x < $limit) {
                $this->frame->addComponent($component);
            }
        }
    }

    public function setData($scores, $limit, $winners)
    {
        $this->scores = $scores;
        $this->limit = $limit;
        $this->winners = $winners;
    }
}
