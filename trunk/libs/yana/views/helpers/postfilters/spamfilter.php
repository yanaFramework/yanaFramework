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

namespace Yana\Views\Helpers\PostFilters;

/**
 * Smarty-compatible HTML-processors
 *
 * This class is registered when instantiating the Smarty Engine.
 *
 * @package     yana
 * @subpackage  views
 */
class SpamFilter extends \Yana\Views\Helpers\AbstractViewHelper implements \Yana\Views\Helpers\IsPostFilter
{

    /**
     * @var  \Yana\Core\Sessions\IsWrapper
     */
    private $_session = null;

    /**
     * @var  \Yana\Security\Users\IsUser
     */
    private $_user = null;

    /**
     * Get active session wrapper.
     *
     * By default this is \Yana\Core\Sessions\Wrapper.
     *
     * @return  \Yana\Core\Sessions\IsWrapper
     */
    public function getSession()
    {
        if (!isset($this->_session)) {
            $this->_session = new \Yana\Core\Sessions\Wrapper();
        }
        return $this->_session;
    }

    /**
     * 
     * @param   \Yana\Core\Sessions\IsWrapper  $session  holds session data
     * @return  \Yana\Views\Helpers\PostFilters\SpamFilter
     */
    public function setSession(\Yana\Core\Sessions\IsWrapper $session)
    {
        $this->_session = $session;
        return $this;
    }

    /**
     * Get user entity.
     *
     * By defaults looks up the currently used user from the session.
     * Returns a GuestUser if none was found.
     *
     * @return  \Yana\Security\Users\IsUser
     */
    public function getUser()
    {
        if (!isset($this->_user)) {
            $userManager = new \Yana\Security\Users\UserBuilder();
            $this->_user = $userManager->buildFromSession();
        }
        return $this->_user;
    }

    /**
     * Provide user information.
     *
     * @param   \Yana\Security\Users\IsUser  $user  containing the current users active-state
     * @return   \Yana\Views\Helpers\PostFilters\SpamFilter
     */
    public function setUser(\Yana\Security\Users\IsUser $user)
    {
        $this->_user = $user;
        return $this;
    }

    /**
     * <<smarty processor>> htmlPostProcessor
     *
     * Adds an invisible dummy-field (honey-pot) to forms for spam protection.
     * If it's filled, it's a bot.
     *
     * @param   string  $source  HTML with PHP source code
     * @return  string
     */
    public function __invoke($source)
    {
        assert('is_string($source); // Wrong type for argument 1. String expected');

        if (!$this->_isUserLoggedIn()) {
            $replace = "<span class=\"yana_button\"><input type=\"text\" name=\"yana_url\"/></span>\n</form>";
            $source = str_replace("</form>", $replace, $source);
        }

        return $source;
    }

    /**
     * Checks if the user is currently logged in.
     *
     * Returns bool(true) on "yes" and bool(false) otherwise.
     *
     * @return  bool
     */
    protected function _isUserLoggedIn()
    {
        $loginManager = new \Yana\Security\Users\Logins\Manager();
        $user = $this->getUser();
        return $loginManager->isLoggedIn($user);
    }

}

?>