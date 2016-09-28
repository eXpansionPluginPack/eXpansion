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
 * Description of Dico
 *
 * @author Petri
 */
class Dico
{

    protected $xml = "";
    protected $messages = array();

    public function __construct($dicoText = array())
    {
        $this->messages = $dicoText;
    }

    public function setMessages($msg)
    {
        $this->messages = $msg;
    }

    public function getXml()
    {
        $xml = "";
        /*
         * Message Array("Lang" = "en", "Text" = Text);
         */
        $messages = array();
        foreach ($this->messages as $id => $msg) {
            foreach ($msg as $message) {
                $messages[$message['Lang']][$id][] = $message['Text'];
            }
        }

        $xml = '<dico>' . "\n";
        foreach ($messages as $key => $value) {
            $xml .= '<language id="' . $key . '">' . "\n";
            foreach ($value as $id => $msg) {
                foreach ($msg as $text) {
                    $xml .= '<' . $id . '>' . $text . '</' . $id . '>' . "\n";
                }
            }
            $xml .= '</language>' . "\n";
        }
        $xml .= '</dico>' . "\n";

        return $xml;
    }
}
