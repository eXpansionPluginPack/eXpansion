<?php

namespace ManiaLivePlugins\eXpansion\Chat_Admin\Gui\Windows;

use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button as OkButton;
use ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;

class ParameterDialog extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

    protected $inputbox;
    protected $btn_ok;
    protected $btn_cancel;
    protected $frame;
    protected $frm;
    protected $compobox;
    protected $adminAction;
    protected $adminParams;

    /** @var \ManiaLivePlugins\eXpansion\Chat_Admin\Chat_Admin */
    public static $mainPlugin;

    protected function onConstruct()
    {
        parent::onConstruct();
        $login = $this->getRecipient();

        $this->frm = new \ManiaLive\Gui\Controls\Frame(2, -6);
        $this->frm->setLayout(new \ManiaLib\Gui\Layouts\Column());
        $this->addComponent($this->frm);

        $this->inputbox = new Inputbox("parameter", 100);
        $this->inputbox->setLabel("Give reason");
        $this->inputbox->setText("Bad Behavior");
        $this->inputbox->setAlign("left", "top");
        $this->inputbox->setSize(100, 6);
        $this->frm->addComponent($this->inputbox);

        $items = array(
            "30 seconds",
            "5 min",
            "10 min",
            "15 min",
            "30min",
            "1 hour",
            "1 day",
            "5 day",
            "week",
            "month",
            "permanent"
        );
        $this->compobox = new \ManiaLivePlugins\eXpansion\Gui\Elements\Dropdown("select", $items);
        $this->compobox->setAlign("left", "top");


        $this->frame = new \ManiaLive\Gui\Controls\Frame(0, 0, new \ManiaLib\Gui\Layouts\Line());
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

    function onResize($oldX, $oldY)
    {
        $this->frame->setSize($this->sizeX, $this->sizeY);
        $this->frame->setPosition($this->sizeX - 48, -$this->sizeY + 6);
        parent::onResize($oldX, $oldY);
    }

    protected function onShow()
    {
        if ($this->adminAction != "kick") {
            $this->frm->addComponent($this->compobox);
        }
    }

    public function setData($action, $params)
    {
        $login = $this->getRecipient();
        $this->adminAction = $action;
        $this->adminParams = $params;
        $this->btn_ok->setText(__($action, $login));
    }

    public function ok($login, $inputbox)
    {
        if ($this->adminAction == "kick") {
            $params = $this->adminAction . " " . $this->adminParams . " " . $inputbox['parameter'];
        } else {
            if (empty($inputbox['select'])) {
                $inputbox['select'] = 0;
            }
            $items = $this->compobox->getDropdownItems();
            $params = $this->adminAction
                . " " . $this->adminParams . " " . $inputbox['parameter']
                . ", duration: " . $items[$inputbox['select']];
            $prms = explode(" ", $this->adminParams);
            self::$mainPlugin->addActionDuration($prms[0], $this->adminAction, $items[$inputbox['select']]);
        }
        AdminGroups::getInstance()->adminCmd($login, $params);
        $this->Erase($login);
    }

    public function cancel($login)
    {
        $this->Erase($login);
    }

    public function destroy()
    {
        $this->btn_ok->destroy();
        $this->inputbox->destroy();
        $this->btn_cancel->destroy();
        parent::destroy();
    }
}
