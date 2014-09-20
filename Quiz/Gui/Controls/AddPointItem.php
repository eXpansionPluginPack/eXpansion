<?php

namespace ManiaLivePlugins\eXpansion\Quiz\Gui\Controls;

use ManiaLivePlugins\eXpansion\Players\Gui\Windows\Playerlist;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use \ManiaLib\Utils\Formatting;

class AddPointItem extends \ManiaLive\Gui\Control
{

	private $bg;

	private $addpointButton, $removepointButton;

	private $nickname;

	private $addpointAction;

	private $removepointAction;

	private $pointsLabel;
	
	private $frame;

	function __construct($indexNumber, \ManiaLive\Data\Player $player, $controller, $isAdmin, $login, $sizeX)
	{
		$sizeY = 6;

		if ($isAdmin) {
			$this->addpointAction = $this->createAction(array($controller, 'addPoint'), $player->login);
			$this->removepointAction = $this->createAction(array($controller, 'removePoint'), $player->login);
		}

		$this->bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround($indexNumber, $sizeX, $sizeY);
		$this->addComponent($this->bg);


		$this->frame = new \ManiaLive\Gui\Controls\Frame();
		$this->frame->setSize($sizeX, $sizeY);
		$this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());

		$spacer = new \ManiaLib\Gui\Elements\Quad();
		$spacer->setSize(4, 4);
		$spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
//$this->frame->addComponent($spacer);

		$this->login = new \ManiaLib\Gui\Elements\Label(20, 4);
		$this->login->setAlign('left', 'center');
		$this->login->setText($player->login);
		$this->login->setScale(0.8);
		$this->frame->addComponent($this->login);

		$this->nickname = new \ManiaLib\Gui\Elements\Label(30, 4);
		$this->nickname->setAlign('left', 'center');
		$this->nickname->setScale(0.8);
		$this->nickname->setText($player->nickName);
		$this->frame->addComponent($this->nickname);

		$spacer = new \ManiaLib\Gui\Elements\Quad();
		$spacer->setSize(4, 4);
		$spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
		$this->frame->addComponent($spacer);

		// admin additions
		if ($isAdmin) {
			$this->removepointButton = new MyButton(15, 5);
			$this->removepointButton->setText("-1");
			$this->removepointButton->setTextColor("fff");
			$this->removepointButton->colorize("a00");
			$this->removepointButton->setScale(0.5);
			$this->removepointButton->setAction($this->removepointAction);
			$this->frame->addComponent($this->removepointButton);
			
			$this->pointsLabel = new \ManiaLib\Gui\Elements\Label(10,5);
			$this->pointsLabel->setTextSize(1);
			$this->pointsLabel->setPosX(5);
			$this->pointsLabel->setAlign("center", "center");
			$this->pointsLabel->setText("0");
			$this->frame->addComponent($this->pointsLabel);
				
			$this->addpointButton = new MyButton(15, 5);
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
		$this->frame->setSize($this->sizeX, $this->sizeY);
		$this->bg->setPosX(-2);
		$this->bg->setSize($this->sizeX, $this->sizeY);
		parent::onResize($oldX, $oldY);
	}

	public function setPoints($point)
	{
		$this->pointsLabel->setText($point);
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

		$this->frame->destroy();
		$this->clearComponents();
		parent::destroy();
	}

}
?>

