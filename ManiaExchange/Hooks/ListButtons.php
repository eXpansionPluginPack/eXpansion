<?php
/**
 * @author       Oliver de Cramer (oliverde8 at gmail.com)
 * @copyright    GNU GENERAL PUBLIC LICENSE
 *                     Version 3, 29 June 2007
 *
 * PHP version 5.3 and above
 *
 * LICENSE: This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see {http://www.gnu.org/licenses/}.
 */

namespace ManiaLivePlugins\eXpansion\ManiaExchange\Hooks;

class ListButtons extends \ManiaLive\Event\Event
{
    const ON_BUTTON_LIST_CREATE = 1;

    protected $buttons;
    protected $login;

    /**
     * @param $onWhat
     * @param $buttons
     * @param $login
     */
    public function __construct($onWhat, $buttons, $login)
    {
        parent::__construct($onWhat);

        $this->buttons = $buttons;
        $this->login = $login;
    }

    public function fireDo($listener)
    {
        switch ($this->onWhat) {
            case self::ON_BUTTON_LIST_CREATE:
                $listener->hook_ManiaExchangeListButtons($this->buttons, $this->login);
                break;
        }
    }
}
