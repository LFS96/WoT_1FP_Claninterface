<?php


namespace App\Controller;


use App\Logic\Helper\ClanRuleHelper;
use App\Logic\Helper\MeetingsHelper;
use App\Logic\Helper\PlayerDataHelper;
use App\Logic\Helper\TeamSpeakQueryHelper;
use App\Logic\Helper\WarGamingHelper;
use App\Model\Entity\Clan;
use Cake\ORM\TableRegistry;

class ImportController extends AppController
{
    public function teamspeak(){
        $CR = new ClanRuleHelper();
        $this->set("response",$CR->checkTeamSpeak((new WarGamingHelper())->getOnlinePlayers(),(new TeamSpeakQueryHelper())->getClientlist()));
    }


    public function notice(){
        $this->set("AllClansChecked",(new TeamSpeakQueryHelper())->TeamSpeakSurveillance());
    }
    public function member(){
        $ClansTable = TableRegistry::getTableLocator()->get('Clans');
        $clans = $ClansTable->find("all")->where(["cron" => 1]);
        $WgApi = new WarGamingHelper();
        $PlayerHelper = new PlayerDataHelper();

        /**
         * @var Clan $clan
         */

        $resp = array();

        foreach ($clans as $clan) {
            $resp[$clan->id]["name"] = $clan->name;
            $resp[$clan->id]["member"] = $WgApi->updateClanMemberStatus($clan->id);

        }
        $this->set("response",$resp);
    }

    public function membersstats(){
        $ClansTable = TableRegistry::getTableLocator()->get('Clans');
        $clans = $ClansTable->find("all")->where(["cron" => 1]);
        $WgApi = new WarGamingHelper();
        $PlayerHelper = new PlayerDataHelper();

        /**
         * @var Clan $clan
         */

        $resp = array();

        foreach ($clans as $clan) {
            $resp[$clan->id]["name"] = $clan->name;
            $resp[$clan->id]["member"] = $WgApi->updateClanMembers($clan->id);

        }
        $this->set("response",$resp);
    }

    public function stats(){
        $ClansTable = TableRegistry::getTableLocator()->get('Clans');
        $clans = $ClansTable->find("all")->where(["cron" => 1]);
         $WgApi = new WarGamingHelper();
        $PlayerHelper = new PlayerDataHelper();

        /**
         * @var Clan $clan
         */

        $resp = array();

        foreach ($clans as $clan) {

            $resp[$clan->id]["name"] = $clan->name;
            $resp[$clan->id]["member"] = $WgApi->updateClanMemberStatus($clan->id);

            $resp[$clan->id]["stats"] = $PlayerHelper->importPlayerStatisticV2($clan->id);
            // $io->out($clan->short." Es wurden $c Datensätze geladen.");
        }

        $this->set("response",$resp);
    }

    public function clean(){
        $ph = new PlayerDataHelper();
        $this->set("players", $ph->cleanUpPlayer());
        $this->set("stats", $ph->cleanUpStatisics());
    }

    public function meeting(){
        MeetingsHelper::createFollowMeeting();
        MeetingsHelper::findParticipant();
    }

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);

        // All public actions are allowed for guests.
        $this->Authentication->allowUnauthenticated(get_class_methods($this));
        $this->Authorization->skipAuthorization();
    }

}
