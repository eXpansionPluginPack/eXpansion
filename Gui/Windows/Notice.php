<?php

namespace ManiaLivePlugins\eXpansion\Gui\Windows;

use ManiaLivePlugins\eXpansion\Core\i18n\Message;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button as OkButton;
use ManiaLivePlugins\eXpansion\Gui\Elements\DicoLabel;

class Notice extends Window
{
    protected $ok;
    protected $cancel;
    protected $actionOk;
    protected $label;
    protected $title;

    protected function onConstruct()
    {
        parent::onConstruct();
        $login = $this->getRecipient();
        $this->actionOk = $this->createAction(array($this, "Ok"));
        $this->setSize(90, 60);

        $this->ok = new OkButton();
        //$this->ok->colorize("0d0");
        $this->ok->setPosition(45, -48);
        $this->ok->setText(__("Close", $login));
        $this->ok->setAction($this->actionOk);
        $this->ok->setAlign("center", "top");
        $this->mainFrame->addComponent($this->ok);

        $this->setTitle(__("Notice", $login));
    }

    public function setMessage(Message $message, $args)
    {
        $this->label = new DicoLabel(80, 50);
        $this->label->setPosition(45, -5);
        $this->label->setAlign("center", "center");
        $this->label->setText($message, $args);
        $this->mainFrame->addComponent($this->label);
    }

    public function setRawMessage($message)
    {
        $this->label = new \ManiaLib\Gui\Elements\Label(80, 50);
        $this->label->setPosition(45, -5);
        $this->label->setAlign("center", "center");
        $this->label->setText($message);
        $this->label->setMaxline(15);
        $this->mainFrame->addComponent($this->label);
    }

    public function setTitleText($title)
    {
        $this->setTitle($title);
    }

    public function Ok($login)
    {
        $this->Erase($login);
    }

    public function destroy()
    {
        $this->destroyComponents();
        parent::destroy();
    }
}
