<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use App\Logic\Helper\RightsHelper;
use App\Model\Entity\User;
use Authentication\Controller\Component\AuthenticationComponent;
use Authorization\Controller\Component\AuthorizationComponent;
use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 * @property AuthorizationComponent $Authorization
 * @property AuthenticationComponent $Authentication
 * @link https://book.cakephp.org/3/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    /** @var null|int $permissionLevel */
    protected $permissionLevel = null;
    /** @var null|User $LoggedInUsers */
    protected $LoggedInUsers = null;
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize() :void
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');

        /*
         * Enable the following component for recommended CakePHP form protection settings.
         * see https://book.cakephp.org/4/en/controllers/components/form-protection.html
         */
        //$this->loadComponent('FormProtection');
        $this->loadComponent('Authentication.Authentication');
        $this->loadComponent('Authorization.Authorization');

        $DESIGN_User = false;
        if ($this->Authentication->getIdentity()?->getIdentifier()) {
            $DESIGN_Ident = $this->Authentication->getIdentity();
            $UsersTables = TableRegistry::getTableLocator()->get('Users');
            $DESIGN_User = $UsersTables->get($DESIGN_Ident->getIdentifier());

            $rh = new RightsHelper($DESIGN_Ident->getIdentifier());
            $this->LoggedInUsers = $DESIGN_User;

        }

        $this->set("user", $DESIGN_Ident);
        $this->set("rights", $rh??null);
        $this->set("auth",$this->LoggedInUsers);




    }
    public function isAuthorized($user)
    {

        if($this->permissionLevel >= 10) {
            return true;
        }
        return false;
    }
}
