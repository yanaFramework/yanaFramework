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
 * Warning
 *
 * This class represents warnings passed to the user.
 *
 * @access      public
 * @package     yana
 * @subpackage  error_reporting
 */
class AlreadyExistsWarning extends Warning
{

    /**
     * Set id of duplicate entry.
     *
     * @access  public
     * @param   scalar  $id  identifier of duplicate entry
     * @return  AlreadyExistsWarning
     */
    public function setId($id)
    {
        assert('is_scalar($id); // Invalid argument $id: Scalar expected');
        $this->data = array('ID' => $id);
        return $this;
    }

}

?>