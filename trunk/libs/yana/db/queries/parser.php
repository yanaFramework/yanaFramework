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
 * $connection = \Yana\Application::connect('my_database');
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
     * Parse SQL query into query object.
     *
     * This is the opposite of __toString().
     * It takes a SQL query string as input and returns
     * a query object of the specific type that
     * corresponds to the given type of query.
     *
     * The result object is always a subclass of {@see \Yana\Db\Queries\AbstractQuery}.
     *
     * @param   string     $sqlStatement   SQL statement
     * @return  \Yana\Db\Queries\AbstractQuery
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if the query is invalid or could not be parsed
     */
    public function parseSQL($sqlStatement)
    {
        assert('is_string($sqlStatement); // Wrong argument type argument 1. String expected');
        $trimmedStatement = trim($sqlStatement);
        $sqlParser = new \SQL_Parser();
        $syntaxTree = $sqlParser->parse($trimmedStatement); // get abstract syntax tree (AST)
        unset($sqlParser);
        // Since version 0.7 the function SQL_Parser::parse() may return multiple statements.
        // However, we do know that there can be only one, because we had only one statement as input.
        // So we take the first (and only) statement and continue as usual.
        if (is_array($syntaxTree) && isset($syntaxTree[0]) && is_array($syntaxTree[0])) {
            assert('count($syntaxTree) === 1; // Must not contain more than one statement');
            $syntaxTree = $syntaxTree[0];
        }

        /* @var $parser \Yana\Db\Queries\Parsers\IsParser */
        $parser = null;
        if (is_array($syntaxTree) && !empty($syntaxTree['command'])) {
            $parser = $this->_selectParser($syntaxTree['command'], $trimmedStatement);
        } // else this is no valid SQL

        if (!$parser) {
            $message = "Invalid or unknown SQL statement: $trimmedStatement.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, $level);
        }
        assert('is_array($syntaxTree)');
        return $parser->parseStatement($syntaxTree);
    }

    /**
     * Select the appropriate SQL parser for the command and return in.
     *
     * When none can be found this function returns NULL instead.
     *
     * @param   string  $command       type of SQL command (SELECT, INSERT aso)
     * @param   string  $sqlStatement  the SQL statement in full
     * @return  \Yana\Db\Queries\Parsers\IsParser
     */
    private function _selectParser($command, $sqlStatement)
    {
        assert('is_string($command); // Wrong argument type argument 1. String expected');
        assert('is_string($sqlStatement); // Wrong argument type argument 2. String expected');
        /* @var $parser \Yana\Db\Queries\Parsers\IsParser */
        switch ($command)
        {
            case 'select':
                switch (true)
                {
                    case preg_match('/^select\s+1\s+/i', $sqlStatement):
                        $parser = new \Yana\Db\Queries\Parsers\SelectExistParser($this->_getDatabase());
                        break;
                    case preg_match('/^select\s+count\(/i', $sqlStatement):
                        $parser = new \Yana\Db\Queries\Parsers\SelectCountParser($this->_getDatabase());
                        break;
                    default:
                        $parser = new \Yana\Db\Queries\Parsers\SelectParser($this->_getDatabase());
                        break;
                }
                break;
            case 'insert':
                $parser = new \Yana\Db\Queries\Parsers\InsertParser($this->_getDatabase());
                break;
            case 'update':
                $parser = new \Yana\Db\Queries\Parsers\UpdateParser($this->_getDatabase());
                break;
            case 'delete':
                $parser = new \Yana\Db\Queries\Parsers\DeleteParser($this->_getDatabase());
                break;
            default:
                $parser = null;
        }
        return $parser;
    }

}

?>