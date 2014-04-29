<?php

namespace ManiaLivePlugins\eXpansion\Emotes;

use \ManiaLivePlugins\eXpansion\Emotes\Gui\Windows\EmotePanel;
use ManiaLive\Event\Dispatcher;

class Emotes extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $timeStamps = "";
    public static $action_GG;
    public static $action_Afk;
    public static $action_Lol;
    public static $action_Bg;

    function exp_onInit() {
	parent::exp_onInit();
	if ($this->isPluginLoaded('\ManiaLivePlugins\oliverde8\HudMenu\HudMenu')) {
	    Dispatcher::register(\ManiaLivePlugins\oliverde8\HudMenu\onOliverde8HudMenuReady::getClass(), $this);
	}
	/* self::$action_GG = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($this, 'sendEmote'), "GG");
	  self::$action_Afk = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($this, 'sendEmote'), "Afk");
	  self::$action_Lol = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($this, 'sendEmote'), "Lol");
	  self::$action_Bg = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($this, 'sendEmote'), "BF"); */
    }

    function exp_onLoad() {
	$this->enableDedicatedEvents();
	EmotePanel::$emotePlugin = $this;
	$this->config = Config::getInstance();

	$commands = array("bb", "bye", "hi", "hello", "thx", "ty", "lol", "brb", "afk", "gg", "nl", "bgm", "sry", "sorry", "glhf", "wb", "omg", "buzz", "eat", "drink", "rant");
	$help = "performs a chatemote.";
	foreach ($commands as $command) {
	    $cmd = $this->registerChatCommand("$command", "$command", -1, true);
	    $cmd->help = $help;
	}
	$oneliners = array("rq", "bootme", "joke", "fact", "proverb", "quote");

	foreach ($oneliners as $command) {
	    $cmd = $this->registerChatCommand($command, $command, 0, true);
	    $cmd->help = $help;
	}

	foreach ($this->storage->players as $player)
	    $this->onPlayerConnect($player->login, false);
	foreach ($this->storage->spectators as $player)
	    $this->onPlayerConnect($player->login, true);
    }

    /* public function onOliverde8HudMenuReady($menu) {
      $config = Config::getInstance();

      $button["style"] = "Icons64x64_1";
      $button["substyle"] = "ToolRoot";
      $parent2 = $menu->findButton(array('menu', 'Extras'));
      $button["image"] = $config->iconMenu;
      if (!$parent2) {
      $parent = $menu->addButton('menu', "Extras", $button);
      }

      unset($button["style"]);
      unset($button["substyle"]);

      $parent = $menu->findButton(array('menu', "Extras", 'Emotes'));
      $button["image"] = $config->iconMenu;
      if (!$parent) {
      $parent = $menu->addButton($parent2, "Emotes", $button);
      }

      $button["image"] = $config->iconGG;
      $button["chat"] = "gg";
      $menu->addButton($parent, "Good Game(gg)", $button);

      $button["image"] = $config->iconBG;
      $button["chat"] = "bg";
      $menu->addButton($parent, "Bad Game(bg)", $button);

      $button["image"] = $config->iconLol;
      $button["chat"] = "lol";
      $menu->addButton($parent, "Lol", $button);

      $button["image"] = $config->iconAfk;
      $button["chat"] = "afk";
      $menu->addButton($parent, "Away from Key(afk)", $button);

      unset($button["image"]);
      $button["chat"] = "bootme";
      $menu->addButton($parent, "Boot Me", $button);
      } */

    function onPlayerConnect($login, $isSpectator) {
	/* if (!Config::getInstance()->showPanel)
	  return;

	  $info = EmotePanel::Create($login);
	  $info->setSize(60, 8);
	  $info->setPosition(-160, -40);
	  $info->show(); */
    }

    public function onPlayerDisconnect($login, $reason = null) {
	// EmotePanel::Erase($login);
	if (isset($this->timeStamps[$login]))
	    unset($this->timeStamps[$login]);
    }

    function bootme($login) {
	$player = $this->storage->getPlayerObject($login);
	$nick = $player->nickName;
	$message = (string) $this->config->bootme[rand(0, count($this->config->bootme) - 1)];

	$this->exp_chatSendServerMessage($nick . ' $z$s #emote#' . $message);
	$this->connection->kick($login, "thanks for playing");
    }

    function rq($login) {
	$player = $this->storage->getPlayerObject($login);
	$nick = $player->nickName;
	$message = (string) $this->config->ragequit[rand(0, count($this->config->ragequit) - 1)];
	$this->exp_chatSendServerMessage($nick . ' $z$s #emote#' . $message);
	$this->connection->kick($login, "thanks for playing");
    }

    function hi($login, $args = "") {
	$this->helper($login, $args, $this->config->hi, $this->config->hi2);
    }

    function hello($login, $args = "") {
	$this->helper($login, $args, $this->config->hi, $this->config->hi2);
    }

    function thx($login, $args = "") {

	$this->helper($login, $args, $this->config->thx, $this->config->thx2);
    }

    function ty($login, $args = "") {

	$this->helper($login, $args, $this->config->thx, $this->config->thx2);
    }

    function bb($login, $args = "") {

	$this->helper($login, $args, $this->config->bb, $this->config->bb2);
    }

    function bye($login, $args = "") {

	$this->helper($login, $args, $this->config->bb, $this->config->bb2);
    }

    function lol($login, $args = "") {
	$this->helper($login, $args, $this->config->lol, $this->config->lol2);
    }

    function brb($login, $args = "") {
	$this->helper($login, $args, $this->config->brb, $this->config->brb2);
    }

    function afk($login, $args = "") {
	$this->helper($login, $args, $this->config->afk, $this->config->afk2);
	$this->connection->forceSpectator($login, 3);
    }

    function gg($login, $args = "") {
	$this->helper($login, $args, $this->config->gg, $this->config->gg2);
    }

    function nl($login, $args = "") {
	$this->helper($login, $args, $this->config->nl, $this->config->nl2);
    }

    function bgm($login, $args = "") {

	$this->helper($login, $args, $this->config->bgm, $this->config->bgm2);
    }

    function sry($login, $args = "") {
	$this->helper($login, $args, $this->config->sry, $this->config->sry2);
    }

    function sorry($login, $args = "") {
	$this->helper($login, $args, $this->config->sry, $this->config->sry2);
    }

    function glhf($login, $args = "") {
	$this->helper($login, $args, $this->config->glhf, $this->config->glhf2);
    }

    function wb($login, $args = "") {
	$this->helper($login, $args, $this->config->wb, $this->config->wb2);
    }

    function omg($login, $args = "") {
	$this->helper($login, $args, $this->config->omg, $this->config->omg2);
    }

    function buzz($login, $args = "") {

	$this->helper($login, $args, $this->config->buzz, $this->config->buzz2);
    }

    function eat($login, $args = "") {

	$this->helper($login, $args, $this->config->eat, $this->config->eat2);
    }

    function drink($login, $args = "") {

	$this->helper($login, $args, $this->config->drink, $this->config->drink2);
    }

    function rant($login, $args = "") {
	$this->helper($login, $args, $this->config->rant, $this->config->rant2);
    }

    function joke($login) {
	$this->oneLiner($login, "jokes");
    }

    function fact($login) {
	$this->oneLiner($login, "facts");
    }

    function proverb($login) {
	$this->oneLiner($login, "proverbs");
    }

    function quote($login) {
	$this->oneLiner($login, "quotes");
    }

    /**
     * helper()
     * Helper function, does the hard stuff for outputting text.
     *
     * @param mixed $login
     * @param mixed $param
     * @param mixed $text
     * @param mixed $source1
     * @param mixed $source2
     * @return void
     */
    function helper($login, $args, $source1, $source2) {
	$args = explode(" ", $args);

	$player = $this->storage->getPlayerObject($login);
	$message = (string) $source1[rand(0, count($source1) - 1)];
	$message2 = (string) $source2[rand(0, count($source2) - 1)];

	if (count($args) >= 0) {
	    if (($nick = $this->getPlayerNick($args[0])) == "") {
		$text = implode(" ", $args);
		$this->exp_chatSendServerMessage($player->nickName . '$z$s #emote#' . $message . " #emote#" . $text);
	    } else {
		array_shift($args);
		$text = implode(" ", $args);
		$this->exp_chatSendServerMessage($player->nickName . '$z$s #emote#' . $message2 . ", " . $nick . " #emote#" . $text);
	    }
	} else {
	    $this->exp_chatSendServerMessage($player->nickName . '$z$s #emote#' . $message);
	}
    }

    function getPlayerNick($login) {
	try {
	    $player = $this->storage->getPlayerObject($login);
	    if ($player instanceof \Maniaplanet\DedicatedServer\Structures\Player) {
		return $player->nickName;
	    }
	    return "";
	} catch (Exception $ex) {
	    return "";
	}
    }

    /**
     * oneLiner()
     * Function used for outputting one-liners.
     *
     * @param mixed $login
     * @param mixed $file
     * @return void
     */
    function oneLiner($login, $file) {
	$data = file_get_contents(__DIR__ . '/Texts/' . $file . '.txt');
	$lines = explode("\n", $data);
	$message = (string) $lines[rand(0, count($lines) - 1)];
	$player = $this->storage->getPlayerObject($login);
	$this->exp_chatSendServerMessage($player->nickName . '$z$s #emote#' . trim($message) . '$z$s');
    }

}

?>