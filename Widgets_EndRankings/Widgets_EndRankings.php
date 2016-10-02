<?php

namespace ManiaLivePlugins\eXpansion\Widgets_EndRankings;

use ManiaLive\PluginHandler\Dependency;

class Widgets_EndRankings extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{
    private $wasWarmup = false;

    public function eXpOnInit()
    {
        $this->addDependency(new Dependency('\ManiaLivePlugins\eXpansion\\LocalRecords\\LocalRecords'));
    }

    public function eXpOnReady()
    {
        $this->enableDedicatedEvents();
        $this->enableDatabase();
    }

    /**
     * displayWidget(string $login)
     *
     * @param string $login
     */
    public function displayWidget($login = null)
    {
        $info = Gui\Widgets\RanksPanel::Create(null);
        $info->setSize(38, 95);
        $info->setData(
            $this->callPublicMethod("\\ManiaLivePlugins\\eXpansion\\LocalRecords\\LocalRecords", "getRanks")
        );
        $info->setPosition(-160, 60);
        $info->show();

        $play = Gui\Widgets\TopPlayTime::Create(null);
        $play->setTitle(eXpGetMessage("Top Playtime"));
        $play->setLines(15);
        $play->setData($this->getTopPlaytime());
        $play->setPosition(120, 60);
        $play->show();


        $don = Gui\Widgets\Donators::Create(null);
        $don->setTitle(eXpGetMessage("Top Donators"));
        $don->setLines(15);
        $don->setData($this->getTopDonators());
        $don->setPosition(120, 0);
        $don->show();
    }

    public function getTopDonators()
    {
        $this->storage->serverLogin;
        $sql = 'SELECT transaction_fromLogin as login, player_nickname as nickname, SUM(transaction_amount) as data'
            . ' FROM exp_planet_transaction, exp_players'
            . ' WHERE transaction_toLogin = ' . $this->db->quote($this->storage->serverLogin) . ''
            . ' AND transaction_subject = \'server_donation\''
            . ' AND transaction_fromLogin = player_login'
            . ' GROUP BY transaction_fromLogin, player_nickname'
            . ' ORDER BY data DESC'
            . ' LIMIT 0, 100';
        $data = $this->db->execute($sql);

        return $data->fetchArrayOfObject();
    }

    public function getTopPlaytime()
    {
        $this->storage->serverLogin;
        $sql = 'SELECT player_nickname as nickname, player_timeplayed as longDate'
            . ' FROM exp_players'
            . ' ORDER BY longDate DESC'
            . ' LIMIT 0, 100';

        $data = $this->db->execute($sql);

        return $data->fetchArrayOfObject();
    }

    public function onBeginMap($map, $warmUp, $matchContinuation)
    {
        $this->hide();
    }

    public function onBeginMatch()
    {
        $this->hide();
    }

    public function onBeginRound()
    {
        $this->wasWarmup = $this->connection->getWarmUp();
    }

    public function onEndMatch($rankings, $winnerTeamOrMap)
    {
        if ($this->wasWarmup) {
            return;
        }
        $this->displayWidget();
    }

    public function hide()
    {
        Gui\Widgets\RanksPanel::EraseAll();
        Gui\Widgets\TopPlayTime::EraseAll();
        Gui\Widgets\Donators::EraseAll();
    }

    public function eXpOnUnload()
    {
        $this->hide();
    }
}
