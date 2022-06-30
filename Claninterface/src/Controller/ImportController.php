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
        $CR->checkTeamSpeak((new WarGamingHelper())->getOnlinePlayers(),(new TeamSpeakQueryHelper())->getClientlist());
    }

    public function import(){
        $ph = new PlayerDataHelper();
        $ph->cleanUpPlayer();
    }

    public function notice(){
        (new TeamSpeakQueryHelper())->TeamSpeakSurveillance();
    }

    public function stats(){
        $ClansTable = TableRegistry::getTableLocator()->get('Clans');
        $clans = $ClansTable->find("all")->where(["cron" => 1]);

        /**
         * @var Clan $clan
         */
        foreach ($clans as $clan) {
            $PlayerHelper = new PlayerDataHelper();
            $c = $PlayerHelper->importPlayerStatistic($clan->id);
            // $io->out($clan->short." Es wurden $c Datensätze geladen.");
        }
    }

    public function clean(){
        $ph = new PlayerDataHelper();
        $ph->cleanUpPlayer();
    }

    public function meeting(){
        MeetingsHelper::createFollowMeeting();
        MeetingsHelper::findParticipant();
    }

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        // Configure the login action to not require authentication, preventing
        // the infinite redirect loop issue
        $this->Authentication->addUnauthenticatedActions(['teamspeak', 'import', 'notice', 'stats']);
    }

}
