<?php
/**
 * @author      Oliver de Cramer (oliverde8 at gmail.com)
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

namespace ManiaLivePlugins\eXpansion\Core\types;

class AsynchronousCurlData
{
    /** @var callable */
    protected $callback;

    /** @var  mixed */
    protected $data;

    /** @var mixed */
    protected $meta = null;

    /**
     * AsynchronousCurlData constructor.
     *
     * @param callable $callback
     * @param mixed $data
     */
    public function __construct($callback, $data)
    {
        $this->callback = $callback;
        $this->data     = $data;
    }

    /**
     * @return callable
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @param callable $callback
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;
    }

    /** @return mixed */
    public function setMeta($data)
    {
        $this->meta = $data;
    }

    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    public function finalize($ch)
    {
        $content = curl_multi_getcontent($ch);
        call_user_func_array($this->callback, array($content, $ch, $this));
    }
}