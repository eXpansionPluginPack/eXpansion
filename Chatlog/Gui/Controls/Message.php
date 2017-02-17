<?php
namespace ManiaLivePlugins\eXpansion\Chatlog\Gui\Controls;

use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Layouts\Line;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\Chatlog\Structures\ChatMessage;
use ManiaLivePlugins\eXpansion\Gui\Control;
use ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround;
use ManiaLivePlugins\eXpansion\Gui\Gui;

class Message extends Control
{

    protected $bg;
    protected $label_time;
    protected $label_nickname;
    protected $label_text;
    protected $label_login;
    protected $frame;
    private $widths = array();

    /**
     *
     * @param int $indexNumber
     * @param ChatMessage $message
     * @param float[] $widths
     * @param int $sizeX
     */
    public function __construct(
        $indexNumber,
        ChatMessage $message,
        $widths,
        $sizeX
    )
    {
        $sizeY = 6;
        $this->widths = $widths;

        $totalWidths = Gui::getScaledSize($widths, $sizeX);

        $this->bg = new ListBackGround($indexNumber, $sizeX, $sizeY);
        $this->addComponent($this->bg);

        $this->frame = new Frame();
        $this->frame->setSize($sizeX, $sizeY);
        $this->frame->setLayout(new Line());
        $this->addComponent($this->frame);

        $this->label_time = new Label($totalWidths[0], 4);
        $this->label_time->setAlign('left', 'center');
        $this->label_time->setText(date("H:i", $message->time));
        $this->label_time->setScale(0.8);
        $this->frame->addComponent($this->label_time);

        $this->label_nickname = new Label($totalWidths[1], 4);
        $this->label_nickname->setAlign('left', 'center');
        $this->label_nickname->setText($message->nickName);
        $this->label_nickname->setScale(0.8);
        $this->frame->addComponent($this->label_nickname);

        $this->label_text = new Label($totalWidths[2], 4);
        $this->label_text->setAlign('left', 'center');
        $this->label_text->setText($message->text);
        $this->label_text->setScale(0.8);
        $this->frame->addComponent($this->label_text);


        $this->setSize($sizeX, $sizeY);
    }

    protected function onResize($oldX, $oldY)
    {
        $this->bg->setSize($this->sizeX + 6, $this->sizeY);
        $this->bg->setPosX(-2);

        $this->frame->setSize($this->sizeX, $this->sizeY);

        $totalWidths = Gui::getScaledSize($this->widths, $this->getSizeX());
        $this->label_time->setSizeX($totalWidths[0] / .8);
        $this->label_nickname->setSizeX($totalWidths[1] / .8);
        $this->label_text->setSizeX($totalWidths[2] / .8 - 2);
    }

    // manialive 3.1 override to do nothing.
    public function destroy()
    {
    }

    /*
     * custom function to remove contents.
     */

    public function erase()
    {
        $this->widths = null;
        $this->frame->clearComponents();
        $this->frame->destroy();
        $this->destroyComponents();
        parent::destroy();
    }
}
