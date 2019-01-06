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

namespace Yana\Db\Ddl\Factories;

/**
 * Null database mapper.
 *
 * @package     yana
 * @subpackage  db
 */
class NullMapper extends \Yana\Core\Object implements \Yana\Db\Ddl\Factories\IsMdb2Mapper
{

    /**
     * Does nothing.
     *
     * @param   \Yana\Db\Ddl\Database  $database  database to add sequence to
     * @param   array        $info      sequence information
     * @param   string       $name      sequence name
     * @return  $this
     */
    public function createSequence(\Yana\Db\Ddl\Database $database, array $info, $name)
    {
        return $this;
    }

    /**
     * Does nothing.
     *
     * @param   \Yana\Db\Ddl\Table  $table  table to add index to
     * @param   array     $info   index information
     * @param   string    $name   index name
     * @return  $this
     */
    public function createIndex(\Yana\Db\Ddl\Table $table, array $info, $name)
    {
        return $this;
    }

    /**
     * Does nothing.
     *
     * @param   \Yana\Db\Ddl\Table  $table  table to add constraint to
     * @param   array     $info   constraint information
     * @param   string    $name   constraint name
     * @return  $this
     */
    public function createConstraint(\Yana\Db\Ddl\Table $table, array $info, $name)
    {
        return $this;
    }


    /**
     * Does nothing.
     *
     * @param   \Yana\Db\Ddl\Table  $table  table to add column to
     * @param   array     $info   column information
     * @param   string    $name   column name
     * @return  $this
     */
    public function createColumn(\Yana\Db\Ddl\Table $table, array $info, $name)
    {
        return $this;
    }

}

?>