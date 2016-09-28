<?php

namespace ManiaLivePlugins\eXpansion\NetStat;

use ManiaLive\Application\Event;
use ManiaLive\Gui\ActionHandler;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\Core\Core;
use ManiaLivePlugins\eXpansion\Core\Structures\NetStat as NetStatStructure;
use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;
use ManiaLivePlugins\eXpansion\NetStat\Gui\Widgets\Helper_Netstat;
use ManiaLivePlugins\eXpansion\NetStat\Gui\Widgets\Helper_PingAnswer;

/**
 * Description of Netstat
 *
 * @author Petri
 */
class NetStat extends ExpPlugin
{

    private $postLoopStamp = 0;

    /**
     *
     * @var NetStatStructure[]
     */
    public static $netStat = array();
    private $cmdNetStat;

    public function eXpOnReady()
    {
        $this->enableApplicationEvents(Event::ON_POST_LOOP);
        $ahandler = ActionHandler::getInstance();
        $value = $ahandler->createAction(array($this, "answerNetstat"));

        $widget = Helper_Netstat::Create(null);
        $widget->setActionId($value);
        $widget->show();

        $admingroup = AdminGroups::getInstance();

        $this->cmdNetStat = AdminGroups::addAdminCommand("lag", $this, 'showNetStat', Permission::CHAT_ADMINCHAT);

    }

    public function eXpOnUnload()
    {
        parent::eXpOnUnload();
        $admingroup = AdminGroups::getInstance();
        $admingroup->removeAdminCommand($this->cmdNetStat);
        $admingroup->removeShortAllias($this->cmdNetStat);
        $this->cmdNetStat = null;
    }

    public function showNetStat($login, $params)
    {
        $window = \ManiaLivePlugins\eXpansion\NetStat\Gui\Windows\NetStatWindow::Create($login);
        $window->setTitle("Network Status");
        $window->setSize(140, 100);
        $window->show();
    }

    public function onPostLoop()
    {
        $this->postLoopStamp = microtime(true);
    }

    public function answerNetstat($login, $data)
    {
        $loop = (microtime(true) - $this->postLoopStamp) * 1000;

        if (array_key_exists($login, Core::$netStat)) {
            $stat = Core::$netStat[$login];
            $stat->updateLatency = $data['latency'] - floor($loop);
            self::$netStat[$login] = $stat;
        }

        $widget = Helper_PingAnswer::Create($login);
        $widget->setStamp($data['stamp']);
        $widget->show();
    }
}
