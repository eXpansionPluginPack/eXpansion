<?php

namespace ManiaLivePlugins\eXpansion\Chat_Admin\Gui\Windows;

use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as OkButton;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Checkbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Ratiobutton;
use ManiaLivePlugins\eXpansion\Players\Gui\Controls\Playeritem;
use ManiaLive\Gui\ActionHandler;

class ParameterDialog extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {

    protected $inputbox, $btn_ok, $btn_cancel, $frame;
    private $action, $adminAction, $adminParams;

    /** @var \ManiaLivePlugins\eXpansion\Chat_Admin\Chat_Admin */
    public static $mainPlugin;

    protected function onConstruct() {
        parent::onConstruct();
        $login = $this->getRecipient();

        $this->inputbox = new Inputbox("parameter", 100);
        $this->inputbox->setLabel("Give reason");
        $this->inputbox->setText("Bad Behavior");
        $this->inputbox->setPosition(2, -6);
        $this->addComponent($this->inputbox);

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
        $this->frame->setPosition("right", "top");
        $this->addComponent($this->frame);

        $action = $this->createAction(array($this, "ok"));
        $this->btn_ok = new OkButton();
        $this->btn_ok->setText(__("Ok", $login));
        $this->btn_ok->setAction($action);
        $this->frame->addComponent($this->btn_ok);

        $action = $this->createAction(array($this, "cancel"));
        $this->btn_cancel = new OkButton();
        $this->btn_cancel->setText(__("Cancel", $login));
        $this->btn_cancel->setAction($action);
        $this->frame->addComponent($this->btn_cancel);
        $this->setSize(110, 20);
    }

    function onResize($oldX, $oldY) {
        $this->frame->setSize($this->sizeX, $this->sizeY);
        $this->frame->setPosition($this->sizeX - 48, -$this->sizeY + 6);
        parent::onResize($oldX, $oldY);
    }

    function setData($action, $params) {
        $login = $this->getRecipient();
        $this->adminAction = $action;
        $this->adminParams = $params;
        $this->btn_ok->setText(__($action, $login));
    }

    function ok($login, $inputbox) {
        $params = $this->adminAction . " " . $this->adminParams . " " . $inputbox['parameter'];
        \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::getInstance()->adminCmd($login, $params);
    }

    function cancel($login) {
        $this->Erase($login);
    }

    function destroy() {
        $this->btn_ok->destroy();
        $this->inputbox->destroy();
        $this->btn_cancel->destroy();
        parent::destroy();
    }

}

?>
