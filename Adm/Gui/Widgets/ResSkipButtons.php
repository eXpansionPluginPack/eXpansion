<?php

namespace ManiaLivePlugins\eXpansion\Adm\Gui\Widgets;

class ResSkipButtons extends \ManiaLivePlugins\eXpansion\Gui\Windows\Widget {

    public $btn_res;
    public $btn_skip;

    protected function onConstruct() {
        parent::onConstruct();
        echo "Started Buidling\n";
        $this->btn_res = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetButton();
        $this->btn_res->setPositionZ(-1);
        $this->addComponent($this->btn_res);

        $this->btn_skip = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetButton();
        $this->btn_skip->setPositionZ(-1);
        $this->addComponent($this->btn_skip);

        $this->setName("Skip and Res Buttons");
        $this->setSize(30,17);
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
        } else {
            $this->btn_res->setText(array('$fffPay $ff0' . $amount, '$fffto', '$ff0Restart'));
        }
    }

    public function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->btn_res->setPosX(8);
        $this->btn_res->setPosY(3);
        $this->btn_skip->setPosX(22);
        $this->btn_skip->setPosY(3);
    }

    public function setSkipAmount($amount) {
        if ($amount == "no") {
            $this->removeComponent($this->btn_skip);
            return;
        }
        if ($amount == "max") {
            $this->btn_skip->setText(array('$ff0fMaximum', '$fffskips', '$ff0reached'));
        } else {
            $this->btn_skip->setText(array('$fffPay $ff0' . $amount, '$fffto', '$ff0Skip'));
        }
    }

    function destroy() {
        $this->clearComponents();
        parent::destroy();
    }

}

?>
