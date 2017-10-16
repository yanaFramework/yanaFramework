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

namespace Yana\Db;

/**
 * Mock class for Unit-tests.
 *
 * @package     yana
 * @subpackage  db
 */
class NullTransaction extends \Yana\Core\Object implements \Yana\Db\IsTransaction
{

    /**
     * Commit current transaction and write all changes to the database.
     *
     * @return  \Yana\Db\IsTransaction
     * @throws  \Yana\Db\CommitFailedException  when the commit did not succeed
     */
    public function commit(\Yana\Db\IsDriver $driver)
    {
        return $this;
    }

    /**
     * Update a row or cell.
     *
     * @param   \Yana\Db\Queries\Update  $updateQuery    the address of the row that should be updated
     * @return  \Yana\Db\NullTransaction
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException          when either the given $key or $value is invalid
     * @throws  \Yana\Db\Queries\Exceptions\InvalidResultTypeException  when the query has an invalid column selector
     * @throws  \Yana\Db\Queries\Exceptions\ConstraintException         when a constraint check fails
     */
    public function update(\Yana\Db\Queries\Update $updateQuery)
    {
        return $this;
    }

    /**
     * Insert $value at position $key.
     *
     * @param   \Yana\Db\Queries\Insert  $insertQuery   the address of the row that should be inserted
     * @return  \Yana\Db\NullTransaction
     */
    public function insert(\Yana\Db\Queries\Insert $insertQuery)
    {
        return $this;
    }

    /**
     * Remove row.
     *
     * @param   \Yana\Db\Queries\Delete  $deleteQuery   the address of the row that should be removed
     * @return  \Yana\Db\NullTransaction
     */
    public function remove(\Yana\Db\Queries\Delete $deleteQuery)
    {
        return $this;
    }

    /**
     * Reset the object to default values.
     *
     * @return  \Yana\Db\NullTransaction
     */
    public function rollback()
    {
        return $this;
    }

}

?>