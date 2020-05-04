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

namespace Yana\Db\Queries;

/**
 * <<abstract>> Database connection wrapper.
 *
 * @package     yana
 * @subpackage  db
 */
abstract class AbstractConnectionWrapper extends \Yana\Core\StdObject implements \Serializable
{

    /**
     * @var \Yana\Db\IsConnection
     */
    private $_db = null;

    /**
     * create a new instance
     *
     * This creates and initializes a new instance of this class.
     *
     * The argument $database can be an instance of class Connection or
     * any derived sub-class (e.g. FileDb).
     *
     * @param  \Yana\Db\IsConnection  $database  a database resource
     */
    public function __construct(\Yana\Db\IsConnection $database)
    {
        $this->_db = $database;
    }

    /**
     * magic function
     *
     * This is automatically used to create copies of the object when using the "clone" keyword.
     * This creates a shallow copy and thus overwrites the default behavior of the parent class.
     *
     * @ignore
     */
    public function __clone()
    {
        // overwrite parent
    }

    /**
     * Returns the query's database connection object.
     *
     * @return \Yana\Db\IsConnection
     */
    public function getDatabase(): \Yana\Db\IsConnection
    {
        return $this->_db;
    }

    /**
     * Returns the serialized object as a string.
     *
     * @return  string
     */
    public function serialize()
    {
        // returns a list of key => value pairs
        $properties = get_object_vars($this);
        // remove the table object (it is redundant)
        unset($properties['_table']);
        $properties['_db'] = $this->getDatabase()->getSchema()->getName();
        return serialize($properties);
    }

    /**
     * Reinitializes the object.
     *
     * @param   string  $string  string to unserialize
     */
    public function unserialize($string)
    {
        foreach (unserialize($string) as $key => $value)
        {
            $this->$key = $value;
        }
        if (is_string($this->_db)) {
            $builder = new \Yana\ApplicationBuilder();
            $this->_db = $builder->buildApplication()->connect($this->_db);
        }
    }

}

?>