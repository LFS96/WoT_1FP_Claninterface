<?php
declare(strict_types=1);

namespace App\Policy;

use App\Logic\Helper\RightsHelper;
use App\Model\Entity\User;
use Authorization\IdentityInterface;

/**
 * User policy
 */
class UserPolicy
{
    /**
     * @var null|RightsHelper $rightsHelper
     */
    private $rightsHelper = null;

    /**
     * Check if $user can add User
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\User $resource
     * @return bool
     */
    public function canAdmin(IdentityInterface $user, User $resource)
    {
        $this->checkUser($resource);
        return $this->rightsHelper->getPermissionLevel() >= 10;
    }
    /**
     * Check if $user can add User
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\User $resource
     * @return bool
     */
    public function canCommander(IdentityInterface $user, User $resource)
    {
        $this->checkUser($resource);
        return $this->rightsHelper->getPermissionLevel() >= 8;
    }
    /**
     * Check if $user can add User
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\User $resource
     * @return bool
     */
    public function canFieldComander(IdentityInterface $user, User $resource)
    {
        $this->checkUser($resource);
        return $this->rightsHelper->getPermissionLevel() == 4 ||  $this->canCommander($user, $resource);
    }
    /**
     * Check if $user can add User
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\User $resource
     * @return bool
     */
    public function canPersonal(IdentityInterface $user, User $resource)
    {
        $this->checkUser($resource);
        return $this->rightsHelper->getPermissionLevel() == 5 ||  $this->canCommander($user, $resource);
    }
    /**
     * Check if $user can add User
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\User $resource
     * @return bool
     */
    public function canMember(IdentityInterface $user, User $resource)
    {
        $this->checkUser($resource);
        return $this->rightsHelper->getPermissionLevel() >= 3;
    }
    private function checkUser(User $user){
        if($this->rightsHelper == null) {
            $rh = new RightsHelper($user);
            $this->rightsHelper = $rh;
        }
    }



}
