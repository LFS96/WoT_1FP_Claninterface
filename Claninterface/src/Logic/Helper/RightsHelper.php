<?php


namespace App\Logic\Helper;


use App\Model\Entity\Token;
use App\Model\Entity\User;
use Cake\ORM\TableRegistry;

class RightsHelper
{
    private $clan = false;
    public readonly bool $isAdmin;
    public readonly bool $isCommander;
    public readonly bool $isFieldCommander;
    public readonly bool $isPersonal;
    public readonly bool $isOfficer;
    public readonly bool $isMember;


    const Commander = [1,2];
    const FieldCommander = [1,2,4,5];
    const PersonalCommander = [1,2,3,7];
    const Officer = [1,2,3,4,5,6,7,8];


    /**
     * @param int $user
     */
    public function __construct($user)
    {
        $this->findPermissionLevel($user);
    }

    /**
     * Findet die Berechtigung eines Users
     * @param int|User $user ID des Nutzeraccounts
     */
    public function findPermissionLevel(int|User $user)
    {
        $UsersTable = TableRegistry::getTableLocator()->get('Users');

        if (is_int($user)) {
            $users = $UsersTable->find("all")->where(['id' => $user]);
            if ($users->count()) {
                $account = $users->first();
            }
        } else {
            $account = $user;
        }


        if ($account->admin >= 1 && filter_var($account->email, FILTER_VALIDATE_EMAIL)) {
            if(!isset($this->isAdmin)){$this->isAdmin = true;}
            if(!isset($this->isCommander)){$this->isCommander = true;}
            if(!isset($this->isFieldCommander)){$this->isFieldCommander = true;}
            if(!isset($this->isPersonal)){$this->isPersonal = true;}
            if(!isset($this->isOfficer)){$this->isOfficer = true;}
            if(!isset($this->isMember)){$this->isMember = true;}
        }

        $TokensTables = TableRegistry::getTableLocator()->get('Tokens');
        $date = (new \DateTime());
        $tokens = $TokensTables->find("all")->contain(["Players", "Players.Clans"])->where(['user_id' => $account->id, "expires >" => $date] );
        if($tokens->count()){
            /** @var Token $token */
            foreach ($tokens as $token){
                if(!isset($this->isCommander) && in_array($token?->player->rank_id, self::Commander) ){$this->isCommander = true;}
                if(!isset($this->isFieldCommander)&& in_array($token?->player->rank_id, self::FieldCommander)){$this->isFieldCommander = true;}
                if(!isset($this->isPersonal)&& in_array($token?->player->rank_id, self::PersonalCommander)){$this->isPersonal = true;}
                if(!isset($this->isOfficer)&& in_array($token?->player->rank_id, self::Officer)){$this->isOfficer = true;}
                if(!isset($this->isMember)){$this->isMember = true;}
            }
        }




        if(!isset($this->isAdmin)){$this->isAdmin = false;}
        if(!isset($this->isCommander)){$this->isCommander = false;}
        if(!isset($this->isFieldCommander)){$this->isFieldCommander = false;}
        if(!isset($this->isPersonal)){$this->isPersonal = false;}
        if(!isset($this->isOfficer)){$this->isOfficer = false;}
        if(!isset($this->isMember)){$this->isMember = false;}
    }

    /**
     * @return bool
     */
    public function isClan(): bool
    {
        return $this->clan;
    }
}
