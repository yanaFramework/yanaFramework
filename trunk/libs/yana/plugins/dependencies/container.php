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
 */

namespace Yana\Plugins\Dependencies;

/**
 * Dependency ccntainer.
 *
 * Used to inject class dependencies into plugins.
 *
 * @package     yana
 * @subpackage  plugins
 */
class Container extends \Yana\Core\Object implements \Yana\Plugins\Dependencies\IsContainer
{

    /**
     * @var  \Yana\Security\Sessions\IsWrapper
     */
    private $_session = null;

    /**
     * @var  array
     */
    private $_defaultEvent = array();

    /**
     * 
     * @param  \Yana\Security\Sessions\IsWrapper  $session       bound to current environment parameters
     * @param  array                              $defaultEvent  tells us what to do as fallback and comes from application configuration file
     */
    public function __construct(\Yana\Security\Sessions\IsWrapper $session, array $defaultEvent)
    {
        $this->_session = $session;
        $this->_defaultEvent = $defaultEvent;
    }

    /**
     * Get default event settings.
     *
     * @return  array
     */
    public function getDefaultEvent()
    {
        return $this->_defaultEvent;
    }

    /**
     * Get session data.
     *
     * @return  \Yana\Security\Sessions\IsWrapper
     */
    public function getSession()
    {
        return $this->_session;
    }

}

?>