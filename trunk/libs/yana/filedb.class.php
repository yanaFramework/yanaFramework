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

/**
 * simulate a database
 *
 * this class simulates a sql-database on a flat-file
 *
 * Example:
 * <code>
 * $schema = XDDL::getDatabase('log');
 * $db = new FileDb($schema);
 * print_r($db->select('log.*'));
 * </code>
 *
 * You might also want to see the factory function
 * {@link Yana::connect()}.
 *
 * @access      public
 * @package     yana
 * @subpackage  database
 */
class FileDb extends DbStream
{
    /**
     * database connection information
     *
     * Note: only "database" name is used.
     * All other entries are static.
     *
     * @access  protected
     * @var     array
     */
    protected $dsn = array(
        'USE_ODBC' => false,
        'DBMS'     => '',
        'HOST'     => false,
        'PORT'     => false,
        'USERNAME' => false,
        'PASSWORD' => false,
        'DATABASE' => 'yana'
    );

    /**
     * constructor
     *
     * Create a new instance of this class.
     *
     * @param  DDLDatabase  $schema  database definition language
     */
    public function __construct(DDLDatabase $schema = null)
    {
        if (is_null($schema)) {
            /*
             * If no structure file is available,
             * this framework has the ability to estimate the structure
             * of the database "on the fly" at runtime, via the
             * "DbStream::buildStructure()" method.
             */
            $this->buildStructure();
            $this->dsn['DATABASE'] = "";
        } else {
            $this->dsn['DATABASE'] = $schema->getName();
            $this->schema = $schema;
            assert('$this->schema instanceof DDLDatabase;');
        }
    }

    /**
     * optional API bypass
     *
     * Send a sinle sql-statement directly to FileDB, bypassing the query-builder.
     *
     * The function will return bool(false) if the database connection is not
     * available and otherwise will return whatever FileDB sends back as the
     * result of your statement.
     *
     * Note: FileDB was designed to understand the statements generated
     * by the query-builder and is not intended to be called directly.
     * This means it will understand only SQL statements that comply with the syntax
     * that is provided by the query-builder.
     *
     * @access  public
     * @param   string|object  $sqlStmt  one SQL statement to execute
     * @param   int            $offset   the row to start from
     * @param   int            $limit    the maximum numbers of rows in the resultset
     * @return  mixed
     * @since   2.8.8
     * @ignore
     */
    public function query($sqlStmt, $offset = 0, $limit = 0)
    {
        assert('is_int($offset) && $offset >= 0; // Invalid argument $offset. Must be a positive integer.');
        assert('is_int($limit) && $limit >= 0; // Invalid argument $limit. Must be a positive integer.');

        // check input
        if ($sqlStmt instanceof DbQuery) {
            return $this->getConnection()->dbQuery($sqlStmt);
        } elseif (!is_string($sqlStmt)) {
            throw new InvalidArgumentException('Argument $sqlStmt is expected to be a string.');
        }

        $reg = "/;.*(?:select|insert|delete|update|create|alter|grant|revoke).*$/is";
        if (strpos($sqlStmt, ';') !== false && preg_match($reg, $sqlStmt)) {
            $message = "A semicolon has been found in the current input '{$sqlStmt}', " .
                "indicating multiple queries.\n\t\t As this might be the result of a hacking attempt " .
                "it is prohibited for security reasons and the queries won't be executed.";
            throw new InvalidArgumentException($message);
        }

        // send query to database
        return $this->getConnection()->limitQuery($sqlStmt, $offset, $limit);
    }

    /**
     * build structure
     *
     * @access  protected
     * @throws  NotFoundException     if database definition is not found
     * @throws  NotReadableException  if database definition is not readable
     */
    protected function buildStructure()
    {
        foreach (DDL::getListOfFiles() as $db)
        {
            if (empty($this->schema)) {
                $this->schema = XDDL::getDatabase($db);
                $this->path = $db;
            } else {
                $this->schema->addInclude($db);
            }
        }
        $this->schema->loadIncludes();
    }

    /**
     * isError
     *
     * @access  public
     * @param   mixed  $result  result
     * @return  bool
     * @ignore
     */
    public function isError($result)
    {
        /* @var $result FileDbResult */
        if ($result instanceof FileDbResult) {
            return $result->isError();
        } else {
            return false;
        }
    }

    /**
     * get database connection
     *
     * @access  protected
     * @return  MDB2_Driver_Common
     * @ignore
     */
    protected function getConnection()
    {
        if (!isset($this->database)) {
            $this->database = new FileDbConnection($this->schema);
            $this->database->autoCommit(false);
        }
        return $this->database;
    }
}

?>