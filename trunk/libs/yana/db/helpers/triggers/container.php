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

namespace Yana\Db\Helpers\Triggers;

/**
 * Result container class that is passed to a function evaluating a trigger.
 *
 * @package     yana
 * @subpackage  db
 */
class Container extends \Yana\Core\Object
{

    /**
     * current operation
     *
     * @var  string
     */
    public $operation = "";

    /**
     * current table
     *
     * @var  \Yana\Db\Ddl\Table
     */
    public $table = null;

    /**
     * current field
     *
     * @var  int
     */
    public $field = "";

    /**
     * current value
     *
     * @var  mixed
     */
    public $value = null;

    /**
     * current row id
     *
     * This is the value of the primary key column.
     * It is only available if it was part of the query.
     *
     * @var  scalar
     */
    public $row = null;

    /**
     * Create new instance
     *
     * @param  string  $table  name of table
     * @param  mixed   $value  value of column
     * @param  string  $field  name of column
     * @param  mixed   $rowId  value of primary key
     */
    public function __construct(\Yana\Db\Ddl\Table $table, &$value, $field = "", $rowId = null)
    {
        assert('is_string($field); // Invalid argument $field: string expected');
        $this->table = $table;
        $this->value =& $value;
        $this->field = mb_strtoupper((string) $field);
        $this->row = $rowId;
    }

}

?>