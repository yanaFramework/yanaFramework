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

namespace Yana\Db\Queries;

/**
 * <<abstract>> SQL wrapper.
 *
 * @package     yana
 * @subpackage  db
 */
abstract class AbstractSql extends \Yana\Db\Queries\AbstractConnectionWrapper
{

    /**
     * SQL statements
     *
     * @var string
     */
    private $_sqlStatement = "";

    /**
     * create a new instance
     *
     * This creates and initializes a new instance of this class.
     *
     * The argument $database can be an instance of class Connection or
     * any derived sub-class (e.g. FileDb).
     *
     * @param  \Yana\Db\IsConnection  $database      a database resource
     * @param  string                 $sqlStatement  a single SQL statement
     */
    public function __construct(\Yana\Db\IsConnection $database, $sqlStatement)
    {
        assert('is_string($sqlStatement); // Wrong argument type: $sqlStatement. String expected');
        parent::__construct($database);
        $this->_setSqlStatement($sqlStatement);
    }

    /**
     * Get wrapped SQL string.
     * 
     * @return  string
     */
    protected function _getSqlStatement()
    {
        return $this->_sqlStatement;
    }

    /**
     * Set a SQL string to wrap.
     *
     * Note that the string is not checked for validity.
     *
     * @param   string  $sqlStatement  one single SQL statement
     * @return  $this
     */
    protected function _setSqlStatement($sqlStatement)
    {
        assert('is_string($sqlStatement); // Wrong argument type: $sqlStatement. String expected');
        $this->_sqlStatement = (string) $sqlStatement;
        return $this;
    }

    /**
     * Return the wrapped SQL statement.
     *
     * @return  string
     */
    public function __toString()
    {
        return $this->_getSqlStatement();
    }

}

?>