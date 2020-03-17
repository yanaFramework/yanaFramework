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

namespace Yana\Db;

/**
 * <<abstract>> Database transaction class.
 *
 * @package     yana
 * @subpackage  db
 */
abstract class AbstractTransaction extends \Yana\Core\StdObject implements \Yana\Db\IsTransaction
{

    /**
     * Queue of statements belonging to this transaction
     *
     * @var  array
     */
    private $_queue = array();

    /**
     * database schema
     *
     * The database schema that is used in the current session.
     *
     * Please note that you should not change this schema unless
     * you REALLY know what you are doing.
     *
     * @var  \Yana\Db\Ddl\Database
     */
    private $_schema  = null;

    /**
     * Create a new instance.
     *
     * Each database connection depends on a schema file describing the database.
     * These files are to be found in config/db/*.db.xml
     *
     * @param   \Yana\Db\Ddl\Database  $schema  schema in database definition language
     * @throws  \Yana\Core\Exceptions\NotWriteableException  when the database or table is locked
     */
    public function __construct(\Yana\Db\Ddl\Database $schema)
    {
        if ($schema->isReadonly()) {
            $message = "Unable to commit changes. Database schema is set to read-only.";
            throw new \Yana\Core\Exceptions\NotWriteableException($message);
        }
        $this->_schema = $schema;
    }

    /**
     * The database schema that is used in the current session.
     *
     * Please note that you should not change this schema unless
     * you REALLY know what you are doing.
     *
     * @return \Yana\Db\Ddl\Database
     */
    protected function _getSchema(): \Yana\Db\Ddl\Database
    {
        return $this->_schema;
    }

    /**
     * Adds a query to the queue.
     *
     * @param  \Yana\Db\Queries\AbstractConnectionWrapper   $query              to add to queue
     * @param  \Yana\Db\Helpers\Triggers\TriggerCollection  $triggerCollection  triggers that should fire upon execution
     */
    protected function _addToQueue(\Yana\Db\Queries\AbstractConnectionWrapper $query, \Yana\Db\Helpers\Triggers\TriggerCollection $triggerCollection)
    {
        $this->_queue[] = array($query, $triggerCollection);
    }

    /**
     * Resets query queue to an empty array.
     */
    protected function _resetQueue()
    {
        $this->_queue = array();
    }

    /**
     * Return contents of query queue.
     *
     * Returns empty array if there are none.
     *
     * @return array
     */
    protected function _getQueue(): array
    {
        return $this->_queue;
    }

}

?>