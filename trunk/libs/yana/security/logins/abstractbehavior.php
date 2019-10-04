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
 * <<abstract>> Login behavior.
 *
 * To handle logins and logouts of users by adjusting the session settings and cookies that go with them.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
abstract class AbstractBehavior extends \Yana\Core\StdObject implements \Yana\Security\Logins\IsBehavior
{

    /**
     * @var  \Yana\Security\Sessions\IsWrapper
     */
    private $_session = null;

    /**
     * @var  \Yana\Security\Sessions\IsIdGenerator
     */
    private $_sessionIdGenerator = null;

    /**
     * Create new instance.
     *
     * @param  \Yana\Security\Sessions\IsWrapper      $session    some session wrapper
     * @param  \Yana\Security\Sessions\IsIdGenerator  $generator  provide your own only when doing unit-tests
     */
    public function __construct(\Yana\Security\Sessions\IsWrapper $session = null, \Yana\Security\Sessions\IsIdGenerator $generator = null)
    {
        $this->_session = $session;
        $this->_sessionIdGenerator = $generator;
    }

    /**
     * Returns a session wrapper.
     *
     * @return  \Yana\Security\Sessions\IsWrapper
     */
    protected function _getSession()
    {
        if (!isset($this->_session)) {
            // @codeCoverageIgnoreStart
            $this->_session = new \Yana\Security\Sessions\Wrapper();
            // @codeCoverageIgnoreEnd
        }
        return $this->_session;
    }

    /**
     * Returns class to generate new session ids.
     *
     * @return  \Yana\Security\Sessions\IsIdGenerator
     */
    protected function _getSessionIdGenerator()
    {
        if (!isset($this->_sessionIdGenerator)) {
            $this->_sessionIdGenerator = new \Yana\Security\Sessions\IdGenerator();
        }
        return $this->_sessionIdGenerator;
    }

}

?>