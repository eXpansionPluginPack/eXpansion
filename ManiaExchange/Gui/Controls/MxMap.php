<?php

namespace ManiaLivePlugins\eXpansion\ManiaExchange\Gui\Controls;

use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Elements\Quad;
use ManiaLib\Gui\Layouts\Line;
use ManiaLive\Gui\Control;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround;
use ManiaLivePlugins\eXpansion\Gui\Gui;
use ManiaLivePlugins\eXpansion\Gui\Structures\ButtonHook;
use ManiaLivePlugins\eXpansion\ManiaExchange\Config;

class MxMap extends Control
{

    private $bg;

    private $buttons = array();
    private $actionSearch;
    private $line1, $line2;

    /**
     * @param                                                            $indexNumber
     * @param \ManiaLivePlugins\eXpansion\ManiaExchange\Structures\MxMap $map
     * @param                                                            $controller
     * @param ButtonHook[]                                               $buttons
     * @param                                                            $sizeX
     */
    function __construct($indexNumber, \ManiaLivePlugins\eXpansion\ManiaExchange\Structures\MxMap $map, $controller, $buttons, $sizeX)
    {
        $sizeY = 12;

        $id = "";

        if (property_exists($map, "trackID"))
            $id = $map->trackID;
        if (property_exists($map, "mapID"))
            $id = $map->mapID;

        $this->bg = new ListBackGround($indexNumber, $sizeX, $sizeY);
        $this->addComponent($this->bg);

        $this->actionSearch = $this->createAction(array($controller, 'search'), "", $map->username, null, null);

        $this->line1 = new Frame(0, 3);
        $this->line1->setAlign("left", "top");
        $this->line1->setSize($sizeX, $sizeY);
        $this->line1->setLayout(new Line());

        $this->line2 = new Frame(0, -3);
        $this->line2->setAlign("left", "top");
        $this->line2->setSize($sizeX, $sizeY);
        $this->line2->setLayout(new Line());

        $label = new Label(36, 6);
        $label->setAlign('left', 'center');
        $pack = str_replace("TM", "", $map->titlePack);
        if (empty($pack) || $pack == "Trackmania_2") {
            $pack = $map->environmentName;
        }
        $label->setText($pack);
        $this->line1->addComponent($label);

        $label = new Label(36, 6);
        $label->setAlign('left', 'center');
        $label->setText("");
        if ($map->vehicleName) {
            $vehicle = str_replace("Car", "", $map->vehicleName);
            if ($vehicle != $pack) {
                $label->setText("Car: " . $vehicle);
            }
        }
        $this->line2->addComponent($label);


        $label = new Label(80, 6);
        $label->setAlign('left', 'center');
        $label->setStyle("TextCardSmallScores2");
        $label->setTextEmboss();
        $label->setText(Gui::fixString($map->gbxMapName));
        $this->line1->addComponent($label);

        $info = new Label(80, 6);
        $info->setAlign('left', 'center');
        $info->setText('$fff' . Gui::fixString($map->username));
        $info->setAction($this->actionSearch);
        $info->setStyle("TextCardSmallScores2");

        $info->setScriptEvents(true);
        $this->line2->addComponent($info);


        $info = new Label(24, 4);
        $info->setAlign('left', 'center');
        $info->setText($map->difficultyName);
        $this->line1->addComponent($info);

        $info = new Label(24, 4);
        $info->setAlign('left', 'center');
        $info->setText($map->mood);
        $this->line2->addComponent($info);

        $info = new Label(18, 4);
        $info->setAlign('left', 'center');
        $info->setText($map->styleName);
        $this->line1->addComponent($info);


        $info = new Label(18, 4);
        $info->setAlign('left', 'center');
        $info->setText($map->lengthName);
        $this->line2->addComponent($info);


        if (!empty($buttons)) {
            foreach ($buttons as $button) {
                $newButton = new myButton(24, 5);
                $newButton->setText(__($button->label));
                $newButton->colorize($button->buttonColorize);
                $newButton->setAction($this->createAction($button->callback, $id));

                $this->line1->addComponent($newButton);
                $this->buttons[] = $newButton;
            }
        }

        if ($map->awardCount > 0) {
            $info = new Quad(4, 4);
            $info->setPosY(3);
            $info->setStyle("Icons64x64_1");
            $info->setSubStyle("OfficialRace");
            $info->setAlign('center', 'center');
            $this->line2->addComponent($info);

            $info = new Label(12, 5);
            $info->setPosY(3);
            $info->setAlign('center', 'center');
            $info->setText($map->awardCount);
            $this->line2->addComponent($info);
        }
        $this->addComponent($this->line1);
        $this->addComponent($this->line2);

        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
    }

    // override destroy method not to destroy its contents on manialive 3.1
    function destroy()
    {

    }

    /**
     * custom function to destroy contents when needed.
     */
    function erase()
    {
        if (!empty($this->buttons)) {
            foreach ($this->buttons as $button) {
                $button->destroy();
            }
        }

        $this->line1->clearComponents();
        $this->line1->destroy();
        $this->line2->clearComponents();
        $this->line2->destroy();
        $this->destroyComponents();
        parent::destroy();
    }

}

?>

