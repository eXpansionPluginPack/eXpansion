<?php

namespace ManiaLivePlugins\eXpansion\Statistics;

use ManiaLive\Event\Dispatcher;
use ManiaLive\Utilities\Console;
use ManiaLivePlugins\eXpansion\Core\i18n\Message;
use \ManiaLivePlugins\eXpansion\LocalRecords\Config;
use \ManiaLivePlugins\eXpansion\LocalRecords\Events\Event;
use ManiaLivePlugins\eXpansion\LocalRecords\Structures\Record;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;

class Statistics extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    public function exp_onReady() {
        parent::exp_onReady();
        $this->enableDatabase();


        $cmd = $this->registerChatCommand("topdonators", 'showTopDonators', 0, true);
        $cmd->help = "Will  display the list of the top donators to this server";

        $cmd = $this->registerChatCommand("topdonatorst", 'showTopDonatorsTotal', 0, true);
        $cmd->help = "Will  display the list of the top donators in the database(all servers)";

        $cmd = $this->registerChatCommand("topdonatorsq", 'showQTopDonators', 0, true);
        $cmd->help = "Will  display the list of the top donators of the server by number of donation instead of amount";

        $cmd = $this->registerChatCommand("topdonatorsqt", 'showTopQDonatorsTotal', 0, true);
        $cmd->help = "Will  display the list of the top donators of the database by number of donation instead of amount";

        $cmd = $this->registerChatCommand("topwinners", 'showTopWinners', 0, true);
        $cmd->help = "Will  display the list of the top winning players";

        $cmd = $this->registerChatCommand("toponlinetime", 'showTopOnline', 0, true);
        $cmd->help = "Will  display the list of the players who spent most their time on this server";

        $cmd = $this->registerChatCommand("topplaytime", 'showTopPlayTime', 0, true);
        $cmd->help = "Will  display the list of the players who spent most their time playing on this server";

        $cmd = $this->registerChatCommand("topfinish", 'showTopFinish', 0, true);
        $cmd->help = "Will  display the list of the players who finished tracks most often";

        $cmd = $this->registerChatCommand("topmapplayed", 'showTopTrackPlay', 0, true);
        $cmd->help = "Will  display the list of the players who played on most tracks";

        $cmd = $this->registerChatCommand("topcountryfinish", 'showTopFinishCountry', 0, true);
        $cmd->help = "Will  display the list of the countries which has players who finished tracks most often";

        $cmd = $this->registerChatCommand("topcountryonline", 'showTopOnlineCountry', 0, true);
        $cmd->help = "Will  display the list of the countries which has players who finished tracks most often";

        $cmd = $this->registerChatCommand("topcountry", 'showTopCountry', 0, true);
        $cmd->help = "Will  display the list of the countries which has the most players";
    }

    public function showTopDonatorsTotal($login) {

        $this->storage->serverLogin;
        $sql = 'SELECT transaction_fromLogin as login, player_nickname as nickname, SUM(transaction_amount) as totalPlanets'
                . ' FROM exp_planet_transaction, exp_players'
                . ' WHERE transaction_subject = \'server_donation\''
                . ' AND transaction_fromLogin = player_login'
                . ' GROUP BY transaction_fromLogin, player_nickname'
                . ' ORDER BY totalPlanets DESC'
                . ' LIMIT 0, 100';

        $datas = $this->getData($sql);

        \ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\ServerDonationAmountTotal::Erase($login);
        $window = \ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\ServerDonationAmountTotal::Create($login);
        $window->setTitle(__('Top Donators(Amount)', $login));
        $window->centerOnScreen();
        $window->populateList($datas);
        $window->setSize(70, 100);
        $window->show();
    }

    public function showTopDonators($login) {

        $this->storage->serverLogin;
        $sql = 'SELECT transaction_fromLogin as login, player_nickname as nickname, SUM(transaction_amount) as totalPlanets'
                . ' FROM exp_planet_transaction, exp_players'
                . ' WHERE transaction_toLogin = ' . $this->db->quote($this->storage->serverLogin) . ''
                . ' AND transaction_subject = \'server_donation\''
                . ' AND transaction_fromLogin = player_login'
                . ' GROUP BY transaction_fromLogin, player_nickname'
                . ' ORDER BY totalPlanets DESC'
                . ' LIMIT 0, 100';

        $datas = $this->getData($sql);

        \ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\ServerDonationAmount::Erase($login);
        $window = \ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\ServerDonationAmount::Create($login);
        $window->setTitle(__('Top Server Donators(Amount)', $login));
        $window->centerOnScreen();
        $window->populateList($datas);
        $window->setSize(70, 100);
        $window->show();
    }

    public function showTopQDonatorsTotal($login) {

        $this->storage->serverLogin;
        $sql = 'SELECT transaction_fromLogin as login, player_nickname as nickname, count(*) as nb'
                . ' FROM exp_planet_transaction, exp_players'
                . ' WHERE transaction_subject = \'server_donation\''
                . ' AND transaction_fromLogin = player_login'
                . ' GROUP BY transaction_fromLogin, player_nickname'
                . ' ORDER BY nb DESC'
                . ' LIMIT 0, 100';

        $datas = $this->getData($sql);

        \ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\ServerDonationCountTotal::Erase($login);
        $window = \ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\ServerDonationCountTotal::Create($login);
        $window->setTitle(__('Top Donators(Amount)', $login));
        $window->centerOnScreen();
        $window->populateList($datas);
        $window->setSize(70, 100);
        $window->show();
    }

    public function showQTopDonators($login) {

        $this->storage->serverLogin;
        $sql = 'SELECT transaction_fromLogin as login, player_nickname as nickname, count(*) as nb'
                . ' FROM exp_planet_transaction, exp_players'
                . ' WHERE transaction_toLogin = ' . $this->db->quote($this->storage->serverLogin) . ''
                . ' AND transaction_subject = \'server_donation\''
                . ' AND transaction_fromLogin = player_login'
                . ' GROUP BY transaction_fromLogin, player_nickname'
                . ' ORDER BY nb DESC'
                . ' LIMIT 0, 100';

        $datas = $this->getData($sql);

        \ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\ServerDonationCount::Erase($login);
        $window = \ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\ServerDonationCount::Create($login);
        $window->setTitle(__('Top Server Donators(Amount)', $login));
        $window->centerOnScreen();
        $window->populateList($datas);
        $window->setSize(70, 100);
        $window->show();
    }

    public function showTopWinners($login) {

        $this->storage->serverLogin;
        $sql = 'SELECT player_login as login, player_nickname as nickname, player_wins as wins'
                . ' FROM exp_players'
                . ' ORDER BY wins DESC'
                . ' LIMIT 0, 100';

        $datas = $this->getData($sql);

        \ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\Winners::Erase($login);
        $window = \ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\Winners::Create($login);
        $window->setTitle(__('Top Server Winners', $login));
        $window->centerOnScreen();
        $window->populateList($datas);
        $window->setSize(70, 100);
        $window->show();
    }

    public function showTopOnline($login) {

        $this->storage->serverLogin;
        $sql = 'SELECT player_login as login, player_nickname as nickname, player_timeplayed as time'
                . ' FROM exp_players'
                . ' ORDER BY time DESC'
                . ' LIMIT 0, 100';

        $datas = $this->getData($sql);

        \ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\OnlineTime::Erase($login);
        $window = \ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\OnlineTime::Create($login);
        $window->setTitle(__('Top Online Time', $login));
        $window->centerOnScreen();
        $window->populateList($datas);
        $window->setSize(70, 100);
        $window->show();
    }

    public function showTopPlayTime($login) {

        $this->storage->serverLogin;
        $sql = 'SELECT player_login as login, player_nickname as nickname, SUM(record_nbFinish * record_avgScore) as time'
                . ' FROM exp_records, exp_players'
                . ' WHERE record_playerlogin = player_login'
                . ' GROUP BY player_login, player_nickname'
                . ' ORDER BY time DESC'
                . ' LIMIT 0, 100';

        $datas = $this->getData($sql);

        \ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\OnlineTime::Erase($login);
        $window = \ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\OnlineTime::Create($login);
        $window->setTitle(__('Top Server Donators(Amount)', $login));
        $window->centerOnScreen();
        $window->populateList($datas);
        $window->setSize(70, 100);
        $window->show();
    }

    public function showTopTrackPlay($login) {

        $this->storage->serverLogin;
        $sql = 'SELECT player_login as login, player_nickname as nickname, count(*) as nb'
                . ' FROM exp_records, exp_players'
                . ' WHERE record_playerlogin = player_login'
                . ' GROUP BY player_login, player_nickname'
                . ' ORDER BY nb DESC'
                . ' LIMIT 0, 100';

        $datas = $this->getData($sql);

        \ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\TrackPlay::Erase($login);
        $window = \ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\TrackPlay::Create($login);
        $window->setTitle(__('Top Number tracks played', $login));
        $window->centerOnScreen();
        $window->populateList($datas);
        $window->setSize(70, 100);
        $window->show();
    }

    public function showTopFinish($login) {

        $this->storage->serverLogin;
        $sql = 'SELECT player_login as login, player_nickname as nickname, SUM(record_nbFinish) as nb'
                . ' FROM exp_records, exp_players'
                . ' WHERE record_playerlogin = player_login'
                . ' GROUP BY player_login, player_nickname'
                . ' ORDER BY nb DESC'
                . ' LIMIT 0, 100';

        $datas = $this->getData($sql);

        \ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\Finish::Erase($login);
        $window = \ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\Finish::Create($login);
        $window->setTitle(__('Top Finish', $login));
        $window->centerOnScreen();
        $window->populateList($datas);
        $window->setSize(70, 100);
        $window->show();
    }

    public function showTopFinishCountry($login) {

        $this->storage->serverLogin;
        $sql = 'SELECT player_nation as nation, SUM(record_nbFinish) as nb'
                . ' FROM exp_records, exp_players'
                . ' WHERE record_playerlogin = player_login'
                . ' GROUP BY player_nation'
                . ' ORDER BY nb DESC'
                . ' LIMIT 0, 100';

        $datas = $this->getData($sql);

        \ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\CountryFinish::Erase($login);
        $window = \ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\CountryFinish::Create($login);
        $window->setTitle(__('Country with top Finish', $login));
        $window->centerOnScreen();
        $window->populateList($datas);
        $window->setSize(70, 100);
        $window->show();
    }

    public function showTopOnlineCountry($login) {

        $this->storage->serverLogin;
        $sql = 'SELECT player_nation as nation, SUM(player_timeplayed) as time'
                . ' FROM exp_players'
                . ' GROUP BY player_nation'
                . ' ORDER BY time DESC'
                . ' LIMIT 0, 100';

        $datas = $this->getData($sql);

        \ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\CountryOnlineTime::Erase($login);
        $window = \ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\CountryOnlineTime::Create($login);
        $window->setTitle(__('Country with top Online Time', $login));
        $window->centerOnScreen();
        $window->populateList($datas);
        $window->setSize(70, 100);
        $window->show();
    }

    public function showTopCountry($login) {

        $this->storage->serverLogin;
        $sql = 'SELECT player_nation as nation, COUNT(*) as nb'
                . ' FROM exp_players'
                . ' GROUP BY player_nation'
                . ' ORDER BY nb DESC'
                . ' LIMIT 0, 100';

        $datas = $this->getData($sql);

        \ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\Country::Erase($login);
        $window = \ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\Country::Create($login);
        $window->setTitle(__('Country with most players', $login));
        $window->centerOnScreen();
        $window->populateList($datas);
        $window->setSize(70, 100);
        $window->show();
    }

    public function getData($sql) {
        $dbData = $this->db->query($sql);

        if ($dbData->recordCount() == 0) {
            return array();
        }


        $i = 0;
        $datas = array();
        while ($data = $dbData->fetchArray()) {
            $datas[$i] = $data;
            array_unshift($datas[$i], $i + 1);
            $i++;
        }
        return $datas;
    }

}

?>
