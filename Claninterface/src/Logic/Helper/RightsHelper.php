<?php


namespace App\Logic\Helper;


use App\Model\Entity\User;
use Cake\ORM\TableRegistry;

class RightsHelper
{
    private $clan = false;
    private $PermissionLevel = 0;

    /**
     * @param int $user
     */
    public function __construct($user)
    {
        $this->PermissionLevel = $this->findPermissionLevel($user);
    }

    /**
     * Findet die Berechtigung eines Users
     * @param int|User $user ID des Nutzeraccounts
     * @return int Berechtigungslevel
     */
    public function findPermissionLevel(int|User $user): int
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
            return 10;
        }

        $TokensTables = TableRegistry::getTableLocator()->get('Tokens');
        $token = $TokensTables->find("all")->contain(["players", "players.Clans"])->where(['user_id' => $account->id, 'Players.rank_id <=' => 2]);
        if ($token->count()) {
            return 8;
        }

        $TokensTables = TableRegistry::getTableLocator()->get('Tokens');
        $token = $TokensTables->find("all")->contain(["players", "players.Clans"])->where(['user_id' => $account->id, 'Players.rank_id =' => 4]);
        if ($token->count()) {
            return 5;
        }

        $token = $TokensTables->find("all")->contain(["players", "players.Clans"])->where(['user_id' => $account->id]);
        if ($token->count()) {
            return 3;
        }
        // By default deny access.
        return 0;
    }

    /**
     * @return bool
     */
    public function isClan(): bool
    {
        return $this->clan;
    }

    /**
     * @return int
     */
    public function getPermissionLevel(): int
    {
        return $this->PermissionLevel;
    }
}
