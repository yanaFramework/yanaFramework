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

namespace Yana\Core;

/**
 * <<abstract>> Utility.
 *
 * "Utility classes" contain only static methods and attributes.
 * This means especially you can't create an instance of such classes.
 *
 * As an example they are usefull to group full static functions of the same domain
 * within a class namespace instead of having them clutter your global namespace.
 *
 * To create a utility class, simply add "extends Utility" to your class definition.
 * Note: this class is abstract by intention not by syntax.
 *
 * @package     yana
 * @subpackage  core
 */
abstract class AbstractUtility extends \StdClass
{

    /**
     * check Utility - constraint
     *
     * This is a protected pseudo-constructor.
     * Utility classes do not allow any instances, since they are intended not
     * to have any instance specific members.
     *
     * @final
     */
    final private function __construct()
    {
        // Cannot create an instance of a static 'utility' class
    }

}

?>