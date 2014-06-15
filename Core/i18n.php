<?php

namespace ManiaLivePlugins\eXpansion\Core;

/**
 * a simple internationalization class for string texts
 * 
 */
class i18n extends \ManiaLib\Utils\Singleton {

    /**
     * default language
     *
     * @var string $language 
     */
    private $defaultLanguage = null;

    /**
     * Translated messages by language
     *
     * @var array(String => Message)
     */
    private $messages = array();

    /**
     * Supported locales
     *
     * @var array
     */
    private $supportedLocales = array();

    /**
     * Directories to look into from translations
     *
     * @var array
     */
    private $directorties = array();

    private $handledDirectories = array();

    /**
     * Was the class started (all directories checked and everything)
     *
     * @var bool
     */
    private $started = false;

    /**
     * Registers a directory to look into for translations. If already started then it will load it immediately
     *
     * @param $dir Directory
     */
    public function registerDirectory($dir) {
        if ($this->started) {
            $this->readFiles($dir);
            $this->supportedLocales = array_unique($this->supportedLocales);
        } else {
            $this->directorties[] = $dir;
        }
    }

    /**
     * Start loading the directories that are pending
     */
    public function start() {
        if (!empty($this->directorties)) {
            foreach ($this->directorties as $dir) {
                $this->readFiles($dir);
            }
            $this->supportedLocales = array_unique($this->supportedLocales);
        }
        $this->started = true;
    }

    /**
     * Read the files in order to register the translations found
     *
     * @param $dir the directory to load translations from
     */
    protected function readFiles($dir) {
        if(isset($this->handledDirectories[$dir]))
            return;

        $this->handledDirectories[$dir] = true;

        if (is_dir($dir . "/messages")) {

            $langFiles = glob($dir . "/messages/*.txt", GLOB_MARK);	    
            foreach ($langFiles as $file) {                
                $language = explode("/", $file);
                $language = end($language);
                $language = str_replace(".txt", "", $language);
                if (empty($language))
                    continue;                                                
                $this->supportedLocales[] = $language;
                $data = file($file, FILE_IGNORE_NEW_LINES);
                for ($x = 0; $x < count($data) - 1; $x += 3) {
                    $orig = trim($data[$x]);
                    $trans = trim($data[$x + 1]);
                    if (!DEBUG)
                        $trans = str_replace("#translate# ", "", $trans);

                    if (!isset($this->messages[$orig])) {
                        $this->messages[$orig] = new i18n\Message($orig);
                    }
                    $this->messages[$orig]->addLanguageMessage($language, $trans);
                }
            }
        }
    }

    /**
     * Sets default language, used for players which has languages unknown to the system
     *
     * @param $language
     */
    public function setDefaultLanguage($language) {
        $this->defaultLanguage = $language;
    }

    /**
     * Get language object from key
     *
     * @param $string Translation key
     *
     * @return i18n\Message FOund message, or new message
     */
    public function getObject($string) {
        if (isset($this->messages[$string])) {
            return $this->messages[$string];
        } else {
            $nmessage = new i18n\Message($string);
            return $nmessage;
        }
    }

    /**
     * Get the translation of a key in a certain language. If no language is defined default language
     *
     * @param      $string      Translation key
     * @param null $fromLanguage Language to get the translation for
     *
     * @return string the translation, if none find the translation key
     */
    public function getString($string, $fromLanguage = null) {
        if ($fromLanguage == null)
            return $this->translate($string, $this->defaultLanguage);
        return $this->translate($string, $fromLanguage);
    }

    /**
     * Returns the list of languages that are supported.
     *
     * @return string[]
     */
    public function getSupportedLocales() {
        return $this->supportedLocales;
    }

    /**
     * @param      $string
     * @param null $language
     *
     * @return mixed
     * @todo check if this is actually used :S I don't understand what it does.
     */
    private function translate($string, $language = null) {

        if (isset($this->messages[$string])) {
            return $this->messages[$string]->getMessage($language);
        } else
            return $string;
    }

}

?>