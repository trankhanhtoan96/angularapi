<?php
/*********************************************************************************
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2013 SugarCRM Inc.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
 * details.
 *
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 *
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by SugarCRM".
 ********************************************************************************/

namespace SpiceCRM\includes\authentication;

use http\Exception\UnexpectedValueException;
use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\authentication\IpAddresses\IpAddresses;
use SpiceCRM\includes\authentication\SpiceCRMAuthenticate\SpiceCRMAuthenticate;
use SpiceCRM\includes\authentication\TOTPAuthentication\TOTPAuthentication;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\ErrorHandlers\Exception;
use SpiceCRM\includes\ErrorHandlers\ForbiddenException;
use SpiceCRM\includes\ErrorHandlers\UnauthorizedException;
use SpiceCRM\includes\Logger\LoggerManager;
use SpiceCRM\includes\LogicHook\LogicHook;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\includes\TimeDate;
use SpiceCRM\modules\Administration\Administration;
use SpiceCRM\modules\Contacts\Contact;
use SpiceCRM\modules\SystemTenants\SystemTenant;
use SpiceCRM\modules\UserAccessLogs\UserAccessLog;
use SpiceCRM\modules\Users\User;
use SpiceCRM\includes\authentication\UserAuthenticate\UserAuthenticate;
use SpiceCRM\includes\authentication\LDAPAuthenticate\LDAPAuthenticate;
use SpiceCRM\includes\authentication\TOTPAuthentication\TwoFactorAuthenticate;

class AuthenticationController
{


    /**
     * Stores the current token.
     * @var string|null
     */
    public $token = null;

    /**
     * Stores the User object of the user that is currently logged in.
     * @var User|null
     */
    private $currentUser = null;
    protected static $authControllerInstance = null;
    /**
     * @var UserAuthenticate
     */
    public $authController;

    /**
     * the systemtenant id if we are in a tenant
     *
     * @var null
     */
    public $systemtenantid = null;

    /**
     * the name of the tenant
     *
     * @var null
     */
    public $systemtenantname = null;
    public $errorReason;
    public $errorCode;

    /**
     * The Singleton's constructor should always be private to prevent direct
     * construction calls with the `new` operator.
     */
    protected function __construct()
    {
    }

    public function getCanChangePassword()
    {
        if (LDAPAuthenticate::isLdapEnabled()) {
            return false;
        }
        if ($this->currentUser && in_array($this->currentUser->external_auth_only, [0, "0"])) {
            return true;
        }
        return false;

    }

    /**
     * Singletons should not be restorable from strings.
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    /**
     * Getter for the current User object.
     *
     * @return User
     */
    public function getCurrentUser()
    {
        return $this->currentUser;
    }

    public function isAuthenticated()
    {
        return $this->currentUser instanceof User;
    }

    public function isAdmin()
    {
        return $this->currentUser instanceof User && $this->currentUser->isAdmin();
    }

    /**
     * Setter for the current User object.
     *
     * @param User $userBean
     * @return bool
     */
    public function setCurrentUser(User $userBean)
    { //todo shouldnt we set currentuser on usersingleton?
        $this->currentUser = $userBean;
        return true;
    }


    /**
     * @param $username
     * @param $currentPwd
     * @param $newPwd
     * @param false $sendByEmail
     * @return bool
     * @throws Exception
     * @throws ForbiddenException
     * @throws UnauthorizedException
     */
    public function changePassword($username, $currentPwd, $newPwd, $sendByEmail = false)
    {
        try {
            $userAuthenticateObj = new UserAuthenticate();
            $userObj = $userAuthenticateObj->authenticate($username, $currentPwd);
            $this->setCurrentUser($userObj);
        } catch (UnauthorizedException $e) {
            //convert error message
            throw new UnauthorizedException("Current Password is not correct");
        }

        if ($this->getCanChangePassword() === false) {
            throw new ForbiddenException("Password Change not allowed");
        }

        return $userAuthenticateObj->setNewPassword($userObj, $newPwd, $sendByEmail, false);

    }

    /**
     * @param string|null $username
     * @param string|null $password
     * @param null $token
     * @param null $tokenIssuer
     * @return array
     * @throws UnauthorizedException
     */
    public function authenticate($username = null, $password = null, $token = null, $tokenIssuer = null, $impersonationUser = null, $apikey = null, $auth3rd = null)
    {
//        $config = SpiceConfig::getInstance()->config;
        try {
            if (!empty($_GET['apikey'])) {
                $userObj = new User();
                $userObj->retrieve_by_string_fields([
                    'apikey' => $_GET['apikey']
                ]);
                if (empty($userObj->id)) {
                    $u = base64_decode($_GET['apikey']);
                    $u = explode(':', $u);
                    if (count($u) == 2) {
                        $userObj = $this->handleUserPassAuth($u[0], $u[1]);
                    } else {
                        throw new UnauthorizedException("Apikey not found");
                    }
                }
            } elseif ($token) {
                $userObj = $this->handleTokenAuth($token, $tokenIssuer);
//                if ( !IpAddresses::checkIpAddress() ) {
//                    if ( !User::isAdmin_byName( $userObj->user_name )) # don´t block the admin
//                        throw ( new UnauthorizedException('No access from this IP address. Contact the admin.', 11))->setIPblocked( true );
//                }
            } elseif ($username && $password) {
//                if ( !IpAddresses::checkIpAddress() ) {
//                    if ( !User::isAdmin_byName( $username )) # don´t block the admin
//                        throw ( new UnauthorizedException('No access from this IP address. Contact the admin.', 11))->setIPblocked( true );
//                }
                $userObj = $this->handleUserPassAuth($username, $password, $impersonationUser);
            } elseif ($apikey) {
                $userObj = new User();
                $userObj->retrieve_by_string_fields([
                    'apikey' => $apikey
                ]);
                if (empty($userObj->id)) {
                    $u = base64_decode($apikey);
                    $u = explode(':', $u);
                    if (count($u) == 2) {
                        $userObj = $this->handleUserPassAuth($u[0], $u[1], $impersonationUser);
                    } else {
                        throw new UnauthorizedException("Apikey not found");
                    }
                }
            } elseif ($auth3rd){
                /***********
                 * GOOGLE *
                 ***********/
                $userObj = new User();
                $userObj->retrieve_by_string_fields(['user_name' => $auth3rd['email']]);
                if (empty($userObj->id)){
                    //Register new user
                    $userObj->user_name = $auth3rd['email'];
                    $userObj->user_image = $auth3rd['photoUrl'];
                    $userObj->first_name = $auth3rd['firstName'];
                    $userObj->last_name = $auth3rd['lastName'];
                    $userObj->authenticate_id = $auth3rd['id'];
                    $userObj->t_role = 'subcriber';
                    $userObj->apikey = md5(time());
                    $userObj->status = 'Active';
                    $userObj->sugar_login = 1;
                    $userObj->created_by = '1';
                    $userObj->modified_user_id = '1';
                    $userObj->login_blocked = 0;
                    $userObj->is_admin = 1;
                    $userObj->t_email = $auth3rd['email'];
                    $userObj->save();
                }
            } else {
                throw new UnauthorizedException("Invalid authentication method", 6);
            }

            //check status field on user
            $this->checkUserStatus($userObj);

            $this->setCurrentUser($userObj);

//            if (($username && $password) || $tokenIssuer !== 'SpiceCRM') { //login was via user/pass therefore create session and log login
                $this->token = SpiceCRMAuthenticate::createSession($this->currentUser);

//                //should we log successful login?
//                if (array_key_exists("logSuccessLogin", $config)) {
//                    $this->logSuccessLogin($userObj);
//                }

//            }

//            $this->handleTenants();

        } catch (UnauthorizedException $e) {
            /** @var UserAccessLog $userAccessLogObj */

            # isUserBlocked() in case the login or password check has not happened, because the user is already blocked (temporary or permanent).
            # Otherwise the check has happened and failed, so log the failed attempt:
//            if (!$e->isUserBlocked() and !empty($username)) {
//                $userAccessLogObj = BeanFactory::getBean('UserAccessLogs');
//                $userAccessLogObj->addRecord("loginfail", empty($impersonationUser) ? $username : $impersonationUser . '#as#' . $username);
//                unset($userAccessLogObj);
//                if ($config['login_attempt_restriction']['user_enabled']) {
//                    $amountFailedLogins = UserAccessLog::getAmountFailedLoginsWithinByUsername($username, $config['login_attempt_restriction']['user_monitored_period']);
//                    if ($amountFailedLogins >= $config['login_attempt_restriction']['user_number_attempts']) {
//                        User::blockUserByName($username, $config['login_attempt_restriction']['user_blocking_duration']);
//                    }
//                }
//            }

            # In case the max. failed login attempts are reached, black list the IP address.
            # ( But only if IP restriction is enabled and the IP address is not white listed and the IP address has not been black listed just before (isIPblocked). )
//            if ($config['login_attempt_restriction']['ip_enabled']
//                and UserAccessLog::getNumberLoginAttemptsByIp() >= (int)$config['login_attempt_restriction']['ip_number_attempts']
//                and !IpAddresses::ipAddressIsWhite()
//                and !$e->isIPblocked()) {
//                IpAddresses::addIpAddress('b');
//                $e->setIPblocked(true);
//            };

            $this->errorReason = $e->getMessage();
            $this->errorCode = $e->getErrorCode();

            throw new UnauthorizedException($e->getMessage(), $e->getErrorCode());
        }

        return [ //todo find a solution to specify return format
            'token' => $this->token,
            'user' => $userObj,
            'pwChangeEnabled' => LDAPAuthenticate::isLdapEnabled() === false && $userObj->external_auth_only === false && TOTPAuthentication::checkTOTPActive($userObj->id) === false
        ];

    }

    private function handleTokenAuth($token, $tokenIssuer)
    {
        $authenticationClass = "SpiceCRM\includes\authentication\\{$tokenIssuer}Authenticate\\{$tokenIssuer}Authenticate";
        if (class_exists($authenticationClass, true)) {
            $authenticationController = new $authenticationClass();
            return $authenticationController->authenticate($token);
        } else {
            throw new \Exception("AuthenticationClass {$authenticationClass} not found");
        }
    }

    private function logSuccessfulLogin(User $userObj)
    {
        $userAccessLogObj = BeanFactory::getBean('UserAccessLogs');
        if (!$userAccessLogObj->addRecord("loginsuccess", $userObj->user_name)) {
            unset($userAccessLogObj);
            throw new \Exception("Unable to log successful login");
        }
        unset($userAccessLogObj);
        return true;
    }

    private function checkUserStatus(User $userObj)
    {
        switch ($userObj->status) {
            case "Active":
                break;
            case "Inactive":
                throw new UnauthorizedException("User is inactive", 4);
            default:
                throw new UnauthorizedException("User Status is unknown", 5);
        }
        return true;
    }

    /**
     * Handles the authentication with username/password credentials.
     * @return User | false
     */
    private function handleUserPassAuth($authUser, $authPass, $impersonationUser = null)
    {
        //first check if ldap authentication is enabled
        if (LDAPAuthenticate::isLdapEnabled()) {
            /** @var LDAPAuthenticate $ldapController */
            $ldapController = new LDAPAuthenticate();
            try {
                if ($userObj = $ldapController->authenticate($authUser, $authPass)) {

                    return $userObj;
                }
            } catch (UnauthorizedException|\Exception $e) {
                $ldapError = $e;
            }
        }

        //second use sugar authentication
        try {

            # Check if the user is blocked (after too many login attempts with wrong passwords).
            # This check must happen BEFORE checking the password. No password check (and answer to the user) in case the user is blocked!
            $isBlocked = User::isBlocked(isset($impersonationUser) ? $impersonationUser : $authUser);
            if ($isBlocked === true) {
                throw (new UnauthorizedException('User is blocked. Contact the admin for access.', 3))->setUserBlocked(true);
            } elseif ($isBlocked !== false) {
                throw (new UnauthorizedException('User is blocked temporary. Access again in ' . $isBlocked . ' Minutes.', 3))->setUserBlocked(true);
            }

            $sugarAuthenticationController = new UserAuthenticate();
            $userObj = $sugarAuthenticationController->authenticate($authUser, $authPass, $impersonationUser);

            // check if password is expired
            if (($userObj->system_generated_password or $userObj->hasExpiredPassword()) and !$userObj->is_api_user) {
                throw new UnauthorizedException('Password expired.', 2);
            }

        } catch (UnauthorizedException $e) {
            throw (new UnauthorizedException($ldapError ? $ldapError->getMessage() : $e->getMessage(), $ldapError ? $ldapError->getErrorCode() : $e->getErrorCode()))->setUserBlocked($e->isUserBlocked());
        }
        return $userObj;
    }


    private function handleTenants()
    {
        /* switch to a different tenant if the tenant id is set for the user */
        if (!empty($this->getCurrentUser()->systemtenant_id)) {
            $tenant = new SystemTenant();
            $tenant->retrieve($this->getCurrentUser()->systemtenant_id);
            if ($tenant->valid_until < TimeDate::getInstance()->nowDbDate()) {
                throw new UnauthorizedException('Tenant expired', 401);
            }
            $tenant->switchToTenant();
            $this->systemtenantid = $tenant->id;
            $this->systemtenantname = $tenant->name;
        }
    }

    /**
     * Returns an array with basic data about the current user.
     *
     * @return array
     */
    public function getLoginData()
    {
        $authenticationController = AuthenticationController::getInstance();
        if ($authenticationController->getCurrentUser() === null) {
            throw new UnauthorizedException($authenticationController->errorReason, $authenticationController->errorCode);
        }
        $currentUser = $this->getCurrentUser();
        if (empty($currentUser->apikey)) $currentUser->apikey = md5(time());
        $currentUser->save();

        $loginData = [
            'admin' => $currentUser->is_admin == '1' ? true : false,
            'email' => $currentUser->email1,
            'first_name' => $currentUser->first_name,
            'id' => session_id(),
            'last_name' => $currentUser->last_name,
            'user_name' => $currentUser->user_name,
            'userid' => $currentUser->id,
            'user_image' => $currentUser->user_image,
            'apikey' => $currentUser->apikey,
            't_role'=>$currentUser->t_role
        ];

        return $loginData;
    }

    /**
     * Returns an instance of the authentication controller
     *
     * @param string $type this is the type of authentication you want to use default is SugarAuthenticate
     * @return AuthenticationController
     */
    public static function getInstance()
    {
        if (empty(self::$authControllerInstance)) {
            self::$authControllerInstance = new static();
        }
        return self::$authControllerInstance;
    }


    /**
     * Deletes the session if it was created without login.
     */
    public function cleanup()
    {
        if (!empty($this->tmpSessionId)) {
            session_destroy();
        }
    }


    /**
     * Called when a user requests to logout. Should invalidate the session and redirect
     * to the login page.
     */
    public function logout()
    {
        $this->getCurrentUser()->call_custom_logic('before_logout');
        $this->authController->logout();
        LogicHook::getInstance()->call_custom_logic('Users', 'after_logout');
    }

}
