<?php

namespace ManiaLivePlugins\eXpansion\Chatlog\Structures;

/**
 * Defines a single chatmessage structure
 *
 * @author Reaby
 */
class ChatMessage extends \Maniaplanet\DedicatedServer\Structures\AbstractStructure
{

    /** @var long */
    public $time;

    /** @var string */
    public $login;

    /** @var string */
    public $nickName;

    /** @var string */
    public $text;

    /**
     * Constructor
     * @param long $stamp
     * @param string $login
     * @param string $nickname
     * @param string $text
     */
    public function __construct($stamp, $login, $nickname, $text)
    {
        $this->time = $stamp;
        $this->login = $login;
        $this->nickName = $nickname;
        $this->text = $text;
    }

    public function __toString()
    {
        return "[" . \ManiaLib\Utils\Formatting::stripStyles($this->nickname) . "] " . $this->text;
    }

}

?>
