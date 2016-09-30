<?php

namespace ManiaLivePlugins\eXpansion\Faq\Gui\Windows;

/**
 * Description of FaqWidget
 *
 * @author Reaby
 */
class FaqWidget extends \ManiaLivePlugins\eXpansion\Gui\Widgets\Widget
{

    public static $mainPlugin;
    protected $frame;
    protected $label_help;
    protected $icon_help;
    protected $action_help;

    protected function eXpOnBeginConstruct()
    {
        parent::eXpOnBeginConstruct();
        $this->setName("Faq Widget");
        $login = $this->getRecipient();

        $bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround(7, 6);
        $this->addComponent($bg);

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line(40));
        $this->addComponent($this->frame);

        $this->action_help = $this->createAction(array(self::$mainPlugin, "showFaq"), $login);

        $this->icon_help = new \ManiaLib\Gui\Elements\UIConstructionSimple_Buttons();
        $this->icon_help->setSubStyle("Help");
        $this->icon_help->setAction($this->action_help);
        $this->icon_help->setScale(.8);
        $this->icon_help->setPositionX(.5);
        $this->frame->addComponent($this->icon_help);
    }
}
