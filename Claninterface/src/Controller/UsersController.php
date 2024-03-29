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
       $this->Authorization->authorize($this->LoggedInUsers,"Admin");
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
        $this->Authorization->authorize($this->LoggedInUsers,"Admin");
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
        $this->Authorization->skipAuthorization();
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
        $this->Authorization->authorize($this->LoggedInUsers,"Admin");
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
        $this->Authorization->authorize($this->LoggedInUsers,"Admin");
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
        $this->Authorization->skipAuthorization();
        $UserIsAdmin = false;
        if ($this->LoggedInUsers->admin) {
            $UserIsAdmin = true;
        }

        $token = $this->Users->Tokens->find("all")->contain(["Players"])->where(['user_id' => $this->LoggedInUsers->id, 'Players.rank_id <=' => 2]);
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
            ->innerJoinWith("Tokens")
            ->innerJoinWith("Ranks")
            ->innerJoinWith("Clans")
            ->where([
                'Tokens.user_id' => $this->LoggedInUsers->id,
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
        $this->Authorization->skipAuthorization();
        $this->request->allowMethod(['get', 'post']);
        $result = $this->Authentication->getResult();
        // regardless of POST or GET, redirect if user is logged in
        if ($result->isValid()) {
            // redirect to /articles after login success
            $target =  ['controller' => 'Users','action' => 'dashboard'];
            return $this->redirect($target);
        }
        // display error if user submitted and authentication failed
        if ($this->request->is('post') && !$result->isValid()) {
            $this->Flash->error(__('Invalid username or password'));
        }

    }

    public function logout()
    {
        $this->Authorization->skipAuthorization();
        $result = $this->Authentication->getResult();
        // regardless of POST or GET, redirect if user is logged in
        if ($result->isValid()) {
            $this->Authentication->logout();
            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        }
    }

    public function newpass()
    {

        $this->Authorization->skipAuthorization();
        $user = $this->LoggedInUsers;
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
        $this->Authorization->authorize($this->LoggedInUsers,"Admin");
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
        $this->Authorization->authorize($this->LoggedInUsers,"Admin");
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
        $this->Authorization->authorize($this->LoggedInUsers,"Admin");
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



    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        // Configure the login action to not require authentication, preventing
        // the infinite redirect loop issue
        $this->Authentication->addUnauthenticatedActions(['logout', 'add', 'login']);
    }
}
