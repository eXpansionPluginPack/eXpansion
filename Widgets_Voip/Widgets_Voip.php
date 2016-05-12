<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Voip;

use ManiaLib\Utils\Formatting;
use ManiaLive\Gui\ActionHandler;
use ManiaLivePlugins\eXpansion\Core\types\config\Variable;
use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;
use ManiaLivePlugins\eXpansion\Gui\Gui;
use ManiaLivePlugins\eXpansion\Widgets_Voip\Gui\Widgets\Widget;

/**
 * Description of Widgets_Advertising
 *
 * @author Petri
 */
class Widgets_Voip extends ExpPlugin
{

    /** @var Config */
    private $config;

    private $settingsChanged = false;

    static public $GotoMumble = -1;

    static public $GotoTs = -1;

    public function eXpOnReady()
    {

        $actionHandler = ActionHandler::getInstance();
        self::$GotoMumble = $actionHandler->createAction(array($this, "gotoMumble"));
        self::$GotoTs = $actionHandler->createAction(array($this, "gotoTs"));
        $this->config = Config::GetInstance();
        Gui::preloadImage($this->config->mumbleImageUrl);
        Gui::preloadImage($this->config->mumbleImageFocusUrl);
        Gui::preloadImage($this->config->tsImageUrl);
        Gui::preloadImage($this->config->tsImageFocusUrl);
        Gui::preloadUpdate();

        $this->displayWidget(null);
        $this->enableApplicationEvents();
    }

    public function onSettingsChanged(Variable $var)
    {
        $name = $var->getName();

        if (isset($this->config->$name)) {
            $this->settingsChanged = true;
        }
    }

    function onPreLoop()
    {
        if ($this->settingsChanged) {
            $this->displayWidget(null);
            $this->settingsChanged = false;
        }
    }

    public function displayWidget($login)
    {
        Widget::EraseAll();
        $providers = array("mumble", "ts");

        for ($x = 1; $x <= 2; $x++) {
            $provider = $providers[$x - 1];

            $varActive = $provider . "Active";
            if (isset($this->config->$varActive) && $this->config->$varActive) {
                $widget = Widget::Create($login, false);

                $varX = $provider . "X";
                $varY = $provider . "Y";
                $varImageUrl = $provider . "ImageUrl";
                $varImageFocusUrl = $provider . "ImageFocusUrl";
                $varUrl = "";
                $varSize = $provider . "Size";
                $varImageSizeX = $provider . "ImageSizeX";
                $varImageSizeY = $provider . "ImageSizeY";

                $widget->setPosition($this->config->$varX, $this->config->$varY, -60);
                $action = self::$GotoTs;
                if ($provider == "mumble")
                    $action = self::$GotoMumble;

                $widget->setImage($this->config->$varImageUrl, $this->config->$varImageFocusUrl, $action);
                $widget->setImageSize($this->config->$varImageSizeX, $this->config->$varImageSizeY, $this->config->$varSize);
                $widget->setPositionX($this->config->$varX);
                $widget->setPositionY($this->config->$varY);
                $widget->show();
            }
        }
    }

    public function getNick($login)
    {
        $player = $this->storage->getPlayerObject($login);
        $nick = Formatting::stripStyles($player->nickName);

        return $nick;
        //return mb_convert_encoding($nick, "latin-1", "UTF-8");
    }

    public function gotoMumble($login)
    {
        $link = "mumble://" . rawurlencode($this->getNick($login)) . "@" . $this->config->mumbleHost . ":" . $this->config->mumblePort;
        $this->connection->sendOpenLink($login, $link, 0);
    }

    public function gotoTs($login)
    {
        $link = "ts3server://" . $this->config->tsHost . "?port=" . $this->config->tsPort . "&nickname=" . rawurlencode($this->getNick($login));
        $this->connection->sendOpenLink($login, $link, 0);
    }

    public function eXpOnUnload()
    {
        Widget::EraseAll();
        self::$GotoMumble = -1;
        self::$GotoTs = -1;

        parent::eXpOnUnload();
    }

}
