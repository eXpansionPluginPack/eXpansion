<?php

namespace ManiaLivePlugins\eXpansion\Faq\Gui\Windows;

use ManiaLive\Utilities\Console;

/**
 * Description of FaqWindow
 *
 * @author Reaby
 */
class FaqWindow extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

    public static $mainPlugin;
    private $userLanguage = "en";
    protected $elements = array();
    protected $frame;

    protected function onConstruct()
    {
        parent::onConstruct();
        $this->setTitle("Frequently asked questions");
        $this->frame = new \ManiaLive\Gui\Controls\Frame(6, -4);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Flow(190, 90));
        $this->addComponent($this->frame);
    }


    public function setLanguage($language)
    {
        $this->userLanguage = "en";
        if (in_array($language, \ManiaLivePlugins\eXpansion\Faq\Faq::$availableLanguages))
            $this->userLanguage = $language;
    }

    public function setTopic($topic)
    {
        $this->frame->clearComponents();
        foreach ($this->elements as $elem) {
            $elem->destroy();
        }

        $this->elements = array();
        try {
            if (strpos($topic, '../') !== false ||
                strpos($topic, "..\\") !== false ||
                strpos($topic, '/..') !== false ||
                strpos($topic, '\..') !== false
            ) {
                $topic = "toc";
            }
            $file = file_get_contents(dirname(dirname(__DIR__)) . "/Topics/" . $this->userLanguage . "/" . $topic . ".txt");
            $this->parse($file);
        } catch (\Exception $e) {
            $file = file_get_contents(dirname(dirname(__DIR__)) . "/Topics/" . $this->userLanguage . "/" . "toc.txt");
            $this->parse($file);
        }
        foreach ($this->elements as $elem) {
            $this->frame->addComponent($elem);
        }
    }

    public function parse($file)
    {
        $data = explode("\n", $file);
        $topic = true;
        $x = 0;
        foreach ($data as $line) {
            $indent = 0;
            if ($topic == true) {
                //$this->setTitle(trim($line));
                $topic = false;
                continue;
            }
            // match #, which marks a text to be rendered as a header
            if (preg_match('/^\#/', trim($line))) {
                $this->elements[$x] = new \ManiaLivePlugins\eXpansion\Faq\Gui\Controls\Header($line);
            } else {
                $this->elements[$x] = new \ManiaLivePlugins\eXpansion\Faq\Gui\Controls\Line(trim($line));
            }

            $matches = array();
            preg_match('/\!(.*)\|(.*)/', trim($line), $matches);
            //preg_match('/\!.*\|(.*)\|(.*)/', trim($line), $matches);

            if (sizeof($matches) == 3) {
                $this->elements[$x]->setText(trim($matches[2]));
                $this->elements[$x]->setTopicLink($matches[1]);
            }
            if (substr_count($line, "\t")) {
                $indent = substr_count($line, "\t");
                $this->elements[$x]->setBlock($indent);
            }
            $x++;
        }
    }

}

?>
