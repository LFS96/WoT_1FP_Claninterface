<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Model\Entity\Meeting;
use App\Model\Entity\Meetingregistration;
use App\Model\Entity\Player;

/**
 * Meetingregistrations Controller
 *
 * @property \App\Model\Table\MeetingregistrationsTable $Meetingregistrations
 *
 * @method \App\Model\Entity\Meetingregistration[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MeetingregistrationsController extends AppController
{
    public function setRegistrations($player, $meeting, $status){
       $registrations =  $this->Meetingregistrations->find()->where(["player_id"=> $player, "meeting_id" => $meeting]);
        $registration = null;
       if($registrations->count()){
           $registration = $registrations->first();
       }else{
           $registration = $this->Meetingregistrations->newEmptyEntity();
           $registration->player_id = $player;
           $registration->meeting_id = $meeting;
       }
       $registration->status = $status;
       $this->Meetingregistrations->save($registration);
       return $this->redirect($this->referer());
    }



    public function calender(){
        $players = $this->Meetingregistrations->Players->find("all");
        $players = $players
            ->select([
                "clanName" => "Clans.short",
                "Players.nick",
                "id" => "Players.id",
                "rank" => "Ranks.speekName",
                "rankIcon" => "Ranks.name",
                "expires" => "max(Tokens.expires)"
            ])
            ->innerJoinWith("tokens")
            ->innerJoinWith("Ranks")
            ->innerJoinWith("Clans")
            ->where([
                'Tokens.user_id' => $this->Auth->user("id"),
                "Tokens.expires >" => $players->func()->now()
            ])
            ->group("Players.id")
            ->orderAsc("rank_id")
            ->orderAsc("nick");

        $this->set("Players", $players);

        $register = [];
        if($players->count()) {
            /** @var  Player[] $players */
            $meetings = $this->Meetingregistrations->Meetings->find("all")->contain(["Clans"])->where(["date >=" => date("Y-m-d")])->orderAsc("date");
            if ($meetings->count()) {
                /** @var Meeting $meeting */
                foreach ($meetings as $meeting) {
                    foreach ($players as $player) {
                        $data = [];
                        $data["player"] = $player;
                        $data["meeting"] = $meeting;
                        $reg = $this->Meetingregistrations->find("all")->where([
                            "player_id" => $player->id,
                            "meeting_id" => $meeting->id,
                        ]);
                        $data["status"]  = -1;
                        if($reg->count()) {
                            /** @var Meetingregistration $reg */
                            $reg = $reg->first();
                            $data["status"] = $reg->status;

                        }
                        $register [] = $data;
                    }
                }
            }
        }
        $this->set("registrations", $register);
    }


    public function isAuthorized($user)
    {
        $player = $this->request->getParam('pass.0');
        $x =$this->Meetingregistrations->Players->Tokens
            ->find("all")
            ->where(["player_id" => $player, "user_id" => $user["id"], "expires >=" => date("Y-m-d H:i:s")]);

        if($x->count()){
            return true;
        }
        return false;
    }

}
