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

namespace Yana\Security\Data\SecurityLevels;

/**
 * Security level.
 *
 * Readonly information about the user's security level.
 *
 * @package     yana
 * @subpackage  security
 */
class Level extends \Yana\Core\Object
{

    /**
     * @var int
     */
    private $_securityLevel = 0;

    /**
     * @var bool
     */
    private $_userProxyActive = true;

    /**
     * Initalize properties.
     *
     * @param  int   $level    integer between 0 and 100
     * @param  bool  $isProxy  is proxy for another user
     */
    public function __construct($level, $isProxy)
    {
        assert('is_int($level); // Wrong type for argument $level. Integer expected');
        assert('$level >= 0 && $level <= 100; // Invalid argument $level. Must be between 0 and 100');
        assert('is_bool($isProxy); // Wrong type for argument $isProxy. Boolean expected');
        $this->_securityLevel = (int) $level;
        $this->_userProxyActive = (bool) $isProxy;
    }

    /**
     * Get granted security level between 0 and 100.
     *
     * @return  int
     */
    public function getSecurityLevel()
    {
        return $this->_securityLevel;
    }

    /**
     * Check proxy settings.
     *
     * Returns bool(true) if this user should be allowed to forward this security setting
     * to another user named to act as a temporary proxy and bool(false) otherwise.
     *
     * Note: this is just a setting. The actual proxy implementation needs to be done by plugins.
     *
     * @return  bool
     */
    public function isUserProxyActive()
    {
        return $this->_userProxyActive;
    }

}

?>