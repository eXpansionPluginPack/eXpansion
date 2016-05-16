<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ManiaLivePlugins\eXpansion\Dedimania\Gui\Windows;

use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Layouts\Column;
use ManiaLive\Data\Storage;
use ManiaLivePlugins\eXpansion\Gui\Elements\TextEdit;
use ManiaLivePlugins\eXpansion\Gui\Windows\Window;

/**
 * Description of DediReport
 *
 * @author Petri Järvisalo <petri.jarvisalo@gmail.com>
 */
class DediReport extends Window
{
    protected $textedit, $infolabel, $frame;

    protected function onConstruct()
    {
        parent::onConstruct();

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setLayout(new Column());
        $this->addComponent($this->frame);

        $info = new Label(100, 16);
        $info->setTextSize(2);
        $info->setText("To report invalid records for dedimania, \n please copy the following info at clipboard (ctrl-a + ctrl-c)");
        $this->frame->addComponent($info);

        $this->textedit = new TextEdit("", 100, 40);
        $this->frame->addComponent($this->textedit);

        $info = new Label(100, 32);
        $info->setTextSize(2);
        $info->setText('$fffThen go click following link:$3af' . "\n" . '$lhttp://dedimania.net/SITE/forum/viewtopic.php?id=384$l ' . "\n" . '$fffand post this information there.');
        $this->frame->addComponent($info);
    }

    public function setLogin($login)
    {
        $text = "Login to check: " . $login . "\n";
        $text .= "Map: " . Storage::getInstance()->currentMap->uId . "\n";
        $text .= "Reason: *edit your reason here*" . "\n";
        $text .= "Reportee: " . $this->getRecipient() . "\n";
        $this->textedit->setText($text);
    }
}
