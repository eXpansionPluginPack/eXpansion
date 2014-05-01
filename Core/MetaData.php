<?php

namespace ManiaLivePlugins\eXpansion\Core;

use ManiaLivePlugins\eXpansion\Core\types\config\types\String;
use ManiaLivePlugins\eXpansion\Core\types\config\types\Boolean;
use ManiaLivePlugins\eXpansion\Core\types\config\types\ColorCode;
use ManiaLivePlugins\eXpansion\Core\types\config\types\BasicList;

/**
 * Description of MetaData
 *
 * @author De Cramer Oliver
 */
class MetaData extends \ManiaLivePlugins\eXpansion\Core\types\config\MetaData {

    public function onBeginLoad() {
	parent::onBeginLoad();
	$this->setName("eXpansion Core");
	$this->setDescription("Core plugin, all other plugins depend on this");

	$config = Config::getInstance();

	$var = new ColorCode('Colors_admin_error', 'Color code for admin error ', $config);
	$var->setGroup("Look & Feel");
	$var->setDefaultValue('$d44');
	$this->registerVariable($var);

	$var = new ColorCode('Colors_error', 'Color code for generic error', $config);
	$var->setGroup("Look & Feel");
	$var->setDefaultValue('$f00');
	$this->registerVariable($var);

	$var = new ColorCode('Colors_admin_action', 'Color code for actions made by admins', $config);
	$var->setGroup("Look & Feel");
	$var->setDefaultValue('$6af');
	$this->registerVariable($var);

	$var = new ColorCode('Colors_variable', 'Color code for all variables used in chatmessages', $config);
	$var->setGroup("Look & Feel");
	$var->setDefaultValue('$eee');
	$this->registerVariable($var);

	$var = new ColorCode('Colors_record', 'Color code for all localrecord messages', $config);
	$var->setGroup("Look & Feel");
	$var->setDefaultValue('$0bb');
	$this->registerVariable($var);

	$var = new ColorCode('Colors_record_top', 'Color code for top 5 localrecord messages', $config);
	$var->setGroup("Look & Feel");
	$var->setDefaultValue('$1F0');
	$this->registerVariable($var);

	$var = new ColorCode('Colors_dedirecord', 'Color code for dedimania record messages', $config);
	$var->setGroup("Look & Feel");
	$var->setDefaultValue('$0af');
	$this->registerVariable($var);

	$var = new ColorCode('Colors_rank', 'Color code for rank in records messages', $config);
	$var->setGroup("Look & Feel");
	$var->setDefaultValue('$ff0');
	$this->registerVariable($var);

	$var = new ColorCode('Colors_time', 'Color code for time in records messages', $config);
	$var->setGroup("Look & Feel");
	$var->setDefaultValue('$fff');
	$this->registerVariable($var);

	$var = new ColorCode('Colors_rating', 'Color code for map rating messages', $config);
	$var->setGroup("Look & Feel");
	$var->setDefaultValue('$fb3');
	$this->registerVariable($var);

	$var = new ColorCode('Colors_queue', 'Color code for map queue messages (jukebox)', $config);
	$var->setGroup("Look & Feel");
	$var->setDefaultValue('$8af');
	$this->registerVariable($var);

	$var = new ColorCode('Colors_personalmessage', 'Color code for personal messages', $config);
	$var->setGroup("Look & Feel");
	$var->setDefaultValue('$0ff');
	$this->registerVariable($var);

	$var = new ColorCode('Colors_admingroup_chat', 'Color code for admin chat channel', $config);
	$var->setGroup("Look & Feel");
	$var->setDefaultValue('$f60');
	$this->registerVariable($var);

	$var = new ColorCode('Colors_donate', 'Color code for donation messages', $config);
	$var->setGroup("Look & Feel");
	$var->setDefaultValue('$0af');
	$this->registerVariable($var);

	$var = new ColorCode('Colors_player', 'Color code for player messages', $config);
	$var->setGroup("Look & Feel");
	$var->setDefaultValue('$z$s$0af');
	$this->registerVariable($var);

	$var = new ColorCode('Colors_music', 'Color code for musicbox messages', $config);
	$var->setGroup("Look & Feel");
	$var->setDefaultValue('$f0a');
	$this->registerVariable($var);

	$var = new ColorCode('Colors_emote', 'Color code for emotes messages', $config);
	$var->setGroup("Look & Feel");
	$var->setDefaultValue('$9f0');
	$this->registerVariable($var);

	$var = new ColorCode('Colors_quiz', 'Color code for Quiz messsages', $config);
	$var->setGroup("Look & Feel");
	$var->setDefaultValue('$z$s$3e3');
	$this->registerVariable($var);

	$var = new ColorCode('Colors_question', 'Color code for Quiz questions', $config);
	$var->setGroup("Look & Feel");
	$var->setDefaultValue('$z$s$o$fa0');
	$this->registerVariable($var);

	$var = new ColorCode('Colors_vote', 'Color code for voting', $config);
	$var->setGroup("Look & Feel");
	$var->setDefaultValue('$0f0');
	$this->registerVariable($var);

	$var = new ColorCode('Colors_vote_success', 'Color code for vote passing', $config);
	$var->setGroup("Look & Feel");
	$var->setDefaultValue('$0f0');
	$this->registerVariable($var);

	$var = new ColorCode('Colors_vote_failure', 'Color code for vote failure', $config);
	$var->setGroup("Look & Feel");
	$var->setDefaultValue('$f00');
	$this->registerVariable($var);

	$var = new String('time_dynamic_max', 'Max time for dynamic TA limit, use format mm:ss', $config);
	$var->setGroup("Misc");
	$var->setDefaultValue('7:00');
	$this->registerVariable($var);

	$var = new String('time_dynamic_min', 'Min time for dynamic TA limit, use format mm:ss', $config);
	$var->setGroup("Misc");
	$var->setDefaultValue('4:00');
	$this->registerVariable($var);

	$var = new Boolean('enableRanksCalc', 'Enable player position calculation', $config);
	$var->setGroup("Misc");
	$var->setDefaultValue(true);
	$this->registerVariable($var);

	$var = new String('defaultMatchSettingsFile', 'This servers autosave matchsettings file', $config, false);
	$var->setGroup("Config Files");
	$var->setDefaultValue('eXpansion_autosave.txt');
	$this->registerVariable($var);

	$var = new String('dedicatedConfigFile', 'This servers autosave dedicated config file', $config, false);
	$var->setGroup("Config Files");
	$var->setDefaultValue('dedicated_cfg.txt');
	$this->registerVariable($var);

	$var = new String('contact', 'Server administrators contact info (displayed at serverinfo window)', $config, false);	
	$var->setDefaultValue('YOUR@EMAIL.COM');
	$this->registerVariable($var);
	
    }

}

?>
