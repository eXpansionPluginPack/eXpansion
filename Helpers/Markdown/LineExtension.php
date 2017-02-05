<?php

namespace ManiaLivePlugins\eXpansion\Helpers\Markdown;

use Ciconia\Common\Text;
use Ciconia\Extension\ExtensionInterface;
use Ciconia\Markdown;
use Ciconia\Renderer\RendererAwareInterface;
use Ciconia\Renderer\RendererAwareTrait;

/**
 * Original source code from Markdown.pl
 *
 * > Copyright (c) 2004 John Gruber
 * > <http://daringfireball.net/projects/markdown/>
 *
 * @author Petri
 *
 */
class LineExtension implements ExtensionInterface, RendererAwareInterface
{

    use RendererAwareTrait;

    /**
     * @var Markdown
     */
    private $markdown;

    /**
     * {@inheritdoc}
     */
    public function register(Markdown $markdown)
    {
        $this->markdown = $markdown;

        $markdown->on('block', array($this, 'buildLines'), 4);

    }

    /**
     * @param Text $text
     */
    public function buildLines(Text $text)
    {
        $parts = $text
            ->replace('/\A\n+/', '')
            ->replace('/\n+\z/', '')
            //->replace('/\n+$/', '')
            ->split('/\n{2,}/', PREG_SPLIT_NO_EMPTY);

        $parts->apply(function (Text $part) {
            if (!$this->markdown->getHashRegistry()->exists($part)) {
                $this->markdown->emit('inline', array($part));
                $part->replace('/^([ \t]*)/', '');
                if (!$part->contains("label")) {
                    $part->setString($this->getRenderer()->renderLine((string)$part));
                }
            }
            return $part;
        });

        $parts->apply(function (Text $part) {
            if ($this->markdown->getHashRegistry()->exists($part)) {
                $part->setString(trim($this->markdown->getHashRegistry()->get($part)));
            }
            return $part;
        });

        $text->setString($parts->join("\n"));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'line';
    }

}
