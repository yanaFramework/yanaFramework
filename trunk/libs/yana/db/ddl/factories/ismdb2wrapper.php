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
 * <<interface>> MDB2 wrapper.
 *
 * Consumed by DatabaseFactory.
 *
 * @package     yana
 * @subpackage  db
 * @ignore
 */
interface IsMdb2Wrapper
{

    /**
     * Get name of selected database.
     *
     * @return  string
     * @throws  \Yana\Db\DatabaseException  if an error occurs, e.g. the connection is invalid/closed
     */
    public function getDatabaseName();

    /**
     * Get list of sequences in the database.
     *
     * Returns array where the keys are the sequence names and the values are the info-arrays returned by MDB2->getSequenceDefinition().
     *
     * @return  array
     * @throws  \Yana\Db\DatabaseException  if an error occurs, e.g. the connection is invalid/closed
     */
    public function listSequences();

    /**
     * Get list of tables.
     *
     * @return  array
     * @throws  \Yana\Db\DatabaseException  if an error occurs, e.g. the connection is invalid/closed
     */
    public function listTables();

    /**
     * Get list of columns associated with the table.
     *
     * Returns array where the keys are the column names and the values are the info-arrays returned by MDB2->getTableFieldDefinition().
     *
     * @param   string  $tableName  must be a valid database table
     * @return  array
     * @throws  \Yana\Db\DatabaseException  if an error occurs, e.g. the table doesn't exist
     */
    public function listTableColumns($tableName);

    /**
     * Get list of indexes associated with the table.
     *
     * Returns array where the keys are the index names and the values are the info-arrays returned by MDB2->getTableIndexDefinition().
     *
     * @param   string  $tableName  must be a valid database table
     * @return  array
     * @throws  \Yana\Db\DatabaseException  if an error occurs, e.g. the table doesn't exist
     */
    public function listTableIndexes($tableName);

    /**
     * Get list of constraints associated with the table.
     *
     * Returns array where the keys are the constraint names and the values are the info-arrays returned by MDB2->getTableConstraintDefinition().
     *
     * @param   string  $tableName  must be a valid database table
     * @return  array
     * @throws  \Yana\Db\DatabaseException  if an error occurs, e.g. the table doesn't exist
     */
    public function listTableConstraints($tableName);

}

?>