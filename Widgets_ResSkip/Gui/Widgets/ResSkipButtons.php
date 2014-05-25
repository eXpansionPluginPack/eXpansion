<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Resskip\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Gui\Elements\WidgetButton;
use ManiaLivePlugins\eXpansion\Gui\Widgets\Widget;

class ResSkipButtons extends Widget {

    /**
     * @var WidgetButton
     */
    public $btn_res, $btn_skip, $btn_fav;

    protected function exp_onBeginConstruct() {
	parent::exp_onBeginConstruct();
	$this->btn_res = new WidgetButton(10, 10);
	$this->btn_res->setPositionZ(-1);
	$this->addComponent($this->btn_res);

	$this->btn_skip = new WidgetButton(10, 10);
	$this->btn_skip->setPositionZ(-1);
	$this->addComponent($this->btn_skip);

	$this->btn_fav = new WidgetButton(10, 10);
	$this->btn_fav->setPositionZ(-1);
	$this->btn_fav->setText(array('$s$fffAdd', '$s$fffto', '$s$fffFav\'s'));
	$this->addComponent($this->btn_fav);

	$this->setName("Skip and Res Buttons");
    }

    public function setActions($res, $skip) {
	$this->btn_res->setAction($res);
	$this->btn_skip->setAction($skip);
    }

    public function setResAmount($amount) {
	if ($amount == "no") {
	    $this->removeComponent($this->btn_res);
	    return;
	}
	if ($amount == "max") {
	    $this->btn_res->setText(array('$ff0Maximum', '$fffrestarts', '$ff0reached'));
	}
	else {
	    $this->btn_res->setText(array('$fffBuy', '$fffRestart', '$fff' . $amount . 'p'));
	}
    }

    public function onResize($oldX, $oldY) {
	parent::onResize($oldX, $oldY);
	$this->btn_fav->setPosX(8);
	$this->btn_fav->setPosY(-5);
	$this->btn_res->setPosX(20);
	$this->btn_res->setPosY(-5);
	$this->btn_skip->setPosX(32);
	$this->btn_skip->setPosY(-5);
    }

    public function setSkipAmount($amount) {
	if ($amount == "no") {
	    $this->removeComponent($this->btn_skip);
	    return;
	}
	if ($amount == "max") {
	    $this->btn_skip->setText(array('$ff0fMaximum', '$fffskips', '$ff0reached'));
	}
	else {
	    $this->btn_skip->setText(array('$fffBuy', '$fffSkip', '$fff' . $amount . 'p'));
	}
    }

    public function setServerInfo($login) {
	$url = 'http://reaby.kapsi.fi/ml/addfavourite.php?login=' . rawurldecode($login);
	$this->btn_fav->setManialink($url);
    }

    function destroy() {
	$this->clearComponents();
	parent::destroy();
    }

}

?>
