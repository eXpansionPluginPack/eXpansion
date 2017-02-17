<?php
namespace ManiaLivePlugins\eXpansion\Chatlog\Structures;

use ManiaLib\Utils\Formatting;
use Maniaplanet\DedicatedServer\Structures\AbstractStructure;

/**
 * Defines a single chatmessage structure
 *
 * @author Reaby
 */
class ChatMessage extends AbstractStructure
{

    /** @var integer */
    public $time;

    /** @var string */
    public $login;

    /** @var string */
    public $nickName;

    /** @var string */
    public $text;

    /**
     * Constructor
     *
     * @param integer $stamp
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
        return "[" . Formatting::stripStyles($this->nickName) . "] " . $this->text;
    }
}
