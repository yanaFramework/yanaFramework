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

namespace Yana\Plugins\Menus;

/**
 * <<interface>> Dependency container.
 *
 * To collect all the dependencies needed for the menu builder.
 *
 * @package     yana
 * @subpackage  plugins
 */
interface IsDependencyContainer
{

    /**
     * Returns translation facade.
     *
     * @return  \Yana\Translations\IsFacade
     */
    public function getTranslationFacade();

    /**
     * Returns security facade.
     *
     * @return  \Yana\Security\IsFacade
     */
    public function getSecurityFacade();

    /**
     * Returns security facade.
     *
     * @return  \Yana\Security\Sessions\IsWrapper
     */
    public function getSession();

    /**
     * Returns bool(true) if the currently active profile is the default profile.
     *
     * @return  bool
     */
    public function isDefaultProfile();

    /**
     * Returns a plugin manager.
     *
     * @return \Yana\Plugins\Manager
     */
    public function getPluginManager();

}

?>