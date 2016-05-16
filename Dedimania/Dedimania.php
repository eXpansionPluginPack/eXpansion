<?php

namespace ManiaLivePlugins\eXpansion\Dedimania;

use ManiaLivePlugins\eXpansion\Dedimania\Classes\Connection as DediConnection;
use ManiaLivePlugins\eXpansion\Dedimania\Events\Event as DediEvent;

class Dedimania extends DedimaniaAbstract
{
    private $checkpoints = array();
    private $bestTimes = array();

    public function expOnInit()
    {
        parent::expOnInit();
    }

    public function onBeginMap($map, $warmUp, $matchContinuation)
    {
        if (!$this->running) {
            return;
        }
        $this->records = array();
        $this->rankings = array();
        $this->vReplay = "";
        $this->gReplay = "";
        $this->checkpoints = array();
        $this->bestTimes = array();
    }

    public function onPlayerFinish($playerUid, $login, $time)
    {
        if (!$this->running) {
            return;
        }
        if ($time == 0) {
            return;
        }

        if ($this->storage->currentMap->nbCheckpoints == 1) {
            return;
        }

        if (!array_key_exists($login, DediConnection::$players)) {
            return;
        }

        // if player is banned from dedimania, don't send his time.
        if (DediConnection::$players[$login]->banned) {
            return;
        }

        if (self::eXpGetCurrentCompatibilityGameMode() == \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_LAPS) {
            return;
        }

        $this->handlePlayerFinish($playerUid, $login, $time, $this->storage->getPlayerObject($login)->bestCheckpoints);
    }

    /**
     * @param \ManiaLive\Data\Player $player
     * @param                        $time
     * @param                        $checkpoints
     * @param int $nbLap
     */
    public function onPlayerFinishLap($player, $time, $checkpoints, $nbLap)
    {
        $gamemode = self::eXpGetCurrentCompatibilityGameMode();

        if ($gamemode != \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_LAPS) {
            return;
        }

        $login = $player->login;

        if (!array_key_exists($login, $this->bestTimes)) {
            $this->bestTimes[$login] = $time;
            $this->checkpoints[$login] = $checkpoints;
        } else {
            if ($time < $this->bestTimes[$login]) {
                $this->bestTimes[$login] = $time;
                $this->checkpoints[$login] = $checkpoints;
            }
        }
        $this->handlePlayerFinish($player->playerId, $player->login, $time, $checkpoints);
    }

    public function handlePlayerFinish($playerUid, $login, $time, $checkpoints)
    {
        if (is_null(DediConnection::$dediMap)) {
            return;
        }

        // if current map doesn't have records, create one.
        if (count($this->records) == 0) {
            $player = $this->storage->getPlayerObject($login);

            $this->records[$login] = new Structures\DediRecord($login, $player->nickName, DediConnection::$players[$login]->maxRank, $time,
                -1, $checkpoints);
            $this->reArrage($login);
            \ManiaLive\Event\Dispatcher::dispatch(new DediEvent(DediEvent::ON_NEW_DEDI_RECORD, $this->records[$login]));

            return;
        }

        // if last record is not set, don't continue.
        if (!is_object($this->lastRecord)) {
            return;
        }

        // so if the time is better than the last entry or the count of records

        $maxrank = DediConnection::$serverMaxRank;
        if (DediConnection::$players[$login]->maxRank > $maxrank) {
            $maxrank = DediConnection::$players[$login]->maxRank;
        }

        if ($time <= $this->lastRecord->time || count($this->records) <= $maxrank) {

            //  print "times matches!";
            // if player exists on the list... see if he got better time

            $player = $this->storage->getPlayerObject($login);

            if (array_key_exists($login, $this->records)) {
                if ($this->records[$login]->time > $time) {
                    $oldRecord = $this->records[$login];

                    $this->records[$login] = new Structures\DediRecord($login, $player->nickName, DediConnection::$players[$login]->maxRank,
                        $time, -1, $checkpoints);

                    // if new records count is greater than old count, and doesn't exceed the maxrank of the server
                    $oldCount = count($this->records);
                    if ((count(
                                $this->records
                            ) > $oldCount) && ((DediConnection::$dediMap->mapMaxRank + 1) < DediConnection::$serverMaxRank)
                    ) {
                        //print "increasing maxrank! \n";
                        DediConnection::$dediMap->mapMaxRank++;
                    }
                    $this->reArrage($login);
                    // have to recheck if the player is still at the dedi array
                    if (array_key_exists(
                        $login, $this->records
                    )
                    ) // have to recheck if the player is still at the dedi array
                    {
                        \ManiaLive\Event\Dispatcher::dispatch(
                            new DediEvent(DediEvent::ON_DEDI_RECORD, $this->records[$login], $oldRecord)
                        );
                    }

                    return;
                }

                // if not, add the player to records table
            } else {
                $oldCount = count($this->records);
                $this->records[$login] = new Structures\DediRecord($login, $player->nickName, DediConnection::$players[$login]->maxRank,
                    $time, -1, $checkpoints);
                // if new records count is greater than old count, increase the map records limit

                if ((count(
                            $this->records
                        ) > $oldCount) && ((DediConnection::$dediMap->mapMaxRank + 1) < DediConnection::$serverMaxRank)
                ) {

                    DediConnection::$dediMap->mapMaxRank++;
                }
                $this->reArrage($login);

                // have to recheck if the player is still at the dedi array
                if (array_key_exists($login, $this->records)) {
                    \ManiaLive\Event\Dispatcher::dispatch(
                        new DediEvent(DediEvent::ON_NEW_DEDI_RECORD, $this->records[$login])
                    );
                }

                return;
            }
        }
    }

    /**
     *
     * @param array $rankings
     * @param string $winnerTeamOrMap
     *
     */
    public function onEndMatch($rankings, $winnerTeamOrMap)
    {
        if (!$this->running) {
            return;
        }
        if ($this->wasWarmup) {
            $this->console("[Dedimania] the last round was warmup, deditimes not send for warmup!");

            return;
        }

        $gamemode = self::eXpGetCurrentCompatibilityGameMode();

        if ($gamemode == \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_LAPS) {
            foreach ($rankings as $number => $rank) {
                $login = $rank['Login'];
                if (array_key_exists($login, $this->bestTimes)) {
                    $rankings[$number]['AllCheckpoints'] = $rankings[$number]['BestCheckpoints'];
                    $rankings[$number]['BestCheckpoints'] = $this->checkpoints[$login];
                    $rankings[$number]['BestTime'] = $this->bestTimes[$login];
                    $rankings[$number]['Score'] = count($this->bestTimes[$login]);
                }
            }
        }

        if ($this->expStorage->isRelay) {
            return;
        }

        try {
            if (sizeof($rankings) == 0) {
                $this->vReplay = "";
                $this->gReplay = "";

                return;
            }
            $this->vReplay = $this->connection->getValidationReplay($rankings[0]['Login']);
            $greplay = "";
            $grfile = sprintf(
                'Dedimania/%s.%d.%07d.%s.Replay.Gbx', $this->storage->currentMap->uId, $this->storage->gameInfos->gameMode,
                $rankings[0]['BestTime'], $rankings[0]['Login']
            );
            $this->connection->SaveBestGhostsReplay($rankings[0]['Login'], $grfile);
            $this->gReplay = file_get_contents($this->connection->gameDataDirectory() . 'Replays/' . $grfile);

            // Dedimania doesn't allow times sent without validation relay. So, let's just stop here if there is none.
            if (empty($this->vReplay)) {
                $this->console(
                    "[Dedimania] Couldn't get validation replay of the first player. Dedimania times not sent."
                );

                return;
            }

            $this->dedimania->setChallengeTimes(
                $this->storage->currentMap, $rankings, $this->vReplay, $this->gReplay
            );
        } catch (\Exception $e) {
            $this->console("[Dedimania] " . $e->getMessage());
            $this->vReplay = "";
            $this->gReplay = "";
        }
        // ignore exception and other, always reset;
        $this->checkpoints = array();
        $this->bestTimes = array();
    }

}
