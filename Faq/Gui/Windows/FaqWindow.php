<?php

namespace ManiaLivePlugins\eXpansion\Faq\Gui\Windows;

use ManiaLib\Gui\Layouts\Column;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\Faq\Faq;
use ManiaLivePlugins\eXpansion\Faq\Gui\Controls\CodeLine;
use ManiaLivePlugins\eXpansion\Faq\Gui\Controls\Header;
use ManiaLivePlugins\eXpansion\Faq\Gui\Controls\Line;
use ManiaLivePlugins\eXpansion\Gui\Elements\ScrollableArea;
use ManiaLivePlugins\eXpansion\Gui\Windows\Window;

/**
 * Description of FaqWindow
 *
 * @author Reaby
 */
class FaqWindow extends Window
{

    public static $mainPlugin;
    protected $userLanguage = "en";
    protected $elements = array();

    /** @var  Frame */
    protected $frame;
    /** @var  ScrollableArea */
    protected $scroll;

    protected function onConstruct()
    {
        parent::onConstruct();
        $this->setTitle("Help");

        $this->scroll = new ScrollableArea(167, 87);
        $this->scroll->setPosition(-1, -4);
        $this->addComponent($this->scroll);
        $this->frame = new Frame(0, 0);
        $this->frame->setLayout(new Column(190, 90));
    }


    public function setLanguage($language)
    {
        $this->userLanguage = "en";
        if (in_array($language, Faq::$availableLanguages)) {
            $this->userLanguage = $language;
        }
    }

    public function setTopic($topic)
    {
        $this->elements = array();
        $sizeY = 0;

        //try {
        if (strpos($topic, '../') !== false || strpos($topic, "..\\") !== false ||
            strpos($topic, '/..') !== false || strpos($topic, '\\..') !== false
        ) {
            $topic = "toc.md";
        }
        $file = file_get_contents(dirname(dirname(__DIR__)) . "/Topics/" . $this->userLanguage . "/" . $topic);
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
        $x = 2;
        $isCodeBlock = false;


        // add one empty line
        $this->elements[0] = new Line("");
        $this->elements[1] = new Line("[Back to index](toc.md)<br>");

        $emptyLines = 0;
        $lastIsTitle = false;
        foreach ($data as $line) {
            $currentIsTitle = false;
            if ($topic == true) {
                $this->setTitle("Help ", trim($line));
                $topic = false;
                continue;
            }

            $matches = array();

            // match #, which marks a text to be rendered as a header
            if (preg_match('/(?P<level>#{1,6})(?P<rest>.*)/', trim($line), $matches)) {
                if ($emptyLines == 0) {
                    $this->elements[$x] = new Line($line);
                    $x++;
                }
                $lastIsTitle = true;
                $currentIsTitle = true;
                $this->elements[$x] = new Header(trim($matches['rest']), strlen($matches['level']));
            }


            if (trim($line) == '') {
                $emptyLines++;
                if ($emptyLines > 1 || $lastIsTitle) {
                    // Simply ignore this line. It's for md files.
                    continue;
                }
            } else {
                $emptyLines = 0;
            }

            $lastIsTitle = $currentIsTitle;

            if (trim($line) === "```") {
                $isCodeBlock = !$isCodeBlock;
                continue;
            }

            if (trim($line) === ">") {
                $this->elements[$x] = new InfoLine();
            }

            if ($isCodeBlock) {
                $this->elements[$x] = new CodeLine($line);
            }

            // in case nothing is set, it's normal line
            if (!isset($this->elements[$x])) {
                if (strlen($line) > 100) {
                    $lines = str_split(trim($line), 100);
                    $blockOld = 0;
                    foreach ($lines as $line2) {
                        $data = new Line($line2);
                        if ($data->getBlock() != $blockOld) {
                            $data->setBlock($blockOld);
                        }
                        $this->elements[$x] = $data;
                        $blockOld = $data->getBlock();
                        $x++;
                    }
                } else {
                    $this->elements[$x] = new Line($line);
                }
            }
            $x++;
        }
    }
}
