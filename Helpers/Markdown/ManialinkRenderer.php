<?php

namespace ManiaLivePlugins\eXpansion\Helpers\Markdown;

use Ciconia\Common\Tag;
use Ciconia\Common\Text;
use Ciconia\Renderer\RendererInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ManialinkRenderer implements RendererInterface
{
    public $posY = 0;

    /**
     * @api
     *
     * @param string|Text $content
     * @param array $options
     *
     * @return string
     */
    public function renderParagraph($content, array $options = array())
    {
        return;
    }


    public function renderLine($content)
    {

        $array = explode("\n", $content);
        $out = "";
        foreach ($array as $line) {
            $line = str_replace('"', "'", $line);
            $line = str_replace(">", "-", $line);
            if (substr(trim($line), 0, 2) == "##") {
                $this->posY -= 3;
                $line = substr(trim($line), 2);
                $add = 'style="TextRaceMessageBig" textsize="2"';
                $y = 5;
            } else if (substr(trim($line), 0, 1) == "#") {
                $this->posY -= 3;
                $line = substr(trim($line), 1);
                $add = 'style="TextRaceMessageBig" textsize="4"';
                $y = 7;
            } else {
                $add = 'textsize="2"';
                $y = 4;
            }

            $out .= '<label autonewline="0" ' . $add . ' posn="0 ' . $this->posY . '" text="' . $line . '" />';
            $this->posY -= $y;
        }

        return $out;
    }


    /**
     * @api
     *
     * @param string|Text $content
     * @param array $options
     *
     * @return string
     */
    public function renderHeader($content, array $options = array())
    {
        $options = $this->createResolver()
            ->setRequired(array('level'))
            ->setAllowedValues(array('level' => array(1, 2, 3, 4, 5, 6)))
            ->resolve($options);
        // level is now $options['level'];

        $out = '<label style="TextRaceMessageBig" textsize="3" posn="0 ' . $this->posY . '" autonewline="0" text="' . $content . '"  />';
        $this->posY -= 4;
        return $out;
    }

    /**
     * @api
     *
     * @param string|Text $content
     * @param array $options
     *
     * @return string
     */
    public function renderCodeBlock($content, array $options = array())
    {
        $out = '<label autonewline="1" textsize="1" posn="0 ' . $this->posY . '"text="' . $content . '"  />';
        $this->posY -= 3;
        return $out;
    }

    /**
     * @api
     *
     * @param string|Text $content
     * @param array $options
     *
     * @return string
     */
    public function renderCodeSpan($content, array $options = array())
    {
        $out = '<label autonewline="1" textsize="3" posn="0 ' . $this->posY . '"text="' . $content . '"  />';
        $this->posY -= 5;
        return $out;
    }

    /**
     * @api
     *
     * @param string|Text $content
     * @param array $options
     *
     * @return string
     */
    public function renderLink($content, array $options = array())
    {

    }

    /**
     * @api
     *
     * @param string|Text $content
     * @param array $options
     *
     * @return string
     */
    public function renderBlockQuote($content, array $options = array())
    {
        // TODO: Implement renderBlockQuote() method.
    }

    /**
     * @api
     *
     * @param string|Text $content
     * @param array $options
     *
     * @return string
     */
    public function renderList($content, array $options = array())
    {
        // TODO: Implement renderList() method.
    }

    /**
     * @api
     *
     * @param string|Text $content
     * @param array $options
     *
     * @return string
     */
    public function renderListItem($content, array $options = array())
    {
        // TODO: Implement renderListItem() method.
    }

    /**
     * @api
     *
     * @param array $options
     *
     * @return string
     */
    public function renderHorizontalRule(array $options = array())
    {
        // TODO: Implement renderHorizontalRule() method.
    }

    /**
     * @api
     *
     * @param string $src
     * @param array $options
     *
     * @return string
     */
    public function renderImage($src, array $options = array())
    {
        // TODO: Implement renderImage() method.
    }

    /**
     * @api
     *
     * @param string|Text $text
     * @param array $options
     *
     * @return string
     */
    public function renderBoldText($text, array $options = array())
    {
        // TODO: Implement renderBoldText() method.
    }

    /**
     * @api
     *
     * @param string|Text $text
     * @param array $options
     *
     * @return string
     */
    public function renderItalicText($text, array $options = array())
    {
        // TODO: Implement renderItalicText() method.
    }

    /**
     * @api
     *
     * @param array $options
     *
     * @return string
     */
    public function renderLineBreak(array $options = array())
    {
        // TODO: Implement renderLineBreak() method.
    }

    /**
     * @param string $tagName
     * @param string $content
     * @param string $tagType
     * @param array $options
     *
     * @return mixed
     */
    public function renderTag($tagName, $content, $tagType = Tag::TYPE_BLOCK, array $options = array())
    {
        $options = $this->createResolver()->resolve($options);

        $tag = new Tag($tagName);
        $tag->setType($tagType);
        $tag->setText($content);
        $tag->setAttributes($options['attr']);

        //  return $tag->render();
    }


    public function createResolver()
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults(array('attr' => array()));
        $resolver->setAllowedTypes(array('attr' => 'array'));

        return $resolver;
    }
}
