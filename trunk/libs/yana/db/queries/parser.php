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
 * Query-Parser.
 *
 * This class allows you to parse a SQL-Statement into a query object.
 *
 * Example:
 * <code>
 * $connection = \Yana::connect('my_database');
 * $parser = new Parser($connection);
 * $selectQuery = $parser->parseSQL("Select * from myTable where id = 1;");
 * </code>
 *
 * @package     yana
 * @subpackage  db
 */
class Parser extends \Yana\Core\Object implements \Yana\Db\Queries\IsParser
{

    /**
     * @var \Yana\Db\IsConnection 
     */
    private $_database = null;

    /**
     * Set up the database to build the queries upon.
     *
     * @param  \Yana\Db\IsConnection  $database 
     */
    public function __construct(\Yana\Db\IsConnection $database)
    {
        $this->_database = $database;
    }

    /**
     * @return \Yana\Db\IsConnection
     */
    protected function _getDatabase()
    {
        return $this->_database;
    }

    /**
     * parse SQL query into query object
     *
     * This is the opposite of __toString().
     * It takes a SQL query string as input and returns
     * a query object of the specific type that
     * corresponds to the given type of query.
     *
     * The result object is always a subclass of {@see \Yana\Db\Queries\AbstractQuery}.
     *
     * @param   string     $sqlStmt   SQL statement
     * @return  \Yana\Db\Queries\AbstractQuery
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if the query is invalid or could not be parsed
     */
    public function parseSQL($sqlStmt)
    {
        assert('is_string($sqlStmt); // Wrong argument type argument 1. String expected');
        $sqlStmt = trim($sqlStmt);
        $parser = new \SQL_Parser();
        $syntaxTree = $parser->parse($sqlStmt); // get abstract syntax tree (AST)
        /* @var $parser \Yana\Db\Queries\Parsers\IsParser */
        $parser = null;
        if (is_array($syntaxTree) && !empty($syntaxTree['command'])) {
            switch ($syntaxTree['command'])
            {
                case 'select':
                    switch (true)
                    {
                        case preg_match('/^select\s+1\s+/i', $sqlStmt):
                            $parser = new \Yana\Db\Queries\Parsers\SelectExistParser($this->_getDatabase());
                        case preg_match('/^select\s+count\(/i', $sqlStmt):
                            $parser = new \Yana\Db\Queries\Parsers\SelectCountParser($this->_getDatabase());
                        default:
                            $parser = new \Yana\Db\Queries\Parsers\SelectParser($this->_getDatabase());
                    }
                    break;
                case 'insert':
                    $parser = new \Yana\Db\Queries\Parsers\InsertParser($this->_getDatabase());
                case 'update':
                    $parser = new \Yana\Db\Queries\Parsers\UpdateParser($this->_getDatabase());
                case 'delete':
                    $parser = new \Yana\Db\Queries\Parsers\DeleteParser($this->_getDatabase());
            }
        }
        if (!$parser) {
            $message = "Invalid or unknown SQL statement: $sqlStmt.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, E_USER_WARNING);
        }
        return $parser->parseStatement($syntaxTree);
    }

}

?>