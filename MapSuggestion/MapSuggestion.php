<?php

namespace ManiaLivePlugins\eXpansion\MapSuggestion;

use ManiaLib\Utils\Formatting;
use ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\Gui\Gui;
use ManiaLivePlugins\eXpansion\Gui\Structures\ButtonHook;
use ManiaLivePlugins\eXpansion\ManiaExchange\Gui\Windows\MxSearch;
use ManiaLivePlugins\eXpansion\ManiaExchange\Hooks\ListButtons;
use ManiaLivePlugins\eXpansion\ManiaExchange\Hooks\ListButtons_Event;
use ManiaLivePlugins\eXpansion\ManiaExchange\Structures\HookData;
use ManiaLivePlugins\eXpansion\MapSuggestion\Gui\Windows\MapWish;

class MapSuggestion extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin implements ListButtons_Event
{

    public function eXpOnReady()
    {
        $this->registerChatCommand("mapwish", "showMapWishWindow", 0, true);
        $this->setPublicMethod("showMapWishWindow");
        Dispatcher::register(ListButtons::getClass(), $this);
    }

    public function showMapWishWindow($login)
    {
        $window = MapWish::Create($login);
        $window->setPlugin($this);
        $window->show();
    }

    public function addMapToWish($login, $mxid, $description = null)
    {

        if (is_array($mxid)) {
            $mxid = $mxid[0];
        }

        if ($description == null || is_array($description)) {
            Gui::showNotice("no description", $login);
            return;
        }

        $player = $this->storage->getPlayerObject($login);
        $from = '"' . $player->nickName . '$z$s$fff (' . $login . ')' . '"';

        $data = "";

        $dataAccess = \ManiaLivePlugins\eXpansion\Core\DataAccess::getInstance();

        if (is_numeric($mxid)) {
            if (empty($description)) {
                Gui::showNotice(eXpGetMessage("Looks like you have not entered any description."), $login);

                return;
            }

            $gameData = \ManiaLivePlugins\eXpansion\Helpers\Helper::getPaths()->getGameDataPath();


            $file = $gameData . DIRECTORY_SEPARATOR . "map_suggestions.txt";
            $date = date_create("now");

            $data .= $date->format("d-m-Y H:i:s") . ";\"" . Formatting::stripStyles($player->nickName) . "\";\"" . $login . "\";\"" . $mxid . "\";\"" . trim($description) . "\"\r\n";

            $dataAccess->save($file, $data, true);
            Gui::showNotice(
                eXpGetMessage(
                    "Your wish has been saved\nThe server "
                    . "admin will review the wish\nand add the map if it's good enough."
                ),
                $login
            );
            MapWish::Erase($login);

            return;
        }
        Gui::showNotice(eXpGetMessage("Looks like mx id is missing or is invalid."), $login);
    }

    function openMxWindow($login)
    {
        $this->callPublicMethod("\\ManiaLivePlugins\\eXpansion\\ManiaExchange\\ManiaExchange", "mxSearch", $login, "", "", "");
    }


    function addMap($login, $mxid)
    {
        /** @var MapWish[] $window */
        $window = MapWish::Get($login);
        if ($window) {
            $window[0]->setMXid((int)$mxid);
            $window[0]->show($login);
            MxSearch::Erase($login);
        }
    }

    /**
     *
     * @param HookData $buttons
     * @param          $login
     *
     * @return mixed
     */
    public function hook_ManiaExchangeListButtons($buttons, $login)
    {
        if (isset($buttons->data['queue'])) {
            unset($buttons->data['queue']);
        }

        $button = new ButtonHook();
        $button->callback = array($this, 'addMap');
        $button->label = 'Suggest';
        $buttons->data['suggest'] = $button;
    }


    public function eXpOnUnload()
    {
        MapWish::EraseAll();
        Dispatcher::unregister(ListButtons::getClass(), $this);
        parent::eXpOnUnload();

    }
}
