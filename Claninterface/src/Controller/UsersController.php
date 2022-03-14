<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Logic\Helper\StringHelper;
use App\Logic\Helper\WN8Helper;
use App\Model\Entity\Meeting;
use App\Model\Entity\Meetingregistration;
use App\Model\Entity\Player;
use App\Model\Entity\Tank;
use App\Model\Entity\User;
use Cake\Mailer\Email;
use Cake\ORM\TableRegistry;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 *
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
       $this->set("users", $this->Users->find("all")->where(["email LIKE"=> "%@%"]));
       $this->set("wgAccounts", $this->Users->find("all")->where(["email NOT LIKE"=> "%@%"]));

    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $user = $this->Users->get($id, ['contain' => ['Players','Tokens', 'Tokens.Players', 'Tokens.Players.Clans']]);

        $this->set('user', $user);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $user = $this->Users->newEmptyEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData(), ['fields' => ['name', 'email', 'password']]);#

            //region Erster Benutzer wird Admin
            $regUsers = $this->Users->find("all")->count();
            if ($regUsers == 0) {
                $user->admin = 1;
            }
            //endregion

            if ($this->Users->save($user)) {
                $this->Flash->success(__('Der Benutzer wurde angelegt'));

                return $this->redirect(['action' => 'login']);
            }
            $this->Flash->error(__('Benutzer konnte nicht angelegt werden'));
        }

        $this->set(compact('user'));
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $players = $this->Users->Players->find('list', ['limit' => 200]);
        $this->set(compact('user', 'players'));
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function dashboard()
    {
        $UserIsAdmin = false;
        if ($this->Auth->user("admin")) {
            $UserIsAdmin = true;
        }

        $token = $this->Users->Tokens->find("all")->contain(["players"])->where(['user_id' => $this->Auth->user("id"), 'Players.rank_id <=' => 2]);
        if ($token->count()) {
            $UserIsAdmin = true;
        }

        $players = $this->Users->Players->find("all");
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
        $this->set("UserIsAdmin", $UserIsAdmin);
        $register = [];
        if($players->count()) {
            /** @var  Player[] $players */
            $meetings = $this->Users->Players->Meetingparticipants->Meetings->find("all")->contain(["Clans"])->where(["date >=" => date("Y-m-d")])->orderAsc("date");
            if ($meetings->count()) {
                /** @var Meeting $meeting */
                foreach ($meetings as $meeting) {
                    foreach ($players as $player) {
                        $data = [];
                        $data["player"] = $player;
                        $data["meeting"] = $meeting;
                        $reg = $this->Users->Players->Meetingregistrations->find("all")->where([
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

    public function login()
    {
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                $this->Auth->setUser($user);
                return $this->redirect($this->Auth->redirectUrl());
            }
            $this->Flash->error('Your username or password is incorrect.');
        }

    }

    public function logout()
    {
        $this->Flash->success('You are now logged out.');
        return $this->redirect($this->Auth->logout());
    }

    public function newpass()
    {
        $id = $this->Auth->user('id');
        $user = $this->Users->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData(), [
                'fields' => [
                    'password_old',
                    'password',
                    'password_confirm'
                ]]);


            if ($this->Users->save($user)) {
                $this->Flash->success(__('Sie haben Ihr Passwort erfolgreich geändert.'));

                return $this->redirect(["controller" => "Users", "action" => "dashboard"]);
            }
            //  $this->Flash->error(__('The Password could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
    }
    public function toggleAdmin($id){
        $this->request->allowMethod(['post', 'delete']);
        $accounts = $this->Users->find("all")->where(["id" => $id, "email LIKE"=> "%@%"]);
        if ($accounts->count() >= 1) {
            $account = $accounts->first();
            $account->admin = ($account->admin >= 1)?0:1;
            $this->Users->save($account);

            $this->Flash->success(__("Adminstatus toggled"));

        }else{
            $this->Flash->error(__("Could not find User"));
        }
        $this->redirect($this->referer());
    }

    public function adminPwReset($id){
        $this->request->allowMethod(['post', 'delete']);
        $accounts = $this->Users->find("all")->where(["id" => $id, "email LIKE"=> "%@%"]);
        if ($accounts->count() >= 1) {
            $account = $accounts->first();
            /**
             * @var User $account
             */
            if($this->pwResetMail($account)){
                $this->Flash->success("Der Nutzer hat ein neues Passwort erhalten");
            }
        }else {
            $this->Flash->error("Kein zurücksetzbares Konto gefunden");
        }
        $this->redirect($this->referer());

    }

    public function unlock()
    {
        $user = $this->Users->newEmptyEntity();
        if ($this->request->is(['patch', 'post', 'put'])) {
            $accounts = $this->Users->find("all")->where(["email" => $this->request->getData("email"), "email LIKE"=> "%@%"]);
            if ($accounts->count() >= 1) {
                /**
                 * @var User $account
                 */
                $account = $accounts->first();
                $this->pwResetMail($account);
            }
            $this->Flash->success("Wir haben Ihnen Ihr  neues Kennwort zugestellt");

        }
        $this->set("user", $user);
    }

    /**
     * @param User $account
     * @return bool
     */
    private function pwResetMail(User $account){
        $newPassword = StringHelper::generateRandomString();
        $account->password = $newPassword;
        $this->Users->save($account);

        $title = "WoT-Claninterface Passwort vergessen";
        $email = new Email();
        $email->setEmailFormat('html');
        $email->viewBuilder()->setLayout('claninterface');
        $email->viewBuilder()->setTemplate('passwortReset');
        $email->setSubject($title);
        $email->setViewVars(['title' => $title]);
        $email->setViewVars(['newPassword' => $newPassword]);
        $email->setViewVars(['user' => $account]);
        $email->setTo($account->email, $account->name);
        if (!$email->send()) {
            $this->Flash->error("Wir konnten keine E-Mail versenden.");
            return false;
        }
        return true;
    }

    public function isAuthorized($user)
    {
        $action = $this->request->getParam('action');
        $action = strtolower($action);
        $user_id = $this->request->getParam('pass.0');
        $pl = $this->permissionLevel;

        if ($pl >= 0 && in_array($action, ["newpass", "dashboard"])) {
            return true;
        }
        if($user["id"] == $user_id && in_array($action,["view"])){
            return  true;
        }

        if ($pl >= 10) {
            return true;
        }
        return false;
    }

    public function initialize(): void
    {
        parent::initialize();
        // Add the 'add' action to the allowed actions list.
        $this->Auth->allow(['logout', 'add', 'unlock']);
    }
}
