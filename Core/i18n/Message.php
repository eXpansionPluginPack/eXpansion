<?php

namespace ManiaLivePlugins\eXpansion\Core\i18n;

/**
 * Description of Message
 *
 * @author oliverde8
 */
class Message
{

	public static $defaultLanguage = "en";

	/**
	 * The orginal message as used in ManiaLive
	 */
	private $originalMessage;

	/**
	 * List of all the messages in different languages
	 *
	 * @var String[String]
	 */
	private $lmessages = array();

	/**
	 * List of all arguments passed for parsing
	 *
	 * @var String[String]
	 */
	private $args = array();

	function __construct($orginalMessage)
	{
		$this->originalMessage = $orginalMessage;
	}

	public function addLanguageMessage($lang, $message)
	{
		$this->lmessages[$lang] = $message;
	}

	public function setArgs(array $args)
	{
		$this->args = $args;
	}

	/**
	 * getMultiLangArray()
	 * Returns a multilanguage message array to be used with Connection->ChatSendServerMessageToLanguage();
	 * if message contains arguments, please use $msg->setArgs before calling this function
	 *
	 * @return Strign[String] $out Array[] = array("Lang" => string, "Text" => string)
	 */
	public function getMultiLangArray()
	{
		$temp = $this->lmessages;
		if (!isset($temp["en"])) {
			$temp["en"] = $this->originalMessage;
		}
		$out = array();

		foreach ($temp as $lang => $msg) {
			$arrgs = $this->args;
			array_unshift($arrgs, $msg);
			$text = call_user_func_array('sprintf', $arrgs);
			$out[$lang] = array(
				"Lang" => lcfirst($lang),
				"Text" => \ManiaLivePlugins\eXpansion\Core\ColorParser::getInstance()->parseColors($text)
			);
		}

		// Put en message last for unknown languages to see english.
		$en = $out['en'];
		unset($out['en']);
		$out['en'] = $en;

		return array_values($out);
	}

	/**
	 * getParsedMessage ($lang)
	 *
	 * @param string $lang
	 *
	 * @return string
	 */
	public function getParsedMessage($lang = null)
	{
		$arrgs = $this->args;
		array_unshift($arrgs, $this->getMessage($lang));
		$text = call_user_func_array('sprintf', $arrgs);
		return \ManiaLivePlugins\eXpansion\Core\ColorParser::getInstance()->parseColors($text);
	}

	/**
	 *  getMessage
	 *
	 * @param string $lang language code
	 *
	 * @return string
	 */
	public function getMessage($lang = null)
	{
		if ($lang == null) {
			return isset($this->lmessages[self::$defaultLanguage]) ? $this->lmessages[self::$defaultLanguage] : $this->originalMessage;
		} else if (isset($this->lmessages[$lang]))
			return $this->lmessages[$lang];
		else {
			return isset($this->lmessages[self::$defaultLanguage]) ? $this->lmessages[self::$defaultLanguage] : $this->originalMessage;
		}
	}

	public function __toString()
	{
		return $this->originalMessage;
	}

}

?>
