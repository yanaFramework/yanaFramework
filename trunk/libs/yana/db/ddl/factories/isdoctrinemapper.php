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
 * <<interface>> Doctrine to database mapper.
 *
 * Maps Doctrine table info objects to a database object.
 *
 * @package     yana
 * @subpackage  db
 */
interface IsDoctrineMapper
{

    /**
     * Add a sequence to database.
     *
     * @param   \Yana\Db\Ddl\Database           $database  database to add sequence to
     * @param   \Doctrine\DBAL\Schema\Sequence  $info      sequence information
     * @param   string                          $name      sequence name
     * @return  $this
     */
    public function createSequence(\Yana\Db\Ddl\Database $database, \Doctrine\DBAL\Schema\Sequence $info, $name);

    /**
     * Add an index to table.
     *
     * @param   \Yana\Db\Ddl\Table           $table  table to add index to
     * @param   \Doctrine\DBAL\Schema\Index  $info   index information
     * @param   string                       $name   index name
     * @return  $this
     * @throws  \Yana\Core\Exceptions\NotImplementedException   when trying to use a compound primary key
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when no "fields" entry is given in index information
     */
    public function createIndex(\Yana\Db\Ddl\Table $table, \Doctrine\DBAL\Schema\Index $info, $name);

    /**
     * Add a foreign key constraint to table.
     *
     * Check constraints seem to be unsupported by Doctrine at this time.
     *
     * @param   \Yana\Db\Ddl\Table                          $table  table to add constraint to
     * @param   \Doctrine\DBAL\Schema\ForeignKeyConstraint  $info   constraint information
     * @param   string                                      $name   constraint name
     * @throws  \Yana\Core\Exceptions\InvalidSyntaxException   when number of source and target columns in constraint is different
     * @throws  \Yana\Core\Exceptions\NotFoundException        when target database/table/column not found
     * @return  $this
     */
    public function createConstraint(\Yana\Db\Ddl\Table $table, \Doctrine\DBAL\Schema\ForeignKeyConstraint $info, $name);

    /**
     * Add a column to table.
     *
     * @param   \Yana\Db\Ddl\Table            $table  table to add column to
     * @param   \Doctrine\DBAL\Schema\Column  $info   column information
     * @param   string                        $name   column name
     * @throws  \Yana\Core\Exceptions\NotImplementedException   when the given 'type' of column is missing or unknwon
     * @return  $this
     */
    public function createColumn(\Yana\Db\Ddl\Table $table, \Doctrine\DBAL\Schema\Column $info, $name);

}

?>