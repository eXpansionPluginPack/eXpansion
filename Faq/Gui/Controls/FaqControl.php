<?php

namespace ManiaLivePlugins\eXpansion\Faq\Gui\Controls;

use ManiaLib\Gui\Elements\Quad;

/**
 * @abstract
 */
abstract class FaqControl extends \ManiaLivePlugins\eXpansion\Gui\Control
{
    protected $label;
    protected $quad;
    protected $action = null;
    protected $block = 0;

    public function __construct($text)
    {
        $this->sizeX = 240;
        $this->sizeY = 5;
        $this->setSize(240, 5);
        $this->setAlign("left");
        $this->label = new \ManiaLib\Gui\Elements\Label(240, 5);
        $this->label->setAlign("left", "center");
        $this->label->setStyle("TextRaceChat");
        $this->label->setTextColor("fff");
        $this->label->setTextSize(2);
        $this->setText($text);
        $this->addComponent($this->label);
    }

    public function setBlock($index = 0)
    {
        $this->block = $index;
        $this->setPosX($index * 6);
        $this->setSize(240 - ($index * 6), 5);
    }

    public function setList()
    {
        $this->quad = new Quad(2, 2);
        $this->quad->setAlign("left", "center");
        $this->quad->setImage("file://Media/Manialinks/Common/Disc.dds", true);
        $this->quad->setColorize('fff');
        $this->quad->setPositionX(0);
        $this->label->setPositionX(2);
        $this->addComponent($this->quad);
    }


    public function setTopicLink($file)
    {
        $this->label->setTextColor("3af");
        $this->label->setStyle("TextCardMedium");
        $file = str_replace("#", "", $file);
        $this->action = $this->createAction(array(\ManiaLivePlugins\eXpansion\Faq\Gui\Windows\FaqWindow::$mainPlugin, "showFaq"), $file, null);
        $this->label->setAction($this->action);
    }

    public function setText($text)
    {
        $matches = array();
        if (preg_match("/^(\t|    )*(\*|\d+\.) (.*)/", $text, $matches)) {
            // Check for lists
            $indent = substr_count($matches[1], '    ');
            $indent += substr_count($matches[1], "\t");
            if ($matches[2] == "*") {
                $this->setList();
                $text = preg_replace("/^(\t|    )*\*/", "", $text);
            }
            $this->setBlock($indent + 0.2);
        } else if ($indent = substr_count($text, "\t")) {
            $this->setBlock($indent);
        }

        $matches = array();
        preg_match("/(?P<textb>.*)(\[(?P<text>.*?)\]\((?P<url>.*?)\))(?P<texta>.*)/", $text, $matches);
        if (!empty($matches['url']) && !empty($matches['text'])) {
            if (substr($matches['url'], 0, 4) == 'http') {
                $text = $matches['textb'] . '$3af$l[' . $matches['url'] . ']' . $matches['text'] . '$l$z' . $matches['texta'];
            } else if (substr($matches['url'], 0, 4) == '##') {
                // It's a manialink.
                $text = $matches['textb'] . '$3af$l[' . str_replace('##', '', $matches['url']) . ']' . $matches['text'] . '$l$z' . $matches['texta'];
            } else {
                // It's an internal link
                $this->setTopicLink($matches['url']);
                $text = $matches['textb'] . $matches['text'] . $matches['texta'];
            }
        }

        $text = str_replace("**", '$o', $text);
        $text = str_replace("__", '$o', $text);
        $text = preg_replace("/\*(.*?)\*/", '\$i$1\$i', $text);
        $text = preg_replace("/_(.*?)_/", '\$i$1\$i', $text);
        $text = preg_replace("/`(.*?)`/", '\$i\$ff0$1\$z', $text);

        $matches = array();
        if (preg_match_all("/```(?P<inline>.*?)```(?P<rest>.*)/", $text, $matches)) {
            $text = '$ff0$i' . $matches['inline'] . '$i$fff ' . $matches['rest'];
        }

        if (empty($text)) {
            // Empty lines should be smaller
            $this->setSizeY(3);
        }

        $this->label->setText($text);
    }

    public function onIsRemoved(\ManiaLive\Gui\Container $target)
    {
        parent::onIsRemoved($target);
        $this->destroy();
    }

    public function getBlock()
    {
        return $this->block;
    }

    public function destroy()
    {
        parent::destroy();
    }

}
