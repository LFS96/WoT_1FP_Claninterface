<?php


namespace App\Logic\Helper;


use App\Logic\Config\StatisticsConfigHelper;
use App\Logic\Config\WgApi;
use App\Model\Entity\Player;
use App\Model\Entity\Statistic;
use App\Model\Table\PlayersTable;

use App\Model\Table\StatisticsTable;
use Cake\Console\ConsoleIo;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Exception;
use Wargaming\Api;

class PlayerDataHelper
{
    private ?Api $api;
    private $wn8expectedValues = null;
    private $tanktypes = null;

    public function __construct()
    {
        $this->api = WgApi::getWG_API();

    }


    /**
     * Aktualisiert die Panzer-Statistiken aller Spieler eines Clans
     * @param int $clan
     * @return int
     */
    public function importPlayerStatisticV2(int $clan):int
    {
        $connection = ConnectionManager::get('default');

        $WgApiResults = array();
        $player_array = array();
        $counter = 0;

        //Dates switch on 04am, nightshift is last date
        $statDate = time() - (24 * 60 * 60);
        if (time() >= strtotime("04:00:00")) {
            $statDate = time();
        }

        //Get all players from clan
        $players = $connection->execute("SELECT p.id, token FROM players as p
	                                LEFT JOIN tokens as t ON t.player_id = p.id AND t.expires > now()
	                                WHERE clan_id = :clan", ["clan" => $clan])
            ->fetchAll('assoc');


        foreach ($players as $player) {
            $player_array[$player["id"]] = $player["token"] ?? null;
        }
        unset($players);

        foreach ($player_array as $player => $token) {
            $body = ["account_id" => $player, "fields" => StatisticsConfigHelper::$FieldsList];
            try {
                if ($token !== null) {
                    $body["access_token"] = $token;
                }
                $resp = WgApi::getWG_API()->get("wot/tanks/stats/", $body);
            } catch (Exception $e) {
                continue;
            }
            if (isset($resp?->{$player})) {
                $WgApiResults [$player] = $resp->{$player};
            }
            $resp = null;
        }


        foreach ($player_array as $player => $token) {
            foreach ($WgApiResults[$player] as $tankStat) {
                foreach (StatisticsConfigHelper::$BattleTypes as $battleType) {
                    $battleTypeStat = $tankStat->$battleType;
                    if ($battleTypeStat->battles) {

                        $inGarage = -1;
                        if(isset($tankStat->in_garage) && $tankStat->in_garage !== null){
                            $inGarage = $tankStat->in_garage?1:0;
                        }


                        $connection
                            ->execute('INSERT INTO statistics
                                            (
                                             player_id, tank_id, `date`, date_b, battletype, damage, spotted, frags, droppedCapturePoints,
                                             battle, win, in_garage, modified, created, shots, xp, hits, survived, tanking
                                             )
                                    VALUES
                                        (:player_id, :tank_id, :date_a, :date_b, :battletype, :damage, :spotted, :frags, :droppedCapturePoints,
                                        :battle, :win, :in_garage, :modified, :created, :shots, :xp, :hits,:survived,:tanking)
                                    ON DUPLICATE KEY
                                    UPDATE date_b=curdate()',
                                [
                                    "player_id" => $player,
                                    "tank_id" => $tankStat->tank_id,
                                    "date_a" => date("Y-m-d", $statDate),
                                    "date_b" => date("Y-m-d", $statDate),
                                    "battletype" => $battleType,
                                    //WN8
                                    "damage" => $battleTypeStat->damage_dealt,
                                    "spotted" => $battleTypeStat->spotted,
                                    "frags" => $battleTypeStat->frags,
                                    "droppedCapturePoints" => $battleTypeStat->dropped_capture_points,
                                    "battle" => $battleTypeStat->battles,
                                    "win" => $battleTypeStat->wins,
                                    "in_garage" => $inGarage,
                                    //DateTime
                                    "modified" => date("Y-m-d H:i:s"),
                                    "created" => date("Y-m-d H:i:s"),
                                    //erweitert
                                    "shots" => $battleTypeStat->shots,
                                    "xp" => $battleTypeStat->xp,
                                    "hits" => $battleTypeStat->hits,
                                    "survived" => $battleTypeStat->survived_battles,
                                    "tanking" => intval($battleTypeStat->tanking_factor * 100),


                                ]);
                        $counter++;
                    }
                }
            }
        }
        return $counter;
    }


    /**
     *  Löscht alle Spieler, die den Clan verlassen haben.
     * @return string
     */
    public function cleanUpPlayer(): string
    {

        $out = "";

        $PlayersTable = TableRegistry::getTableLocator()->get('Players');
        $players = $PlayersTable->find()->where(function ($exp) {
            return $exp->isNull('clan_id');
        });

        $clan_array = WarGamingHelper::getClanListArray();

        /**
         * @var Player $player
         */
        foreach ($players as $player) {
            try {
                $resp = $this->api->get("wot/clans/memberhistory/", ["account_id" => $player->id]);
            } catch (Exception $e) {
                continue;
            }
            $data = $resp->{$player->id};


            $left = 0;
            foreach ($data as $clan_hist) {
                if (in_array($clan_hist->clan_id, $clan_array)) {
                    if ($left <= $clan_hist->left_at) {
                        $left = $clan_hist->left_at;
                    }
                }
            }
            $days = Configure::read('PlayerData.DelAfterDaysLeft');

            $diff = floor((time() - $left) / (24 * 60 * 60));
            if ($diff >= $days) {
                $out .= "Spieler '{$player->nick}' wird gelöscht, ist vor $diff Tagen am " . date("d.m.Y", $left) . " ausgetreten" . PHP_EOL;
                $PlayersTable->delete($player);
            } else {
                $out .= "Spieler '{$player->nick}' ist erst vor $diff Tagen ausgetreten" . PHP_EOL;
            }
        }
        return $out;
    }

    public function cleanUpStatisics():string
    {
        $result = "";

        $connection = ConnectionManager::get('default');
        $s = $connection->execute("DELETE s FROM statistics as s LEFT JOIN players as p ON p.id = s.player_id WHERE date_b < curdate() - INTERVAL 1 MONTH AND p.id IS NULL")->rowCount();
        $result .= "Löschte $s Einträge aus der Statistiktabelle" . PHP_EOL;
        $t = $connection->execute("DELETE t FROM tokens as t LEFT JOIN players as p ON p.id = t.player_id WHERE p.id IS NULL")->rowCount();
        $result .= "Löschte $t Einträge aus der Token-Tabelle" . PHP_EOL;
        $t = $connection->execute("DELETE t FROM teamspeaks as t LEFT JOIN players as p ON p.id = t.player_id WHERE p.id IS NULL")->rowCount();
        $result .= "Löschte $t Einträge aus der Teamspeaks-Tabelle" . PHP_EOL;
        return $result;
    }
}
