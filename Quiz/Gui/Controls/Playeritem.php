<?php

namespace ManiaLivePlugins\eXpansion\Quiz\Gui\Controls;

use ManiaLib\Gui\Elements\Icons64x64_1;
use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Elements\Quad;
use ManiaLib\Gui\Layouts\Line;
use ManiaLive\Gui\Control;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button;
use ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround;
use ManiaLivePlugins\eXpansion\Quiz\Structures\QuizPlayer;

class Playeritem extends Control
{

	private $background;

	private $addpointButton;

	private $removepointButton;

	private $nickname;

	private $addpointAction;

	private $removeAction;

	private $frame;

	private $points;

	private $isAdmin;

	function __construct($indexNumber, QuizPlayer $player, $controller, $isAdmin, $login, $sizeX)
	{
		$sizeY = 6;
		$this->isAdmin = $isAdmin;

		if ($isAdmin) {
			$this->addpointAction = $this->createAction(array($controller, 'addPoint'), $player->login);
			$this->removeAction = $this->createAction(array($controller, 'removePoint'), $player->login);
		}
		
		$this->background = new ListBackGround($indexNumber, 90, $sizeY);
		$this->addComponent($this->background);

		$this->frame = new Frame();
		$this->frame->setSize($sizeX, $sizeY);
		$this->frame->setLayout(new Line());

		$spacer = new Quad();
		$spacer->setSize(4, 4);
		$spacer->setStyle(Icons64x64_1::EmptyIcon);
//$this->frame->addComponent($spacer);

		$this->login = new Label(20, 4);
		$this->login->setAlign('left', 'center');
		$this->login->setText($player->login);
		$this->login->setScale(0.8);
		$this->frame->addComponent($this->login);

		$this->nickname = new Label(30, 4);
		$this->nickname->setAlign('left', 'center');
		$this->nickname->setScale(0.8);
		$this->nickname->setText($player->nickName);
		$this->frame->addComponent($this->nickname);

		$spacer = new Quad();
		$spacer->setSize(4, 4);
		$spacer->setStyle(Icons64x64_1::EmptyIcon);
		$this->frame->addComponent($spacer);

		$this->points = new Label(12, 4);
		$this->points->setAlign('left', 'center');
		$this->points->setScale(0.8);
		$this->points->setText($player->points);
		$this->frame->addComponent($this->points);


		// admin additions
		if ($this->isAdmin) {
			$this->removepointButton = new Button(15, 5);
			$this->removepointButton->setText("-1");
			$this->removepointButton->setTextColor("fff");
			$this->removepointButton->colorize("a22");
			$this->removepointButton->setAction($this->removeAction);
			$this->removepointButton->setScale(0.5);
			$this->frame->addComponent($this->removepointButton);

			$this->addpointButton = new Button(15, 5);
			$this->addpointButton->setText("+1");
			$this->addpointButton->setTextColor("fff");
			$this->addpointButton->colorize("2a2");
			$this->addpointButton->setScale(0.5);
			$this->addpointButton->setAction($this->addpointAction);
			$this->frame->addComponent($this->addpointButton);
		}

		$this->addComponent($this->frame);

		$this->sizeX = $sizeX;
		$this->sizeY = $sizeY;
		$this->setSize($sizeX, $sizeY);
	}

	protected function onResize($oldX, $oldY)
	{
		parent::onResize($oldX, $oldY);
		$this->frame->setSize($this->sizeX, $this->sizeY);
		$this->background->setSize($this->getSizeX(), $this->getSizeY());
	}

	// manialive 3.1 override to do nothing.
	function destroy()
	{
		
	}

	/*
	 * custom function to remove contents.
	 */

	function erase()
	{
		if (is_object($this->addpointButton))
			$this->addpointButton->destroy();
		if (is_object($this->removepointButton))
			$this->removepointButton->destroy();

		$this->frame->clearComponents();
		$this->frame->destroy();
		$this->destroyComponents();
		parent::destroy();
	}

}
?>

