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

namespace Yana\Security\Users;

/**
 * User manager.
 *
 * This persistent class provides access to user data and function to set logins and passwords.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class LoginManager extends \Yana\Core\Object
{

    /**
     * @var  \Yana\Core\Sessions\IsWrapper
     */
    private $_session = null;

    /**
     * @var  \Yana\Security\SessionIds\IsGenerator
     */
    private $_sessionIdGenerator = null;

    /**
     * Create new instance.
     *
     * @param  \Yana\Core\Sessions\IsWrapper          $session    some session wrapper
     * @param  \Yana\Security\SessionIds\IsGenerator  $generator  provide your own only when doing unit-tests
     */
    public function __construct(\Yana\Core\Sessions\IsWrapper $session, \Yana\Security\SessionIds\IsGenerator $generator = null)
    {
        if (!isset($generator)) {
            $generator = new \Yana\Security\SessionIds\Generator();
        }
        $this->_session = $session;
        $this->_sessionIdGenerator = $generator;
    }

    /**
     * Returns a session wrapper.
     *
     * @return  \Yana\Core\Sessions\IsWrapper
     */
    protected function _getSession()
    {
        return $this->_session;
    }

    /**
     * Returns class to generate new session ids.
     *
     * @return  \Yana\Security\SessionIds\IsGenerator
     */
    protected function _getSessionIdGenerator()
    {
        return $this->_sessionIdGenerator;
    }

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
     * @return  bool
     */
    public function isLoggedIn(\Yana\Security\Users\IsUser $user)
    {
        assert('!isset($session); // Cannot redeclare var $session');
        $session = $this->_getSession();
        switch (true)
        {
            case $user->getId() == "":
            case function_exists('sha1') && strlen($session->getId()) < 20:
            case !$this->_getSession()->offsetExists('prog_id'):
            case !$this->_getSession()->offsetExists('user_name'):
            case !$this->_getSession()->offsetExists('user_session'):
            case $this->_getSession()->offsetGet('prog_id') !== self::getApplicationId():
            case $this->_getSession()->offsetGet('user_name') !== $user->getId():
            case $this->_getSession()->offsetGet('user_session') !== $user->getSessionCheckSum():
                return false;
        }
        return true;
    }

    /**
     * Handle user logins.
     *
     * It destroys any previous session (to prevent session fixation).
     * Creates new session id and updates the user's session information in the database.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @throws  \Yana\Core\Exceptions\Security\InvalidLoginException  when access is denied
     */
    public function login(\Yana\Security\Users\IsUser $user)
    {
        assert('!isset($session); // Cannot redeclare var $session');
        $session = $this->_getSession();

        if (!$user->isActive()) {
            throw new \Yana\Core\Exceptions\Security\InvalidLoginException();
        }
        /* never reuse old sessions, to prevent injection of data or session id */
        $this->logout();

        /* create new session with new session id */
        $sessionId = $this->_getSessionIdGenerator()->createAuthenticatedSessionId();
        $session
            ->setId($sessionId) // overwrites the session id
            ->start()
            ->unsetAll();

        $session['user_name'] = $user->getName();
        $session['prog_id'] = $this->_getSessionIdGenerator()->createApplicationUserId();
        $session['user_session'] = md5($session->getId());

        // initialize language settings
        if (!empty($this->_language)) {
            assert('!isset($languageManager); // Cannot redeclare var $languageManager');
            $languageManager = \Yana\Translations\Facade::getInstance();
            try {

                $languageManager->setLocale($this->_language);
                $session['language'] = $this->_language;

            } catch (\Yana\Core\Exceptions\InvalidArgumentException $e) {
                unset($e);
                // ignore
            }
            unset($languageManager);
        } // end if

        $user
            // set time of last login to current timestamp
            ->setLoginTime(time())
            // mark user as logged-in in database
            ->setSessionCheckSum($session['user_session'])
            // increment login count
            ->setLoginCount($user->getLoginCount() + 1)
            // save changes
            ->saveEntity();
    }

    /**
     * logout
     *
     * Destroy the current session and clear all session data.
     */
    public function logout()
    {
        assert('!isset($session); // Cannot redeclare var $session');
        $session = $this->_getSession();
        // backup language setting before destroying old session
        if (isset($session['language'])) {
            $this->setLanguage($session['language']);
        }
        // make session cookie expire (get's deleted)
        if (isset($_COOKIE[$session->getName()])) {
            setcookie($session->getName(), '', time() - 42000, '/');
        }
        // unset session data
        $session->unsetAll();
        // kill session
        @$session->destroy();
        // get rid of the old sesion id - just in case
        @$session->regeneratId();
        // mark user as logged-out in database
        $this->updates["USER_SESSION"] = "";
    }

}

?>