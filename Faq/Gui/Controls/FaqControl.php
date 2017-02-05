<?php

namespace ManiaLivePlugins\eXpansion\Faq\Gui\Controls;

/**
 * @abstract
 */
abstract class FaqControl extends \ManiaLivePlugins\eXpansion\Gui\Control
{
    protected $label;
    protected $action = null;
    protected $block = 0;

    public function __construct($text)
    {
        $this->sizeX = 240;
        $this->sizeY = 4;
        $this->setSize(240, 4);
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
        $this->setSize(240 - ($index * 6), 4);
    }

    public function setTopicLink($file)
    {
        $this->label->setTextColor("3af");
        $this->label->setStyle("TextCardMedium");
        $this->action = $this->createAction(array(\ManiaLivePlugins\eXpansion\Faq\Gui\Windows\FaqWindow::$mainPlugin, "showFaq"), $file, null);
        $this->label->setAction($this->action);
    }

    public function setText($text)
    {

        $matches = array();
        preg_match('/\!(.*)\|(.*)/', $text, $matches);

        if (substr_count($text, "\t")) {
            $indent = substr_count($text, "\t");
            $this->setBlock($indent);
        }

        if (sizeof($matches) == 3) {

            $this->setTopicLink($matches[1]);
            $text = $matches[2];
        }
        $text = str_replace("**", '$o', $text);
        $text = str_replace("__", '$o', $text);
        $text = preg_replace("/\*(.*?)\*/", '\$i$1\$i', $text);
        $text = preg_replace("/_(.*?)_/", '\$i$1\$i', $text);

        $matches = array();
        $text = preg_replace("/`(.*?)`/", '\$i\$ff0$1\$z', $text);

        $matches = array();
        if (preg_match_all("/```(?P<inline>.*?)```(?P<rest>.*)/", $text, $matches)) {
            $text = '$ff0$i' . $matches['inline'] . '$i$fff ' . $matches['rest'];
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
