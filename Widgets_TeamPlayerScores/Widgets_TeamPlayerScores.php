<?php

namespace ManiaLivePlugins\eXpansion\Widgets_TeamPlayerScores;

use ManiaLivePlugins\eXpansion\Core\Core;
use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;
use ManiaLivePlugins\eXpansion\Gui\Widgets\Widget;
use ManiaLivePlugins\eXpansion\Widgets_TeamPlayerScores\Structures\PlayerScore;

/**
 * Description of Widgets_PlayerScores
 *
 * @author Petri
 */
class Widgets_TeamPlayerScores extends ExpPlugin {

    /**
     * @var PlayerScore[]
     */
    private $playerScores = array();

    public function exp_onLoad() {
	
    }

    public function exp_onReady() {
	$this->enableDedicatedEvents();
	$this->reset();
	$this->registerChatCommand("test", "test", 0, false);
    }

    public function test() {
	foreach ($this->storage->players as $login => $player) {
	    if (!array_key_exists($login, $this->playerScores)) {
		$this->playerScores[$login] = new PlayerScore();
		$this->playerScores[$login]->login = $login;
		$this->playerScores[$login]->nickName = $player->nickName;
	    }
	}

	foreach (Core::$playerInfo as $player) {
	    // get points
	    $this->playerScores[$player->login]->score += $this->getScore($player->position);

	    // assign best time
	    if ($this->playerScores[$player->login]->bestTime == 0 || $player->finalTime < $this->playerScores[$player->login]->bestTime) {
		$this->playerScores[$player->login]->bestTime = $player->finalTime;
	    }

	    // count wins
	    switch ($player->position) {
		case 0:
		    $this->playerScores[$player->login]->winScore[0] ++;
		    break;
		case 1:
		    $this->playerScores[$player->login]->winScore[1] ++;
		    break;
		case 2:
		    $this->playerScores[$player->login]->winScore[2] ++;
		    break;
	    }
	}

	$this->showWidget();
    }

    public function onBeginRound() {
	$this->hideWidget();
    }

    public function onEndRound() {
	// create players
	foreach ($this->storage->players as $login => $player) {
	    if (!array_key_exists($login, $this->playerScores)) {
		$this->playerScores[$login] = new PlayerScore();
		$this->playerScores[$login]->login = $login;
		$this->playerScores[$login]->nickName = $player->nickName;
	    }
	}

	// count scores
	foreach (Core::$playerInfo as $player) {
	    if ($player->finalTime != 0) {
		// get points
		$this->playerScores[$player->login]->score += $this->getScore($player->position);

		// assign best time
		if ($this->playerScores[$player->login]->bestTime == 0 || $player->finalTime < $this->playerScores[$player->login]->bestTime) {
		    $this->playerScores[$player->login]->bestTime = $player->finalTime;
		}

		// count wins
		switch ($player->position) {
		    case 0:
			$this->playerScores[$player->login]->winScore[0] ++;
			break;
		    case 1:
			$this->playerScores[$player->login]->winScore[1] ++;
			break;
		    case 2:
			$this->playerScores[$player->login]->winScore[2] ++;
			break;
		}
	    }
	}
	$this->showWidget();
    }

    public function onBeginMap($map, $warmUp, $matchContinuation) {
	$this->reset();
    }

    private function getScore($position) {
	/** @var int[] */
	$points = $this->connection->getRoundCustomPoints();
	if (empty($points)) {
	    $points = array(10, 9, 8, 7, 6, 5, 4, 3, 2, 1);
	}

	if (array_key_exists($position, $points))
	    return $points[$position];
	else
	    return end($points);
    }

    private function reset() {
	$this->playerScores = array();
    }

    private function showWidget() {
	$widget = Gui\Widgets\PlayerScoreWidget::Create();
	$widget->setSize(42, 56);
	$widget->setScores($this->playerScores);
	$widget->setLayer(Widget::LAYER_NORMAL);
	$widget->setPosition(-124, 6);
	$widget->show();
    }

    private function hideWidget() {
	Gui\Widgets\PlayerScoreWidget::EraseAll();
    }

    public function exp_onUnload() {
	$this->reset();
	$this->hideWidget();
    }

}
