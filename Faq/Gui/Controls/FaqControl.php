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

        if (substr_count($text, "\t")) {
            $indent = substr_count($text, "\t");
            $this->setBlock($indent);
        }

        $matches = array();

        // Match links
        // @TODO try and make it match more then one links at the time
        // Should be something like this : ((\[([^\]]*)\]\(([^\)]*)\)))*
        preg_match("(?P<textb>.*)(\[(?P<text>.*)\]\((?P<url>.*)\))(?P<texta>.*)", $text, $matches);
        if (!empty($matches['url']) && !empty($matches['text'])) {

            if (substr($matches['url'], 0, 1) == '#') {
                // It's an internal link
                $this->setTopicLink($matches['url']);
                $text = $matches['textb'] . $matches['text'] . $matches['texta'];
            } else {
                $text = $matches['textb'] . '$l[' . $matches['url'] . ']' . $matches['text'] . '$l' . $matches['textb'];
            }
        }

        // Match bold.
        $text = str_replace("**", '$o', $text);
        $text = str_replace("__", '$o', $text);

        // Match italic
        $text = preg_replace("/\*(.*?)\*/", '\$i$1\$i', $text);
        $text = preg_replace("/_(.*?)_/", '\$i$1\$i', $text);

        // Match code lines
        // @TODO check why there is 2 preg_replace here. First seems superflous.
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
