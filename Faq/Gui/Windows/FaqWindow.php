<?php

namespace ManiaLivePlugins\eXpansion\Faq\Gui\Windows;

use ManiaLivePlugins\eXpansion\Faq\Gui\Controls\Line;
use ManiaLivePlugins\eXpansion\Gui\Elements\ScrollableArea;

/**
 * Description of FaqWindow
 *
 * @author Reaby
 */
class FaqWindow extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

    public static $mainPlugin;
    protected $userLanguage = "en";
    protected $elements = array();
    protected $frame;
    protected $scroll;

    protected function onConstruct()
    {
        parent::onConstruct();
        $this->setTitle("Help");

        $this->scroll = new ScrollableArea(167, 87);
        $this->scroll->setPosition(-1, -4);
        $this->addComponent($this->scroll);
        $this->frame = new \ManiaLive\Gui\Controls\Frame(0, 0);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Column(190,90));
    }


    public function setLanguage($language)
    {
        $this->userLanguage = "en";
        if (in_array($language, \ManiaLivePlugins\eXpansion\Faq\Faq::$availableLanguages)) {
            $this->userLanguage = $language;
        }
    }

    public function setTopic($topic)
    {
        $this->elements = array();
        $sizeY = 0;

        //try {
        if (strpos($topic, '../') !== false || strpos($topic, "..\\") !== false || strpos($topic, '/..') !== false || strpos($topic, '\..') !== false) {
            $topic = "toc";
        }
        $file = file_get_contents(dirname(dirname(__DIR__)) . "/Topics/" . $this->userLanguage . "/" . $topic . ".txt");
        $this->parse($file);
        //} catch (\Exception $e) {
        //$file = file_get_contents(dirname(dirname(__DIR__)) . "/Topics/" . $this->userLanguage . "/" . "toc.txt");
        //    throw $e;
        //$this->parse($file);
        //}

        foreach ($this->elements as $elem) {
            $sizeY += $elem->getSizeY();
            $this->frame->addComponent($elem);
        }

        $this->frame->setSize(280, $sizeY);
        $this->scroll->setContent($this->frame);
    }

    public function parse($file)
    {
        $this->frame->clearComponents();
        foreach ($this->elements as $elem) {
            $elem->destroy();
        }

        $data = explode("\n", $file);
        $topic = true;
        $x = 1;
        $isCodeBlock = false;
        $this->elements[0] = new Line("");

        foreach ($data as $line) {

            if ($topic == true) {
                $this->setTitle("Help ", trim($line));
                $topic = false;
                continue;
            }

            $matches = array();

            // match #, which marks a text to be rendered as a header
            if (preg_match('/(?P<level>#{1,6})(?P<rest>.*)/', trim($line), $matches)) {
                $this->elements[$x] = new \ManiaLivePlugins\eXpansion\Faq\Gui\Controls\Header(trim($matches['rest']), strlen($matches['level']));
            }

            if (trim($line) === "```") {
                $isCodeBlock = !$isCodeBlock;
                continue;
            }

            if ($isCodeBlock) {
                $this->elements[$x] = new \ManiaLivePlugins\eXpansion\Faq\Gui\Controls\CodeLine($line);
            }

            // in case nothing is set, it's normal line
            if (!isset($this->elements[$x])) {
                if (strlen($line) > 100) {
                    $lines = str_split(trim($line), 100);
                    $blockOld = 0;
                    foreach ($lines as $line2) {
                        $data = new \ManiaLivePlugins\eXpansion\Faq\Gui\Controls\Line($line2);
                        if ($data->getBlock() != $blockOld) {
                            $data->setBlock($blockOld);
                        }
                        $this->elements[$x] = $data;
                        $blockOld = $data->getBlock();
                        $x++;
                    }
                }
                else {
                    $this->elements[$x] = new \ManiaLivePlugins\eXpansion\Faq\Gui\Controls\Line($line);
                }
            }
            $x++;
        }

    }
}
