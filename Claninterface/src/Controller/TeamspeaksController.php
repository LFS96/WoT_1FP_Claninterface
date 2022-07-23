<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Logic\Helper\ClanRuleHelper;
use App\Logic\Helper\TeamSpeakQueryHelper;
use App\Logic\Helper\WarGamingHelper;
use App\Model\Table\ClansTable;
use Cake\Datasource\ConnectionManager;

/**
 * Teamspeaks Controller
 *
 * @property \App\Model\Table\TeamspeaksTable $Teamspeaks
 *
 * @method \App\Model\Entity\Teamspeak[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TeamspeaksController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->Authorization->authorize($this->LoggedInUsers,"Personal");
        $TS_Online = $this->Teamspeaks->find("all")->contain(["Players", "Players.Clans", "Players.Ranks"])->innerJoinWith("Players.Clans")->where(["end >=" => "1970-01-02", "TIMESTAMPDIFF(SECOND, start, end) > " => "400"])->orderDesc("end")->limit(1000);
        $this->set('OfflineRecords', $TS_Online);

        $clans = $this->Teamspeaks->Players->Clans->find("all");
        $clans = $clans
            ->select(["Clan" => "Clans.short", "bis" => "max(Tokens.expires)"])
            ->leftJoinWith("Players")
            ->innerJoinWith("Players.Tokens")
            ->where(["Tokens.expires >" => $clans->func()->now()])
            ->group("Clans.id");
        $this->set('ClansTimeout', $clans);
    }

    public function nowOffline()
    {
        $this->Authorization->authorize($this->LoggedInUsers,"Personal");
        $CR = new ClanRuleHelper();
        $this->set("MembersOnline", $CR->checkTeamSpeak((new WarGamingHelper())->getOnlinePlayers(), (new TeamSpeakQueryHelper())->getClientlist()));

    }

    public function tsOnline()
    {
        $this->Authorization->authorize($this->LoggedInUsers,"Personal");
        $this->set("online", (new TeamSpeakQueryHelper())->getOnlinePlayersInfo());
    }

    /**
     * Delete method
     *
     * @param string|null $id Teamspeak id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->Authorization->authorize($this->LoggedInUsers,"Personal");
        $this->request->allowMethod(['post', 'delete']);
        $teamspeak = $this->Teamspeaks->get($id);
        if ($this->Teamspeaks->delete($teamspeak)) {
            $this->Flash->success(__('Der Eintrag über den Verstoß wurde gelöscht'));
        } else {
            $this->Flash->error(__('Der Eintrag über den Verstoß konnte nicht gelöscht werden. Bitte erneut versuchen.'));
        }

        return $this->redirect(['action' => 'index']);
    }
    public function deletePlayer($id = null)
    {
        $this->Authorization->authorize($this->LoggedInUsers,"Personal");
        $this->request->allowMethod(['post', 'delete']);
        $teamspeaks = $this->Teamspeaks->find("all")->where(["player_id" => $id]);
        $count = 0;
        foreach ($teamspeaks as $teamspeak) {
            $this->Teamspeaks->delete($teamspeak);
            $count++;
        }
        $this->Flash->success(__('Es wurden '.$count.' Einträge gelöscht'));
        return $this->redirect(['action' => 'players']);
    }

    public function players()
    {
        $this->Authorization->authorize($this->LoggedInUsers,"Personal");
        $connection = ConnectionManager::get('default');
        $TS_Online = $connection
            ->execute('
                SELECT
                       Clans.short AS short,
                       Players.id AS id,
                       Players.nick AS nick,
                       (SUM(TIMESTAMPDIFF(SECOND, start, end))) AS sum,
                       (COUNT(*)) AS count
                FROM teamspeaks Teamspeaks
                INNER JOIN players Players ON Players.id = Teamspeaks.player_id
                INNER JOIN clans Clans ON Clans.id = Players.clan_id
                WHERE
                    (end >= "1970-02-01" AND TIMESTAMPDIFF(SECOND, start, end) > 400)
                GROUP BY Players.id
                ORDER BY
                    Clans.short DESC,
                    Players.nick DESC
                LIMIT 1000
            ')
            ->fetchAll('assoc');
        $this->set('OfflineRecords', $TS_Online);
    }

    /** Baned einen Spieler vom Server
     * @param string $uid CLient ID
     * @return \Cake\Http\Response|null letzte seite
     */
    public function ban($uid)
    {
        $this->Authorization->authorize($this->LoggedInUsers,"Admin");
        $this->request->allowMethod(['post', 'delete']);
        (new TeamSpeakQueryHelper())->banPlayerByUID(hex2bin($uid));
        return $this->redirect($this->referer());
    }

    /** Kickt einen Spieler vom Server
     * @param string $uid CLient ID
     * @return \Cake\Http\Response|null letzte seite
     */
    public function kick($uid)
    {
        $this->Authorization->authorize($this->LoggedInUsers,"Admin");
        $this->request->allowMethod(['post', 'delete']);
        (new TeamSpeakQueryHelper())->kickPlayerByUID(hex2bin($uid));
        return $this->redirect($this->referer());
    }

    public function initialize(): void
    {
        parent::initialize();
    }
}
