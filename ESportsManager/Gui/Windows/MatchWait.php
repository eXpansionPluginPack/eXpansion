<?php

namespace ManiaLivePlugins\eXpansion\ESportsManager\Gui\Windows;

/**
 * Description of Stop
 *
 * @author Reaby
 */
class MatchWait extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

    protected $label_halt;
    protected $label_reason;
    protected $frame;
    protected $btn_continue;
    protected $btn_select;
    private $admin = false;
    private $action;

    protected function onConstruct()
    {
        parent::onConstruct();
        $login = $this->getRecipient();
        $this->setTitle(__("Wait", $login));
        $this->frame = new \ManiaLive\Gui\Controls\Frame(2, -4);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Column());
        $this->addComponent($this->frame);

        $this->label_halt = new \ManiaLib\Gui\Elements\Label(60, 12);
        $this->label_halt->setAlign("center", "top");
        $this->label_halt->setStyle(\ManiaLib\Gui\Elements\Format::TextRaceMessageBig);
        $this->label_halt->setText(__("Please wait...", $login));
        $this->label_halt->setTextColor("000");
        $this->label_halt->setTextEmboss();
        $this->frame->addComponent($this->label_halt);

        $this->label_reason = new \ManiaLib\Gui\Elements\Label(90, 8);
        $this->label_reason->setAlign("center", "top");
        $this->label_reason->setTextColor("000");
        $this->label_reason->setText(__("Admin is selecting next match server settings, please wait...", $login));
        $this->frame->addComponent($this->label_reason);


        $this->btn_select = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(35);
        $this->btn_select->setAlign("center", "top");
        $this->btn_select->setText(__("Choose Settings", $login));
        $this->btn_select->colorize("0d09");

        $this->btn_continue = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(35);
        $this->btn_continue->setAlign("center", "top");
        $this->btn_continue->setText(__("Force Continue", $login));
        $this->btn_continue->colorize("0d09");

        $this->setAlign("center");
    }

    function onResize($oldX, $oldY)
    {
        $this->label_halt->setPosX($this->sizeX / 2);
        $this->label_reason->setPosX($this->sizeX / 2);
        $this->btn_select->setPosX(($this->sizeX / 2));
        $this->btn_continue->setPosX(($this->sizeX / 2));

        parent::onResize($oldX, $oldY);
    }

    function setAdminAction($select, $continue)
    {
        $this->admin = true;
        $this->btn_select->setAction($select);
        $this->frame->addComponent($this->btn_select);

        $this->btn_continue->setAction($continue);
        $this->frame->addComponent($this->btn_continue);
    }
}
