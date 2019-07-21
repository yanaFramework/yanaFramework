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
 * <<interface>> MDB2 to database mapper.
 *
 * Maps MDB2 table info arrays to a database object.
 *
 * @package     yana
 * @subpackage  db
 */
interface IsMdb2Mapper
{

    /**
     * Add a sequence to database.
     *
     * Info contains these elements:
     * <code>
     * array(
     *   [start] => int
     * );
     * </code>
     *
     * @param   \Yana\Db\Ddl\Database  $database  database to add sequence to
     * @param   array        $info      sequence information
     * @param   string       $name      sequence name
     * @return  $this
     * @throws  \Yana\Core\Exceptions\AlreadyExistsException    when a sequence with the same name already exists
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if given an invalid name
     */
    public function createSequence(\Yana\Db\Ddl\Database $database, array $info, $name);

    /**
     * Add a index to table.
     *
     * Info contains these elements:
     * <code>
     * array(
     *   [fields] => array(
     *     [fieldname] => array( [sorting] => ascending )
     *     // more fields
     *   )
     * );
     * </code>
     *
     * @param   \Yana\Db\Ddl\Table  $table  table to add index to
     * @param   array     $info   index information
     * @param   string    $name   index name
     * @return  $this
     */
    public function createIndex(\Yana\Db\Ddl\Table $table, array $info, $name);

    /**
     * Add a constraint to table.
     *
     * Info contains these elements:
     * <code>
     *  array(
     *      [primary] => 0
     *      [unique]  => 0
     *      [foreign] => 1
     *      [check]   => 0
     *      [fields] => array(
     *          [field1name] => array() // one entry per each field covered
     *          [field2name] => array() // by the index
     *          [field3name] => array(
     *              [sorting]  => ascending
     *              [position] => 3
     *          )
     *      )
     *      [references] => array(
     *          [table] => name
     *          [fields] => array(
     *              [fieldname] => array( [position] => 1 )
     *              // more fields
     *          )
     *      )
     *      [deferrable] => 0
     *      [initiallydeferred] => 0
     *      [onupdate] => CASCADE|RESTRICT|SET NULL|SET DEFAULT|NO ACTION
     *      [ondelete] => CASCADE|RESTRICT|SET NULL|SET DEFAULT|NO ACTION
     *      [match] => SIMPLE|PARTIAL|FULL
     *  );
     * </code>
     *
     * @param   \Yana\Db\Ddl\Table  $table  table to add constraint to
     * @param   array     $info   constraint information
     * @param   string    $name   constraint name
     * @throws  \Yana\Core\Exceptions\NotImplementedException  when trying to use a compound primary key
     * @throws  \Yana\Core\Exceptions\InvalidSyntaxException   when number of source and target columns in constraint is different
     * @return  $this
     */
    public function createConstraint(\Yana\Db\Ddl\Table $table, array $info, $name);

    /**
     * Add a column to table.
     *
     * Info contains these elements:
     * <code>
     *  array(
     *      [notnull] => 1
     *      [nativetype] => int
     *      [length] => 10
     *      [fixed] => 0
     *      [default] => 0
     *      [type] =>
     *      [mdb2type] => integer
     *  );
     * </code>
     *
     * @param   \Yana\Db\Ddl\Table  $table  table to add column to
     * @param   array     $info   column information
     * @param   string    $name   column name
     * @throws  \Yana\Core\Exceptions\NotImplementedException  when the given 'type' of column is unknwon
     * @return  $this
     */
    public function createColumn(\Yana\Db\Ddl\Table $table, array $info, $name);
}

?>