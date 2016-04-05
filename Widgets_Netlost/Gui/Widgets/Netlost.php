<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Netlost\Gui\Widgets;

class Netlost extends \ManiaLivePlugins\eXpansion\Gui\Widgets\Widget
{

    protected $clockBg;

    private $frame, $players, $specs, $map, $author;

    private $line;

    protected function eXpOnBeginConstruct()
    {
        $this->frame = new \ManiaLive\Gui\Controls\Frame(6, 0);
        $this->frame->setAlign("left", "top");
        $layout = new \ManiaLib\Gui\Layouts\Flow(200, 12);
        $layout->setMargin(1, 0);
        $this->frame->setLayout($layout);

        $icon = new \ManiaLib\Gui\Elements\Quad(4, 4);
        $icon->setStyle("Icons64x64_2");
        $icon->setSubStyle("Disconnected");
        $icon->setId("icon");
        $icon->setHidden(true);
        $icon->setAlign("left", "center");
        $this->addComponent($icon);


        for ($x = 0; $x < 12; $x++) {
            $label = new \ManiaLib\Gui\Elements\Label(40, 5);
            $label->setAlign("left", "center");
            $label->setTextColor('fff');
            $label->setStyle('TextRaceChat');
            $label->setTextSize(2);
            $label->setTextEmboss();
            $label->setId('netlost_' . $x);
            $label->setText("");
            $this->frame->addComponent($label);
        }

        $this->addComponent($this->frame);
        $script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Widgets_Netlost\Gui\Scripts_Netlost");
        $this->registerScript($script);

        $this->setName("Netlost Widget");
    }

    public function setServerName($name)
    {
        // $this->server->setText($name);
    }

    function destroy()
    {
        $this->destroyComponents();
        parent::destroy();
    }

}

?>
