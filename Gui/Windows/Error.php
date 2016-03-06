<?php

namespace ManiaLivePlugins\eXpansion\Gui\Windows;

use ManiaLivePlugins\eXpansion\Core\i18n\Message;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button as OkButton;
use ManiaLivePlugins\eXpansion\Gui\Elements\DicoLabel;

class Error extends Window
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
        $this->setSize(120, 90);
        $this->setTitle(__("Error", $login));
    }

    public function setMessage($message)
    {
        $out = $message;
        if (is_array($message)) {
            $out = "";
            foreach ($message as $line) {
                $out .= trim($line)."\n";
            }
        }

        $this->label = new \ManiaLib\Gui\Elements\Label(120, 90);
        $this->label->setAlign("left", "top");
        $this->label->setText($out);
        $this->label->setMaxline(25);
        $this->mainFrame->addComponent($this->label);
    }

    function destroy()
    {
        $this->destroyComponents();
        parent::destroy();
    }
}
?>
