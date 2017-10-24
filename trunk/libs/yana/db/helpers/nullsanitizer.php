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
 * @ignore
 */

namespace Yana\Db\Helpers;

/**
 * <<strategy>> NULL class that does nothing and is supposed to be used for mocking in Unit tests.
 *
 * @package     yana
 * @subpackage  db
 */
class NullSanitizer extends \Yana\Core\Object implements \Yana\Db\Helpers\IsSanitizer
{

    /**
     * Validate a row against database schema.
     *
     * @param   array   $row       values of the inserted/updated row
     * @param   bool    $isInsert  type of operation (true = insert, false = update)
     * @param   array   &$files    list of modified or inserted columns of type file or image
     * @return  array
     */
    public function sanitizeRowByTable(\Yana\Db\Ddl\Table $table, array $row, $isInsert = true, array &$files = array())
    {
        return $row;
    }

    /**
     * Validate a row against database schema.
     *
     * @param   \Yana\Db\Ddl\Column $column  
     * @param   mixed               $value   value of the inserted/updated row
     * @param   array               &$files  list of modified or inserted columns of type file or image
     * @return  bool
     */
    public function sanitizeValueByColumn(\Yana\Db\Ddl\Column $column, $value, array &$files = array())
    {
        return $value;
    }

}

?>