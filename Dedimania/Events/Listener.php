<?php

namespace ManiaLivePlugins\eXpansion\Dedimania\Events;

interface Listener extends \ManiaLive\Event\Listener
{

    /**
     * Callback for dedimania.OpenSession
     *
     */
    public function onDedimaniaOpenSession();

    /**
     * Callback for dedimania.GetChallengeRecords
     *
     * $data =  array {'UId' => string, 'ServerMaxRank' => int, 'AllowedGameModes' => string (list of string, comma
     * separated),
     *                'Records' => array of struct {'Login': string, 'NickName': string, 'Best': int, 'Rank': int,
     *                'MaxRank': int, 'Checks': string (list of int, comma separated), 'Vote': int},
     *            'Players' => array of {'Login': string, 'MaxRank': int}, 'TotalRaces' => int, 'TotalPlayers' => int
     *            }:
     * ServerMaxRank: the nominal max number of records for this server,
     * MaxRank in records: the max record rank for the record (can be bigger than ServerMaxRank),
     * MaxRank in players: the max record rank for the player (can be bigger than ServerMaxRank!),
     * Checks: checkpoints times of the associated record.
     * Vote: 0 to 100 value (or -1 if player did not vote for the map).
     *
     * @param array $data
     */
    public function onDedimaniaGetRecords($data);

    /**
     * Callback when records are updated locally, ie when new record is set and recordlist is modified
     * $data =
     *  array {'UId': string, 'ServerMaxRank': int, 'AllowedGameModes': string (list of string, comma separated),
     *         'Records': array of struct {'Login': string, 'NickName': string, 'Best': int, 'Rank': int, 'MaxRank':
     *         int, 'Checks': string (list of int, comma separated), 'Vote': int},
     *         'Players': array of {'Login': string, 'MaxRank': int}, 'TotalRaces': int, 'TotalPlayers': int }:
     * . ServerMaxRank: the nominal max number of records for this server,
     * . MaxRank in records: the max record rank for the record (can be bigger than ServerMaxRank),
     * . MaxRank in players: the max record rank for the player (can be bigger than ServerMaxRank!),
     * . Checks: checkpoints times of the associated record.
     * . Vote: 0 to 100 value (or -1 if player did not vote for the map).
     *
     * @param array $data
     *
     */
    public function onDedimaniaUpdateRecords($data);

    /**
     * Callback when a new record is driven for the map
     *
     * @param Record $record
     */
    public function onDedimaniaNewRecord($record);

    /**
     * Callback when player enhances his own record
     *
     * @param Record $record
     * @param Record $oldrecord
     */
    public function onDedimaniaRecord($record, $oldrecord);

    /**
     * Callback for dedimania.PlayerConnect
     *
     * $data = array {'Login' => string, 'MaxRank' => int, 'Banned' => boolean, 'OptionsEnabled' => boolean,
     * 'ToolOption' => string}, where: MaxRank: max rank for player records, Banned: ban status on Dedimania for the
     * player, OptionsEnabled: true if tool options can be stored for the player, ToolOption: optional value stored for
     * the player by the used tool (can usually be config/layout values, and storable only if player has
     * OptionsEnabled).
     *
     * @param array $data
     */
    public function onDedimaniaPlayerConnect($data);

    /**
     *  Callback for dedimania.PlayerDisconnect
     *
     */
    public function onDedimaniaPlayerDisconnect($login);
}
