<?php

namespace ManiaLivePlugins\eXpansion\TMKarma\Structures;

class Karma
{
    public $challengeUid;
    public $fantanstic;
    public $beautiful;
    public $good;
    public $bad;
    public $poor;
    public $waste;
    public $score;
    public $total;
    public $votes;

    /**
     * Just copy all the response values into
     * the Karma object and do simple calculations.
     *
     * @param $response
     */
    public function __construct($response = null)
    {
        $this->votes = array();

        if ($response === null) {
            return;
        }

        $this->challengeUid = (string)$response->uid;
        $this->fantanstic = new Rating($response->votes->fantastic);
        $this->beautiful = new Rating($response->votes->beautiful);
        $this->good = new Rating($response->votes->good);
        $this->bad = new Rating($response->votes->bad);
        $this->poor = new Rating($response->votes->poor);
        $this->waste = new Rating($response->votes->waste);
        $this->score = (int)$response->votes->karma;

        // calculate total votes
        $this->total = $this->fantanstic->count + $this->beautiful->count
            + $this->good->count + $this->bad->count
            + $this->poor->count + $this->waste->count;

        // parse all player votes
        $this->votes = array();
        //var_dump($response);
        foreach ($response->players as $player) {
            $this->votes[(string)$player['login']] = (int)$player['vote'];
        }
    }

    /**
     * Copies the values of one Karma object to another.
     * This keeps the votes array intact.
     *
     * @param Karma $from
     * @param Karma $to
     */
    public static function copy(Karma $from, Karma $to)
    {
        // copy all properties to the destination
        foreach ($from as $property => $value) {
            if (property_exists($to, $property) && $property != 'votes') {
                $to->$property = $value;
            }
        }

        // merge the votes in the destination object
        $to->votes = array_merge($to->votes, $from->votes);

        return $to;
    }
}
