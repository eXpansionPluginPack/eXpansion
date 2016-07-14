<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Resskip\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Gui\Elements\WidgetButton;
use ManiaLivePlugins\eXpansion\Gui\Widgets\Widget;

class ResSkipButtons extends Widget
{

    /**
     * @var WidgetButton
     */
    protected $btn_res, $btn_skip, $btn_fav, $edgeWidget;

    protected function eXpOnBeginConstruct()
    {
        parent::eXpOnBeginConstruct();
        $line = new \ManiaLive\Gui\Controls\Frame(6, 0);
        $line->setAlign("center", "top");
        $line->setLayout(new \ManiaLib\Gui\Layouts\Line());

        $this->btn_skip = new WidgetButton(10, 10);
        $this->btn_skip->setPositionZ(-1);
        $line->addComponent($this->btn_skip);

        $this->btn_res = new WidgetButton(10, 10);
        $this->btn_res->setPositionZ(-1);
        $line->addComponent($this->btn_res);

        $this->btn_fav = new WidgetButton(10, 10);
        $this->btn_fav->setPositionZ(-1);
        $this->btn_fav->setText(
            array(
                eXpGetMessage('AddToFav:$s$fffAdd'),
                eXpGetMessage('AddToFav:$s$fffto'),
                eXpGetMessage('AddToFav:$s$fffFav\'s'),
            )
        );
        $line->addComponent($this->btn_fav);

        $this->addComponent($line);

        $this->setName("Skip and Res Buttons");

        $this->edgeWidget = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Gui/Scripts/EdgeWidget");
        $this->registerScript($this->edgeWidget);


    }

    public function setActions($res, $skip)
    {
        if (is_object($this->btn_res)) {
            $this->btn_res->setAction($res);
        }
        if (is_object($this->btn_skip)) {
            $this->btn_skip->setAction($skip);
        }
    }

    public function setResAmount($amount)
    {
        if (is_numeric($amount)) {
            $this->btn_res->setText(
                array(
                    eXpGetMessage('AddToFav:$ff0Buy'),
                    eXpGetMessage('AddToFav:$fffRestart'),
                    '$ff0' . $amount . 'p'));
        }

        if ($amount == "max") {
            $this->btn_res->setText(
                array(
                    eXpGetMessage('AddToFav:$ff0Max'),
                    eXpGetMessage('AddToFav:$fffrestarts'),
                    eXpGetMessage('AddToFav:$ff0reached'),
                )
            );
            $this->btn_res->setAction(null);
        }
    }

    public function setSkipAmount($amount)
    {
        if (is_numeric($amount)) {
            $this->btn_skip->setText(
                array(
                    eXpGetMessage('AddToFav:$ff0Buy'),
                    eXpGetMessage('AddToFav:$fffSkip'),
                    '$ff0' . $amount . 'p')
            );
        }

        if ($amount == "max") {
            $this->btn_skip->setText(
                array(
                    eXpGetMessage('AddToFav:$ff0Max'),
                    eXpGetMessage('AddToFav:$fffskips'),
                    eXpGetMessage('AddToFav:$ff0reached'),
                )
            );
            $this->btn_skip->setAction(null);
        }
    }

    public function setServerInfo($login)
    {
        $url = 'http://reaby.kapsi.fi/ml/addfavourite.php?login=' . rawurldecode($login);
        $this->btn_fav->setManialink($url);
    }

}
