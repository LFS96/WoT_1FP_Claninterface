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
        return $this->rightsHelper->isAdmin;
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
        return $this->rightsHelper->isCommander;
    }
    /**
     * Check if $user can add User
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\User $resource
     * @return bool
     */
    public function canFieldCommander(IdentityInterface $user, User $resource)
    {
        $this->checkUser($resource);
        return $this->rightsHelper->isFieldCommander;
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
        return $this->rightsHelper->isPersonal;
    }
    /**
     * Check if $user can add User
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\User $resource
     * @return bool
     */
    public function canOfficer(IdentityInterface $user, User $resource)
    {
        $this->checkUser($resource);
        return $this->rightsHelper->isPersonal;
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
        return $this->rightsHelper->isMember;
    }
    private function checkUser(User $user){
        if($this->rightsHelper == null) {
            $rh = new RightsHelper($user);
            $this->rightsHelper = $rh;
        }
    }



}
