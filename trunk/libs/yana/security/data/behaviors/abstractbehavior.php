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

namespace Yana\Security\Data\Behaviors;

/**
 * <<abstract>> User behavior facade.
 *
 * Holds user data and function to set logins and passwords.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
abstract class AbstractBehavior extends \Yana\Core\StdObject implements \Yana\Security\Data\Behaviors\IsBehavior
{

    /**
     * User entity.
     *
     * @var  \Yana\Security\Data\Users\IsEntity
     */
    private $_entity = null;

    /**
     * Dependency container.
     *
     * @var  \Yana\Security\Dependencies\IsContainer
     */
    private $_container = null;

    /**
     * Count boundary.
     *
     * Maximum number of times a user may enter
     * a wrong password before its account
     * is suspended for $maxFailureTime seconds.
     *
     * @var  int
     */
    private $_maxFailureCount = 3;

    /**
     * Time boundary.
     *
     * Maximum time in seconds a user's login
     * is blocked after entering a wrong password
     * $maxFailureCount times.
     *
     * E.g. 300 sec. = 5 minutes.
     *
     * @var  int
     */
    private $_maxFailureTime = 300;

    /**
     * Creates an user by name.
     *
     * @param  \Yana\Security\Dependencies\IsContainer  $container        dependency container
     * @param  \Yana\Security\Data\Users\IsEntity       $user             entity to wrap
     * @param   int                                     $maxFailureCount  1 = block on first invalid password, 0 = never block user
     * @param   int                                     $maxFailureTime   in seconds (0 = keep blocked forever)
     */
    public function __construct(\Yana\Security\Dependencies\IsContainer $container, \Yana\Security\Data\Users\IsEntity $user, $maxFailureCount = 3, $maxFailureTime = 300)
    {
        $this->_entity = $user;
        $this->_container = $container;
        $this->_setMaxFailureCount($maxFailureCount);
        $this->_setMaxFailureTime($maxFailureTime);
    }

    /**
     * Returns User entity.
     *
     * @return  \Yana\Security\Data\Users\IsEntity
     */
    protected function _getEntity()
    {
        return $this->_entity;
    }

    /**
     * Returns dependency containe.
     *
     * @return  \Yana\Security\Dependencies\IsContainer
     */
    protected function _getDependencies()
    {
        return $this->_container;
    }

    /**
     * Get count boundary.
     *
     * Maximum number of times a user may enter a wrong password before its account is suspended for x seconds.
     *
     * @return  int
     */
    protected function _getMaxFailureCount()
    {
        return (int) $this->_maxFailureCount;
    }

    /**
     * Get time boundary.
     *
     * Maximum time in seconds a user's login is blocked after entering a wrong password x times.
     *
     * @return  int
     */
    protected function _getMaxFailureTime()
    {
        return (int) $this->_maxFailureTime;
    }

    /**
     * Set count boundary.
     *
     * Maximum number of times a user may enter a wrong password before its account is suspended for x seconds.
     *
     * @param   int  $maxFailureCount  1 = block on first invalid password, 0 = never block user
     * @return  \Yana\Security\Facade
     */
    private function _setMaxFailureCount($maxFailureCount)
    {
        assert(is_int($maxFailureCount), 'Invalid argument $maxFailureCount: integer expected');
        assert($maxFailureCount >= 0, 'Invalid argument $maxFailureCount: must not be negative');
        $this->_maxFailureCount = (int) $maxFailureCount;
        return $this;
    }

    /**
     * Set time boundary.
     *
     * Maximum time in seconds a user's login is blocked after entering a wrong password x times.
     *
     * @param   int  $maxFailureTime  in seconds (0 = keep blocked forever)
     * @return  \Yana\Security\Facade
     */
    private function _setMaxFailureTime($maxFailureTime)
    {
        assert(is_int($maxFailureTime), 'Invalid argument $maxFailureTime: integer expected');
        assert($maxFailureTime >= 0, 'Invalid argument $maxFailureTime: must not be negative');
        $this->_maxFailureTime = (int) $maxFailureTime;
        return $this;
    }

}

?>