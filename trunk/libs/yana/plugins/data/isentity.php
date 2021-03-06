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

namespace Yana\Plugins\Data;

/**
 * <<interface>> Plugin entity.
 *
 * Holds user data and function to set logins and passwords.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
interface IsEntity extends \Yana\Data\Adapters\IsEntity
{

    /**
     * Get the alphanumeric name of the plugin as a string.
     *
     * @return  string
     */
    public function getId();

    /**
     * Set the plugin's name.
     *
     * @param  string  $pluginName  alphanumeric plugin id
     * @return  \Yana\Data\Adapters\IsEntity
     */
    public function setId($pluginName);

    /**
     * Activate/Deactivate.
     *
     * Set to bool(true) if the plugin should be active or to bool(false) if the plugin should be deactivated.
     * Note that some plugins cannot be deactivated. In this case, this setting will have no effect.
     *
     * @param   bool  $isActive  use expert settings (yes/no)
     * @return  $this
     */
    public function setActive($isActive);

    /**
     * Plugin is active.
     *
     * Returns bool(true) if the plugin is activated and bool(false) otherwise.
     * Note that some plugins cannot be deactivated. In this case, this setting will have no effect.
     *
     * @return  bool
     */
    public function isActive();

}

?>