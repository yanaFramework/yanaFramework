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

/**
 * <<Interface>> object
 *
 * @access      public
 * @package     yana
 * @subpackage  core
 */
interface IsObject
{

    /**
     * get a string representation of this object
     *
     * This function is intended to be called when the object
     * is used in a string context.
     *
     * @access   public
     * @return   string
     */
    public function toString();

    /**
     * get the class name of the instance
     *
     * This function returns the name of the class of this object as a string.
     *
     * @access public
     * @return string
     */
    public function getClass();

    /**
     * compare with another object
     *
     * Returns bool(true) if this object and $anotherObject
     * are the same and bool(false) otherwise.
     *
     * @access public
     * @param  object $anotherObject    another object to compare
     * @return bool
     */
    public function equals(object $anotherObject);

}

?>