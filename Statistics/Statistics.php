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

    function exp_onInit() {
	//The Database plugin is needed. 
	$this->addDependency(new \ManiaLive\PluginHandler\Dependency("\\ManiaLivePlugins\\eXpansion\\Database\\Database"));
    }

    public function exp_onReady() {
	parent::exp_onReady();
	$this->enableDatabase();
	$aHandler = \ManiaLive\Gui\ActionHandler::getInstance();

	$menu = new Gui\Controls\Menu();
	$menu->setSize(70, 100);
	$menu->setScale(.8);

	$menu->addItem('Planets Related', -1, '2B2');
	$menu->addItem('Top Income sources', $aHandler->createAction(array($this, 'showTopIncome')));
	$menu->addItem('Top Donators', $aHandler->createAction(array($this, 'showTopDonators')));
	$menu->addItem('Top Donators All servers', $aHandler->createAction(array($this, 'showTopDonatorsTotal')));
	$menu->addItem('Top nb Donations', $aHandler->createAction(array($this, 'showQTopDonators')));
	$menu->addItem('Top nb Donations', $aHandler->createAction(array($this, 'showQTopDonators')));
	$menu->addItem('Top nb Donations All Servers', $aHandler->createAction(array($this, 'showTopQDonatorsTotal')));

	$menu->addItem('Players Related', -1, '2B2');
	$menu->addItem('Top Winners', $aHandler->createAction(array($this, 'showTopWinners')));
	$menu->addItem('Top Online Time', $aHandler->createAction(array($this, 'showTopOnline')));
	$menu->addItem('Top Play Time', $aHandler->createAction(array($this, 'showTopPlayTime')));
	$menu->addItem('Top nb Finish', $aHandler->createAction(array($this, 'showTopFinish')));
	$menu->addItem('Top nb Map Played', $aHandler->createAction(array($this, 'showTopTrackPlay')));

	$menu->addItem('Country Related', -1, '2B2');
	$menu->addItem('Top Country by Finish', $aHandler->createAction(array($this, 'showTopFinishCountry')));
	$menu->addItem('Top Country Online', $aHandler->createAction(array($this, 'showTopOnlineCountry')));
	$menu->addItem('Top Winning Country', $aHandler->createAction(array($this, 'showTopWinnerCountry')));
	$menu->addItem('Top Country by nb Player', $aHandler->createAction(array($this, 'showTopCountry')));

	Gui\Windows\StatsWindow::$menuFrame = $menu;

	$this->setPublicMethod("showTopWinners");
	$this->registerChatCommand("stats", "showTopWinners", 0, true);
    }

    public function closeAllWindows($login) {
	\ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\ServerTopIncome::Erase($login);
	\ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\ServerDonationAmount::Erase($login);
	\ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\ServerDonationAmountTotal::Erase($login);
	\ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\ServerDonationCountTotal::Erase($login);
	\ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\ServerDonationCountTotal::Erase($login);
	\ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\ServerDonationCount::Erase($login);
	\ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\Winners::Erase($login);
	\ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\OnlineTime::Erase($login);
	\ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\OnlineTime::Erase($login);
	\ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\TrackPlay::Erase($login);
	\ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\Finish::Erase($login);
	\ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\CountryFinish::Erase($login);
	\ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\CountryOnlineTime::Erase($login);
	\ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\CountryWinner::Erase($login);
	\ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\Country::Erase($login);
    }

    public function showTopIncome($login) {

	if (!empty($this->donateConfig->toLogin))
	    $toLogin = $this->donateConfig->toLogin;
	else
	    $toLogin = $this->storage->serverLogin;

	$sql = 'SELECT transaction_plugin as plugin, transaction_subject as subject, SUM(transaction_amount) as totalPlanets'
		. ' FROM exp_planet_transaction'
		. ' WHERE transaction_toLogin = ' . $this->db->quote($toLogin)
		. ' GROUP BY transaction_plugin, transaction_subject'
		. ' ORDER BY totalPlanets DESC'
		. ' LIMIT 0, 100';

	$datas = $this->getData($sql);

	$this->closeAllWindows($login);
	$window = \ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\ServerTopIncome::Create($login);
	$window->setTitle(__('Top Planet Incomes', $login));
	$window->centerOnScreen();
	$window->populateList($datas);
	$window->setSize(140, 100);
	$window->show();
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

	$this->closeAllWindows($login);
	$window = \ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\ServerDonationAmountTotal::Create($login);
	$window->setTitle(__('Top Donators(Amount)', $login));
	$window->centerOnScreen();
	$window->populateList($datas);
	$window->setSize(140, 100);
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

	$this->closeAllWindows($login);
	$window = \ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\ServerDonationAmount::Create($login);
	$window->setTitle(__('Top Server Donators(Amount)', $login));
	$window->centerOnScreen();
	$window->populateList($datas);
	$window->setSize(140, 100);
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

	$this->closeAllWindows($login);
	$window = \ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\ServerDonationCountTotal::Create($login);
	$window->setTitle(__('Top Donators(Amount)', $login));
	$window->centerOnScreen();
	$window->populateList($datas);
	$window->setSize(140, 100);
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

	$this->closeAllWindows($login);
	$window = \ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\ServerDonationCount::Create($login);
	$window->setTitle(__('Top Server Donators(Amount)', $login));
	$window->centerOnScreen();
	$window->populateList($datas);
	$window->setSize(140, 100);
	$window->show();
    }

    public function showTopWinners($login) {

	$this->storage->serverLogin;
	$sql = 'SELECT player_login as login, player_nickname as nickname, player_wins as wins'
		. ' FROM exp_players'
		. ' WHERE player_wins > 0'
		. ' ORDER BY wins DESC'
		. ' LIMIT 0, 100';

	$datas = $this->getData($sql);

	$this->closeAllWindows($login);
	$window = \ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\Winners::Create($login);
	$window->setTitle(__('Top Server Winners', $login));
	$window->centerOnScreen();
	$window->populateList($datas);
	$window->setSize(140, 100);
	$window->show();
    }

    public function showTopOnline($login) {

	$this->storage->serverLogin;
	$sql = 'SELECT player_login as login, player_nickname as nickname, player_timeplayed as time'
		. ' FROM exp_players'
		. ' ORDER BY time DESC'
		. ' LIMIT 0, 100';

	$datas = $this->getData($sql);

	$this->closeAllWindows($login);
	$window = \ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\OnlineTime::Create($login);
	$window->setTitle(__('Top Online Time', $login));
	$window->centerOnScreen();
	$window->populateList($datas);
	$window->setSize(140, 100);
	$window->show();
    }

    public function showTopPlayTime($login) {

	$this->storage->serverLogin;
	$sql = 'SELECT player_login as login, player_nickname as nickname, SUM(record_nbFinish * record_avgScore)/1000 as time'
		. ' FROM exp_records, exp_players'
		. ' WHERE record_playerlogin = player_login'
		. '	AND record_nbFinish > 0'
		. ' GROUP BY player_login, player_nickname'
		. ' ORDER BY time DESC'
		. ' LIMIT 0, 100';

	$datas = $this->getData($sql);

	$this->closeAllWindows($login);
	$window = \ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\OnlineTime::Create($login);
	$window->setTitle(__('Top Play Time', $login));
	$window->centerOnScreen();
	$window->populateList($datas);
	$window->setSize(140, 100);
	$window->show();
    }

    public function showTopTrackPlay($login) {

	$this->storage->serverLogin;
	$sql = 'SELECT player_login as login, player_nickname as nickname, count(*) as nb'
		. ' FROM exp_records, exp_players'
		. ' WHERE record_playerlogin = player_login'
		. ' GROUP BY player_login, player_nickname'
		. ' HAVING count(*) > 0'
		. ' ORDER BY nb DESC'
		. ' LIMIT 0, 100';

	$datas = $this->getData($sql);

	$this->closeAllWindows($login);
	$window = \ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\TrackPlay::Create($login);
	$window->setTitle(__('Top Number tracks played', $login));
	$window->centerOnScreen();
	$window->populateList($datas);
	$window->setSize(140, 100);
	$window->show();
    }

    public function showTopFinish($login) {

	$this->storage->serverLogin;
	$sql = 'SELECT player_login as login, player_nickname as nickname, SUM(record_nbFinish) as nb'
		. ' FROM exp_records, exp_players'
		. ' WHERE record_playerlogin = player_login'
		. ' GROUP BY player_login, player_nickname'
		. ' HAVING SUM(record_nbFinish) > 0'
		. ' ORDER BY nb DESC'
		. ' LIMIT 0, 100';

	$datas = $this->getData($sql);

	$this->closeAllWindows($login);
	$window = \ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\Finish::Create($login);
	$window->setTitle(__('Top Finish', $login));
	$window->centerOnScreen();
	$window->populateList($datas);
	$window->setSize(140, 100);
	$window->show();
    }

    public function showTopFinishCountry($login) {

	$this->storage->serverLogin;
	$sql = 'SELECT player_nation as nation, SUM(record_nbFinish) as nb'
		. ' FROM exp_records, exp_players'
		. ' WHERE record_playerlogin = player_login'
		. ' GROUP BY player_nation'
		. ' HAVING SUM(record_nbFinish) > 0'
		. ' ORDER BY nb DESC'
		. ' LIMIT 0, 100';

	$datas = $this->getData($sql);

	$this->closeAllWindows($login);
	$window = \ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\CountryFinish::Create($login);
	$window->setTitle(__('Country with top Finish', $login));
	$window->centerOnScreen();
	$window->populateList($datas);
	$window->setSize(140, 100);
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

	$this->closeAllWindows($login);
	$window = \ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\CountryOnlineTime::Create($login);
	$window->setTitle(__('Country with top Online Time', $login));
	$window->centerOnScreen();
	$window->populateList($datas);
	$window->setSize(140, 100);
	$window->show();
    }

    public function showTopWinnerCountry($login) {

	$this->storage->serverLogin;
	$sql = 'SELECT player_nation as nation, SUM(player_wins) as nb'
		. ' FROM exp_players'
		. ' GROUP BY player_nation'
		. ' HAVING SUM(player_wins) > 0'
		. ' ORDER BY nb DESC'
		. ' LIMIT 0, 100';

	$datas = $this->getData($sql);

	$this->closeAllWindows($login);
	$window = \ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\Country::Create($login);
	$window->setTitle(__('Most winning country', $login));
	$window->centerOnScreen();
	$window->populateList($datas);
	$window->setSize(140, 100);
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

	$this->closeAllWindows($login);
	$window = \ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\Country::Create($login);
	$window->setTitle(__('Country with most players', $login));
	$window->centerOnScreen();
	$window->populateList($datas);
	$window->setSize(140, 100);
	$window->show();
    }

    public function getData($sql) {
	$dbData = $this->db->execute($sql);

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
