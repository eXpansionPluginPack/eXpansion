<?php

namespace ManiaLivePlugins\eXpansion\Gui\Windows;

use ManiaLib\Gui\Elements\Label;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button as OkButton;

class ConfirmDialog extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

    protected $ok;
    protected $cancel;
    protected $label;

    protected $actionOk;
    protected $actionCancel;

    protected $title;
    private $action;

    protected function onConstruct()
    {
        parent::onConstruct();
        $login = $this->getRecipient();
        $this->actionOk = $this->createAction(array($this, "Ok"));
        $this->actionCancel = $this->createAction(array($this, "Cancel"));


        $this->label = new Label(57, 12);
        $this->label->setPosition(3);

        $this->mainFrame->addComponent($this->label);

        $this->ok = new OkButton();
        $this->ok->colorize("0d0");
        $this->ok->setPosition(4, -6);
        $this->ok->setText(__("Yes", $login));
        $this->ok->setAction($this->actionOk);
        $this->mainFrame->addComponent($this->ok);

        $this->cancel = new OkButton();
        $this->cancel->setPosition(30, -6);
        $this->cancel->setText(__("No", $login));
        $this->cancel->colorize("d00");
        $this->cancel->setAction($this->actionCancel);
        $this->mainFrame->addComponent($this->cancel);

        $this->setSize(57, 16);
        $this->setTitle(__("Really do this ?", $login));

    }

    protected function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
    }

    public function setText($text)
    {
        $this->label->setText($text);
    }

    public function setInvokeAction($action)
    {
        $this->action = $action;
    }

    public function Ok($login)
    {
        $action = ConfirmProxy::Create($login);
        $action->setInvokeAction($this->action);
        $action->setTimeOut(1);
        $action->show();
        $this->Erase($login);
    }

    public function Cancel($login)
    {
        $this->erase($login);
    }

    public function destroy()
    {
        $this->ok->destroy();
        $this->cancel->destroy();
        $this->destroyComponents();
        parent::destroy();
    }
}
