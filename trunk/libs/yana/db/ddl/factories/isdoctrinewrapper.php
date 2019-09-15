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
declare(strict_types=1);

namespace Yana\Db\Ddl\Factories;

/**
 * <<wrapper>> Doctrine DBAL wrapper.
 *
 * Consumed by DatabaseFactory.
 *
 * @package     yana
 * @subpackage  db
 * @ignore
 */
interface IsDoctrineWrapper
{

    /**
     * Get name of selected database.
     *
     * @return  string
     */
    public function getDatabaseName();

    /**
     * Get list of sequences in the database.
     *
     * @return  \Doctrine\DBAL\Schema\Sequence[]
     */
    public function listSequences();

    /**
     * Get list of tables.
     *
     * @return  \Doctrine\DBAL\Schema\Table[]
     */
    public function listTables();

    /**
     * Get list of columns associated with the table.
     *
     * @param   string  $tableName  must be a valid database table
     * @return  \Doctrine\DBAL\Schema\Column[]
     */
    public function listTableColumns($tableName);

    /**
     * Get list of indexes associated with the table.
     *
     * @param   string  $tableName  must be a valid database table
     * @return  \Doctrine\DBAL\Schema\Index[]
     */
    public function listTableIndexes($tableName);

    /**
     * Get list of constraints associated with the table.
     *
     * @param   string  $tableName  must be a valid database table
     * @return  \Doctrine\DBAL\Schema\ForeignKeyConstraint[]
     */
    public function listTableConstraints($tableName);

    /**
     * Get list of views.
     *
     * @return  \Doctrine\DBAL\Schema\View[]
     */
    public function listViews();

}

?>