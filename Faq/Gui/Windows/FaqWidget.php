<?php

namespace ManiaLivePlugins\eXpansion\Faq\Gui\Windows;

/**
 * Description of FaqWidget
 *
 * @author Reaby
 */
class FaqWidget extends \ManiaLivePlugins\eXpansion\Gui\Windows\Widget {

    public static $mainPlugin;
    protected $frame, $label_help, $icon_help;
    private $action_help;

    protected function onConstruct() {
        parent::onConstruct();
        $login = $this->getRecipient();

		$this->setName("Faq Widget");
		
        $bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround(7, 6);
        $bg->setPosition(-2, -3);
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

        /* $this->label_help = new \ManiaLib\Gui\Elements\Label(27, 7);
          $this->label_help->setStyle(\ManiaLib\Gui\Elements\Label::TextCardRaceRank);
          $this->label_help->setText(__("Help Topics", $login));
          $this->label_help->setScale(0.55);
          $this->label_help->setAlign("left", "center");
          $this->label_help->setAction($this->action_help);
          $this->frame->addComponent($this->label_help); */
    }

}

?>
