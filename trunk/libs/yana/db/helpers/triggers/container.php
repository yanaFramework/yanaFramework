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
     * current table
     *
     * @var  \Yana\Db\Ddl\Table
     */
    public $table = null;

    /**
     * new values of the modified row
     *
     * @var  \Yana\Db\Queries\AbstractQuery
     */
    public $query = null;

    /**
     * Create new instance
     *
     * @param  \Yana\Db\Ddl\Table              $table  name of table
     * @param  \Yana\Db\Queries\AbstractQuery  $query  database query to execute
     */
    public function __construct(\Yana\Db\Ddl\Table $table, \Yana\Db\Queries\AbstractQuery $query)
    {
        $this->table = $table;
        $this->query = $query;
    }

}

?>