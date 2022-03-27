<?php


namespace App\Logic\Helper;


use App\Logic\Config\WgApi;
use App\Model\Entity\Statistic;
use App\Model\Entity\Tank;
use Cake\ORM\TableRegistry;

class WN8Helper
{

    /**
     * Diese Funktion berechnet die WN8 eines Spielers
     *
     * Diese Funktion basiert auf dem Werk von artur-stepien https://github.com/artur-stepien/wot-wn8/blob/master/src/wn8.php
     * Geändert wurden externe zugriffe sofern diese Daten auch lokal bereit stehen
     *
     * @param $account_id
     * @return float|int
     * @author GitHub artur-stepien
     * @author Fabian Harmsen
     */
    public static function getPlayerWN8($account_id)
    {
        $api = WgApi::getWG_API();
        $summary = $api->get('wot/account/info', array(
            'fields' => 'statistics.all.battles,statistics.all.frags,statistics.all.damage_dealt,statistics.all.dropped_capture_points,statistics.all.spotted,statistics.all.wins',
            'account_id' => $account_id
        ))->$account_id->statistics->all;
        $tanks = $api->get('wot/account/tanks', array('fields' => 'tank_id,statistics.battles', 'account_id' => $account_id))->$account_id;

        // If this account has no tanks data skip calculation and return 0
        if (empty($tanks)) {
            return 0;
        }

        // WN8 expected calculation
        $TanksTable = TableRegistry::getTableLocator()->get('Tanks');
        /**
         * @var Tank[] $tanksData
         */
        $tanksData = $TanksTable->find("all");
        $expectedTankValues = [];
        foreach ($tanksData as $tanksDatum) {
            $expectedTankValues [$tanksDatum->id] = $tanksDatum;
        }


        $expDAMAGE = $expFRAGS = $expSPOT = $expDEF = $expWIN = 0;
// Tanks missing in expected tank values but existing in account


        // Calculated account expected values
        foreach ($tanks as $tank) {

            // Tank exists in expected tank values
            if (key_exists($tank->tank_id, $expectedTankValues)) {

                // Expected values for current tank
                $expected = $expectedTankValues[$tank->tank_id];

                // Battles on current tank
                $tank_battles = $tank->statistics->battles;

                // Calculate expected values for current tank
                $expDAMAGE += $expected->expDamage * $tank_battles;
                $expSPOT += $expected->expSpot * $tank_battles;
                $expFRAGS += $expected->expFrag * $tank_battles;
                $expDEF += $expected->expDef * $tank_battles;
                $expWIN += 0.01 * $expected->expWinRate * $tank_battles;

                // Tank missing in expected tank values so add it to the list
            }
        }

        // Calculate WN8
        $rDAMAGE = $expDAMAGE==0?0: $summary->damage_dealt / $expDAMAGE;
        $rSPOT =   $expSPOT==0?0:   $summary->spotted / $expSPOT;
        $rFRAG =   $expFRAGS==0?0:  $summary->frags / $expFRAGS;
        $rDEF =    $expDEF==0?0:    $summary->dropped_capture_points / $expDEF;
        $rWIN =    $expWIN==0?0:    $summary->wins / $expWIN;

        $rWINc = max(0, ($rWIN - 0.71) / (1 - 0.71));
        $rDAMAGEc = max(0, ($rDAMAGE - 0.22) / (1 - 0.22));
        $rFRAGc = max(0, min($rDAMAGEc + 0.2, ($rFRAG - 0.12) / (1 - 0.12)));
        $rSPOTc = max(0, min($rDAMAGEc + 0.1, ($rSPOT - 0.38) / (1 - 0.38)));
        $rDEFc = max(0, min($rDAMAGEc + 0.1, ($rDEF - 0.10) / (1 - 0.10)));

        $wn8 = 980 * $rDAMAGEc + 210 * $rDAMAGEc * $rFRAGc + 155 * $rFRAGc * $rSPOTc + 75 * $rDEFc * $rFRAGc + 145 * MIN(1.8, $rWINc);
	$wn8 = ($wn8 < 0)?0:$wn8;
	$wn8 = ($wn8 >999999)?1000000:$wn8;
	return $wn8 ;
    }




/**
 * @param Statistic $stat
 * @return float|int
 */
public
static function calcWN8(Statistic $stat, $tank = false)
{
    if (!$tank) {
        $tank = $stat->Tanks;
    }


    $DAMAGE = $stat->damage / $stat->battle;
    $SPOT = $stat->spotted / $stat->battle;
    $FRAG = $stat->frags / $stat->battle;
    $DEF = $stat->droppedCapturePoints / $stat->battle;
    $WIN = ($stat->win * 100) / $stat->battle;
    //  echo ("|DMG :".$DAMAGE."|SPOT :".$SPOT."|FRAG :".$FRAG."|DEF :".$DEF."|WIN :".$WIN );

    //Step 1
    $rDAMAGE = $DAMAGE / $tank["expDamage"];
    $rSPOT = $SPOT / $tank["expSpot"];
    $rFRAG = $FRAG / $tank["expFrag"];
    $rDEF = $DEF / $tank["expDef"];
    $rWIN = $WIN / $tank["expWinRate"];
    //  echo ("|rDMG :".$rDAMAGE."|rSPOT :".$rSPOT."|rFRAG :".$rFRAG."|rDEF :".$rDEF."|rWIN :".$rWIN );

    //Step2
    $rWINc = max(0, ($rWIN - 0.71) / (1 - 0.71));
    $rDAMAGEc = max(0, ($rDAMAGE - 0.22) / (1 - 0.22));
    $rFRAGc = max(0, min($rDAMAGEc + 0.2, ($rFRAG - 0.12) / (1 - 0.12)));
    $rSPOTc = max(0, min($rDAMAGEc + 0.1, ($rSPOT - 0.38) / (1 - 0.38)));
    $rDEFc = max(0, min($rDAMAGEc + 0.1, ($rDEF - 0.10) / (1 - 0.10)));
    // echo ("|rWINc :".$rWINc."|rDAMAGEc :".$rDAMAGEc."|rFRAGc :".$rFRAGc."|rSPOTc :".$rSPOTc."|rDEFc :".$rDEFc );

    return 980 * $rDAMAGEc + 210 * $rDAMAGEc * $rFRAGc + 155 * $rFRAGc * $rSPOTc + 75 * $rDEFc * $rFRAGc + 145 * MIN(1.8, $rWINc);
}

public
static function WnColor($n)
{
    $class = "wn8 wn8-black";
    if ($n >= 500) {
        $class = "wn8 wn8-red";
    }
    if ($n >= 700) {
        $class = "wn8 wn8-orange";
    }
    if ($n >= 900) {
        $class = "wn8 wn8-yellow";
    }
    if ($n >= 1100) {
        $class = "wn8 wn8-green";
    }
    if ($n >= 1350) {
        $class = "wn8 wn8-darkgreen";
    }
    if ($n >= 1550) {
        $class = "wn8 wn8-blue";
    }
    if ($n >= 1850) {
        $class = "wn8 wn8-violett";
    }
    if ($n >= 2050) {
        $class = "wn8 wn8-purple";
    }

    return $class;
}

public
static function SiegColor($n)
{
    $class = "wn8 wn8-black";
    if ($n >= 45) {
        $class = "wn8 wn8-red";
    }
    if ($n >= 47) {
        $class = "wn8 wn8-orange";
    }
    if ($n >= 49) {
        $class = "wn8 wn8-yellow";
    }
    if ($n >= 52) {
        $class = "wn8 wn8-green";
    }
    if ($n >= 54) {
        $class = "wn8 wn8-darkgreen";
    }
    if ($n >= 56) {
        $class = "wn8 wn8-blue";
    }
    if ($n >= 60) {
        $class = "wn8 wn8-violett";
    }
    if ($n >= 65) {
        $class = "wn8 wn8-purple";
    }

    return $class;
}

}
