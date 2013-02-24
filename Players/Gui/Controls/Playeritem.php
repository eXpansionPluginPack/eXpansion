<?php

namespace ManiaLivePlugins\eXpansion\Players\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use \ManiaLib\Utils\Formatting;

class Playeritem extends \ManiaLive\Gui\Control {

	private $bg;
	private $forceButton;
	private $kickButton;
	private $banButton;
	private $login;
	private $nickname;
	private $kickAction;
	private $banAction;
	private $forceAction;
	private $frame;

	function __construct($indexNumber, \DedicatedApi\Structures\Player $player, $controller, $isAdmin) {
		$sizeX = 120;
		$sizeY = 4;
		$this->isAdmin = $isAdmin;
		$this->player = $player;

		$this->kickAction = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($controller, 'kickPlayer'), $player->login);
		$this->banAction = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($controller, 'banPlayer'), $player->login);
		$this->forceAction = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($controller, 'toggleSpec'), $player->login);

		// stupid background...
		/* $this->bg = new \ManiaLib\Gui\Elements\Quad($sizeX, $sizeY);
		  $this->bg->setAlign('left', 'center');
		  if ($indexNumber % 2 == 0) {
		  $this->bg->setBgcolor('fff4');
		  } else {
		  $this->bg->setBgcolor('77f4');
		  }
		  $this->bg->setScriptEvents(true);
		  $this->addComponent($this->bg);
		 */

		$this->frame = new \ManiaLive\Gui\Controls\Frame();
		$this->frame->setSize($sizeX, $sizeY);
		$this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());

		$spacer = new \ManiaLib\Gui\Elements\Quad();
		$spacer->setSize(4, 4);
		$spacer->setAlign("center", "center2");
		$spacer->setStyle("Icons64x64_1");

		if ($player->forceSpectator == 1 || $player->isSpectator)
			$spacer->setSubStyle("Camera");
		else
			$spacer->setSubStyle("Buddy");


		$this->frame->addComponent($spacer);

		$spacer = new \ManiaLib\Gui\Elements\Quad();
		$spacer->setSize(4, 4);
		$spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
//$this->frame->addComponent($spacer);

		$this->login = new \ManiaLib\Gui\Elements\Label(20, 4);
		$this->login->setAlign('left', 'center');
		$this->login->setText($player->login);
		$this->login->setScale(0.8);
		$this->frame->addComponent($this->login);

		$this->nickname = new \ManiaLib\Gui\Elements\Label(60, 4);
		$this->nickname->setAlign('left', 'center');
		$this->nickname->setScale(0.8);
		$this->nickname->setText($player->nickName);
		$this->frame->addComponent($this->nickname);

		$spacer = new \ManiaLib\Gui\Elements\Quad();
		$spacer->setSize(4, 4);
		$spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);

		$this->frame->addComponent($spacer);

// admin additions
		if ($this->isAdmin) {

			$this->forceButton = new MyButton(24, 6);
			$this->forceButton->setAction($this->forceAction);
			$this->forceButton->setScale(0.6);
			$this->frame->addComponent($this->forceButton);


			$this->kickButton = new MyButton(16, 6);
			$this->kickButton->setText(_("Kick"));
			$this->kickButton->setAction($this->kickAction);
			$this->kickButton->setScale(0.6);
			$this->frame->addComponent($this->kickButton);

			$this->banButton = new MyButton(16, 6);
			$this->banButton->setText(_("Ban"));
			$this->banButton->setAction($this->banAction);
			$this->banButton->setScale(0.6);
			$this->frame->addComponent($this->banButton);
		}

		$this->addComponent($this->frame);

		$this->sizeX = $sizeX;
		$this->sizeY = $sizeY;
		$this->setSize($sizeX, $sizeY);
	}

	protected function onResize($oldX, $oldY) {
// $this->frame->setSize($this->sizeX, $this->sizeY);
//  $this->button->setPosx($this->sizeX - $this->button->sizeX);
		if ($this->isAdmin) {
			if ($this->player->forceSpectator == 1 || $this->player->isSpectator) {
				$this->forceButton->setText(_("Release Spec"));
			} else {
				$this->forceButton->setText(_("Force Spec"));
			}
		}
	}

	function onDraw() {
		
	}

	function __destruct() {
		ActionHandler::getInstance()->deleteAction($this->kickAction);
		ActionHandler::getInstance()->deleteAction($this->banAction);
		ActionHandler::getInstance()->deleteAction($this->forceAction);		
	}

}
?>

