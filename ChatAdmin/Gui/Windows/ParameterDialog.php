<?php
namespace ManiaLivePlugins\eXpansion\ChatAdmin\Gui\Windows;


use ManiaLib\Gui\Layouts\Column;
use ManiaLib\Gui\Layouts\Line;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\ChatAdmin\ChatAdmin;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button;
use ManiaLivePlugins\eXpansion\Gui\Elements\Dropdown;
use ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use ManiaLivePlugins\eXpansion\Gui\Windows\Window;

class ParameterDialog extends Window
{

    /** @var  Inputbox */
    protected $inputbox;
    /** @var  Button */
    protected $btn_ok;
    /** @var  Button */
    protected $btn_cancel;
    /** @var  Frame */
    protected $frame;
    /** @var  Frame */
    protected $frm;
    /** @var  Dropdown */
    protected $compobox;

    protected $adminAction;
    protected $adminParams;

    /** @var ChatAdmin */
    public static $mainPlugin;

    protected function onConstruct()
    {
        parent::onConstruct();
        $login = $this->getRecipient();

        $this->frm = new Frame(2, -6);
        $this->frm->setLayout(new Column());
        $this->addComponent($this->frm);

        $this->inputbox = new Inputbox("parameter", 100);
        $this->inputbox->setLabel("Give a reason");
        $this->inputbox->setText("Bad Behavior");
        $this->inputbox->setAlign("left", "top");
        $this->inputbox->setSize(100, 6);
        $this->frm->addComponent($this->inputbox);

        $items = array(
            "permanent",
            "30 seconds",
            "5 min",
            "10 min",
            "15 min",
            "30 min",
            "1 hour",
            "1 day",
            "5 day",
            "week",
            "month"
        );
        $this->compobox = new Dropdown("select", $items);
        $this->compobox->setAlign("left", "top");


        $this->frame = new Frame(0, 0, new Line());
        $this->frame->setPosition("right", "top");
        $this->addComponent($this->frame);

        $action = $this->createAction(array($this, "ok"));
        $this->btn_ok = new Button();
        $this->btn_ok->setText(__("Ok", $login));
        $this->btn_ok->setAction($action);
        $this->frame->addComponent($this->btn_ok);

        $action = $this->createAction(array($this, "cancel"));
        $this->btn_cancel = new Button();
        $this->btn_cancel->setText(__("Cancel", $login));
        $this->btn_cancel->setAction($action);
        $this->frame->addComponent($this->btn_cancel);
        $this->setSize(110, 20);
    }

    public function onResize($oldX, $oldY)
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
