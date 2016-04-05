<?php

/*
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

/**
 * Description of DicoLabel
 *
 * You can use this class to create labels with localized content.
 *
 * example:
 * $label = new DicoLabel();
 * $label->setText(exp_getMessage("Hello World"));
 *
 * to update localized messages, run first genLocales.php and then updateLocales.php
 * new localized contentfiles are then at plugin\messages folder.
 *
 * note: eXpansion widgets and windows autocreate dico elements from this class :)
 *
 * @author Petri
 */
class DicoLabel extends \ManiaLib\Gui\Elements\Label
{

    protected $messages = array();

    public function __construct($sizeX = 20, $sizeY = 7)
    {
        parent::__construct($sizeX, $sizeY);
    }

    /**
     * example:
     * $label = new DicoLabel();
     * $label->setText(exp_getMessage("Hello World"));
     *
     * @param \ManiaLivePlugins\eXpansion\Core\i18n\Message $text
     */
    public function setText($text = "", $args = array())
    {
        if ($text instanceof \ManiaLivePlugins\eXpansion\Core\i18n\Message) {
            $text->setArgs($args);
            $this->messages = $text->getMultiLangArray();
            $this->setTextid('x' . md5(spl_object_hash($this)));
        } else if (is_string($text)) {
            parent::setText($text);
        }
    }

    public function getMessages()
    {
        return $this->messages;
    }

}
