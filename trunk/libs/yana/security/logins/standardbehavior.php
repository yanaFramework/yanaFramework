<?php
/**
 * YANA library
 *
 * Software:  Yana PHP-Framework
 * Version:   {VERSION} - {DATE}
 * License:   GNU GPL  http://www.gnu.org/licenses/
 *
 * This program: can be redistributed and/or modified under the
 * terms of the GNU General Public License as published by the
 * Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see http://www.gnu.org/licenses/.
 *
 * This notice MAY NOT be removed.
 *
 * @package  yana
 * @license  http://www.gnu.org/licenses/gpl.txt
 *
 * @ignore
 */

namespace Yana\Security\Logins;

/**
 * Login manager.
 *
 * To handle logins and logouts of users by adjusting the session settings and cookies that go with them.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class StandardBehavior extends \Yana\Security\Logins\AbstractBehavior
{

    /**
     * check if user is logged in
     *
     * Returns bool(true) if the user is currently
     * logged in and bool(false) otherwise.
     *
     * @internal  Note on security:
     * This framework introduces SHA-1 encoded session-ids only to logged-in users and instead
     * provides md5 encoded ids to others.
     * SHA-1 produces a 20 bytes long string (a 40 digits hexadecimal number).
     * MD5 encoded ids are only 16 bytes (a 32 digits hexadecimal number).
     * Thus: if a session-id is shorter than 20 bytes (40 digits) this is an obvious hint that
     * either the user has not logged-in, or the session id is not valid.
     *
     * @param   \Yana\Security\Data\Users\IsEntity  $user  entity
     * @return  bool
     */
    public function isLoggedIn(\Yana\Security\Data\Users\IsEntity $user)
    {
        assert('!isset($session); // Cannot redeclare var $session');
        $session = $this->_getSession();
        assert('!isset($isLoggedIn); // Cannot redeclare var $isLoggedIn');
        $isLoggedIn = true;
        switch (true)
        {
            case $user->getId() == "":
            case function_exists('sha1') && strlen($session->getId()) < 20:
            case !$this->_getSession()->getApplicationUserId():
            case !$this->_getSession()->getCurrentUserName():
            case !$this->_getSession()->getSessionUserId():
            case $this->_getSession()->getApplicationUserId() !== $this->_getSessionIdGenerator()->createApplicationUserId():
            case $this->_getSession()->getCurrentUserName() !== $user->getId():
            case $this->_getSession()->getSessionUserId() !== $user->getSessionCheckSum():
                $isLoggedIn = false;
        }
        return $isLoggedIn;
    }

    /**
     * Handle user logins.
     *
     * It destroys any previous session (to prevent session fixation).
     * Creates new session id and updates the user's session information in the database.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @param   \Yana\Security\Data\Users\IsEntity  $user  entity
     * @return  self
     * @throws  \Yana\Core\Exceptions\Security\InvalidLoginException  when access is denied
     */
    public function handleLogin(\Yana\Security\Data\Users\IsEntity $user)
    {
        assert('!isset($session); // Cannot redeclare var $session');
        $session = $this->_getSession();

        if (!$user->isActive()) {
            throw new \Yana\Core\Exceptions\Security\InvalidLoginException();
        }
        /* never reuse old sessions, to prevent injection of data or session id */
        $this->handleLogout($user);

        /* create new session with new session id */
        $sessionId = $this->_getSessionIdGenerator()->createAuthenticatedSessionId();
        $session->setId($sessionId)->start(); // overwrites the session id
        $session->unsetAll();

        /* initiate session and user database entry */
        $this
            ->_setupSessionDataOnLogin($session, $user)
            ->_setupSessionUserId($session)
            ->_updateUserDataOnLogin($user, $session->getSessionUserId());

        return $this;
    }

    /**
     * Initializes session user id.
     *
     * @param   \Yana\Security\Sessions\IsWrapper   $session  some session wrapper
     * @return  \Yana\Security\Logins\StandardBehavior
     */
    private function _setupSessionUserId(\Yana\Security\Sessions\IsWrapper $session)
    {
        $sessionUserId = md5($session->getId());
        // save a copy to check validity of session against data-source later
        $session->setSessionUserId($sessionUserId);

        return $this;
    }

    /**
     * Initializes session values like prefered language.
     *
     * @param   \Yana\Security\Sessions\IsWrapper   $session  some session wrapper
     * @param   \Yana\Security\Data\Users\IsEntity  $user     which is to be logged in
     * @return  \Yana\Security\Logins\StandardBehavior
     */
    private function _setupSessionDataOnLogin(\Yana\Security\Sessions\IsWrapper $session, \Yana\Security\Data\Users\IsEntity $user)
    {
        $session->setCurrentUserName($user);
        $session->setApplicationUserId($this->_getSessionIdGenerator()->createApplicationUserId());

        // initialize language settings
        if ($user->getLanguage() > '') {
            try {

                \Yana\Translations\Facade::getInstance()->setLocale($user->getLanguage());
                $session->setCurrentLanguage($user->getLanguage());

            } catch (\Yana\Core\Exceptions\InvalidArgumentException $e) {
                unset($e); // the user's prefered language isn't installed (anymore)
            }
        } // end if

        return $this;
    }

    /**
     * Updates user entity with login time and login count.
     *
     * @param   \Yana\Security\Data\Users\IsEntity  $user  which is to be logged in
     * @return  \Yana\Security\Logins\StandardBehavior
     */
    private function _updateUserDataOnLogin(\Yana\Security\Data\Users\IsEntity $user)
    {
        $user
            // mark user as logged-in in database
            ->setSessionCheckSum($this->_getSession()->getSessionUserId())
            // set time of last login to current timestamp
            ->setLoginTime(time())
            // increment login count
            ->setLoginCount($user->getLoginCount() + 1)
            // save changes
            ->saveEntity();
        return $this;
    }

    /**
     * Destroy the current session and clear all session data.
     *
     * @param   \Yana\Security\Data\Users\IsEntity  $user  entity
     * @return  self
     */
    public function handleLogout(\Yana\Security\Data\Users\IsEntity $user)
    {
        assert('!isset($session); // Cannot redeclare var $session');
        $session = $this->_getSession();
        // backup language setting before destroying old session
        if ($session->getCurrentLanguage() > "") {
            $user->setLanguage($session->getCurrentLanguage());
        }
        // make session cookie expire (get's deleted)
        if (\filter_has_var(\INPUT_COOKIE, $session->getName())) {
            $params = $session->getCookieParameters();
            setcookie($session->getName(), '', time() - 42000, $params["path"],
                $params["domain"], $params["secure"], $params["httponly"]
            );
        }
        // unset session data
        $session->unsetAll();
        // kill session
        @$session->destroy();
        // get rid of the old sesion id - just in case
        @$session->regenerateId();

        // reset session-checksum to mark user as logged-out in database
        $user->setSessionCheckSum("")->saveEntity();
        // Note: the session data has already been purged,
        // so we don't need to reset $session['user_session'] at this point.
        return $this;
    }

}

?>