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
 * Database query builder
 *
 * This class is a query builder that can be used to build SQL statements to insert new rows
 * in a database-table. The inserted row must not exist.
 *
 * Note: this class does NOT untaint input data for you.
 *
 * @access      public
 * @package     yana
 * @subpackage  database
 */
class DbInsert extends DbQuery
{
    /**#@+
     * @access  protected
     * @ignore
     */

    /** @var int   */ protected $type    = DbQueryTypeEnumeration::INSERT;
    /** @var array */ protected $column  = array();
    /** @var array */ protected $profile = array();
    /** @var array */ protected $values  = null;
    /** @var array */ protected $queue   = array();
    /** @var array */ protected $files   = array();

    /**#@-*/

    /**
     * clone (magic function)
     *
     * This is automatically used to create copies of the object when
     * using the "clone" keyword.
     *
     * It will empty the query queue for the cloned object.
     * Note: the cloned object will use the same database connection, since
     * connection are resources, which may not be cloned.
     *
     * @access  public
     */
    public function __clone()
    {
        $this->queue = array();
    }

    /**
     * reset query
     *
     * Resets all properties of the query object, except
     * for the database connection and the properties
     * "table", "type", "useInheritance".
     *
     * This function allows you to "recycle" a query object
     * and reuse it without creating another one. This can
     * help to improve the performance of your application.
     *
     * @access  public
     * @since   2.9.4
     */
    public function resetQuery()
    {
        parent::resetQuery();
        $this->profile = array();
        $this->values  = null;
        $this->queue   = array();
        $this->files   = array();
    }

    /**
     * set value(s) for current query
     *
     * This takes an associative array, where the keys are column names.
     * When updating a single column, it may also be a scalar value.
     *
     * @access  public
     * @param   mixed  $values            value(s) for current query
     * @throws  InvalidArgumentException  if a given argument is invalid
     * @throws  InvalidValueWarning       if a givem value does not match expected criteria
     * @throws  DbWarningLog              if an input check fails
     */
    public function setValues($values)
    {
        $this->id = null;
        /*
         * 1.a) lowercase array keys
         */
        if (is_array($values)) {
            $values = Hashtable::changeCase($values, CASE_LOWER);

        } elseif ($this->type === DbQueryTypeEnumeration::INSERT) {
            throw new InvalidArgumentException("Invalid type. " .
                "Database values must be an array for insert-statements.");

        /*
         * 1.b) error - wrong argument type
         */
        } elseif (!is_scalar($values)) {
            throw new InvalidArgumentException("Invalid type. " .
                "Database values must be an array or scalar.");
        }
        assert('isset($this->tableName); // Cannot set values - need to set table first!');

        /*
         * 1.d) handle table inheritance
         */
        if ($this->getParent()) {
            assert('!isset($columns); // Cannot redeclare var $columns');
            $columns = $this->getColumns();
            if (empty($columns)) {
                assert('!isset($columnName); // Cannot redeclare var $columnName');
                assert('!isset($parent); // Cannot redeclare var $parent');
                foreach (array_keys($values) as $columnName)
                {
                    $parent = $this->getParentByColumn($columnName);
                    if (false !== $parent) {
                        $this->_appendValue($parent, $columnName, $values[$columnName]);
                        unset($values[$columnName]);
                    }
                }
                unset($parent, $columnName);
            } else {
                assert('!isset($column); // Cannot redeclare var $column');
                foreach ($columns as $column)
                {
                    assert('is_array($column); // Invalid property "column". Two-dimensional array expected.');
                    assert('!isset($parent); // Cannot redeclare var $parent');
                    $parent = $this->getParentByColumn($column[1]);
                    if (false !== $parent) {
                        $this->_appendValue($parent, $column[1], $values);
                        return;
                    }
                    unset($parent);
                }
                unset($column);
            }
            unset($columns);
        }

        $table = $this->currentTable();

        /*
         * 2.a) inserting a row
         */
        assert('!isset($primaryKey); /* Cannot redeclare var $primaryKey */');
        $primaryKey = $table->getPrimaryKey();

        // copy primary key to row property
        if ($this->expectedResult === DbResultEnumeration::TABLE && isset($values[$primaryKey])) {
            $this->setRow($values[$primaryKey]);
        }

        assert('!isset($isInsert); // Cannot redeclare var $isInsert');
        $isInsert = false;
        if ($this->type === DbQueryTypeEnumeration::INSERT) {
            $isInsert = true;
            assert('is_array($values);');

            /*
             * 2.a.1) copy primary key from row property or vice versa
             */
            if (!isset($values[$primaryKey])) {
                assert('!isset($column);');
                $column = $table->getColumn($primaryKey);
                if ($column->isAutoIncrement()) {
                    /* ignore - is to be inserted automatically by database */
                } elseif ($this->row !== '*') {
                    $values[$primaryKey] = $column->sanitizeValue($this->row, $this->db->getDBMS(), $this->files);
                } else {
                    $message = "Cannot insert a row without a primary key. Operation aborted.";
                    throw new DbWarningLog($message);
                }
            } elseif ($this->row !== '*' && strcasecmp($this->row, $values[$primaryKey]) !== 0) {
                $message = "Cannot set values. The primary key is ambigious.\n\t\t" .
                    "The primary key has been set via " . __CLASS__ . "->setRow() or " .
                    __CLASS__ . "->setKey() to '" . $this->row . "'.\n\t\t" .
                    "However, the primary key provided with " . __CLASS__ . "->setValues() is '" .
                    $values[$primaryKey] . "'.";
                throw new DbWarningLog($message, E_USER_WARNING);
            }

        }

        if ($this->expectedResult === DbResultEnumeration::ROW) {
            assert('is_array($values); // Row must be an array');
            // upper-case primary key
            if (isset($values[$primaryKey])) {
                $values[$primaryKey] = mb_strtoupper($values[$primaryKey]);
            }
            // check if row is valid
            $values = $table->sanitizeRow($values, $this->db->getDBMS(), $isInsert, $this->files);
        } else {
            assert('!$isInsert; // May only insert rows, not tables, cells or columns');
            assert('$this->expectedResult === DbResultEnumeration::CELL || ' .
                '$this->expectedResult ===  DbResultEnumeration::COLUMN;');
            if (empty($this->arrayAddress) && isset($this->column[0]) && is_array($this->column[0])) {
                assert('count($this->column) === 1;');
                assert('count($this->column[0]) === 2;');
                assert('isset($this->column[0][1]);');
                assert('$this->tableName === $this->column[0][0];');
                assert('$table->isColumn($this->column[0][1]);');
                assert('!isset($column); // Cannot redeclare var $column');
                $column = $table->getColumn($this->column[0][1]);
                assert('$column instanceof DDLColumn;');
                $values = $column->sanitizeValue($values, $this->db->getDBMS(), $this->files);
                unset($column);
            }
        }
        unset($primaryKey);

        /*
         * 3) error - access denied
         */
        if (!$this->checkProfile($values)) {
            $message = "Cannot set values. Profile constraint mismatch.";
            throw new DbWarningLog($message, E_USER_WARNING);
        }

        /*
         * 4) input is valid - update values
         */
        $this->values =& $values;
    }

    /**
     * get the list of values
     *
     * If none are available, NULL (not bool(false)!) is returned.
     *
     * @access  public
     * @return  mixed
     */
    public function &getValues()
    {
        return $this->values;
    }

    /**
     * append value to query queue
     *
     * @access  private
     * @param   string  $table   table name
     * @param   string  $column  column name
     * @param   mixed   $value   value
     * @throws  InvalidValueWarning  value cannot be appended because another value is already
     *                               set for the given table and column
     */
    private function _appendValue($table, $column, $value)
    {
        assert('is_string($table); // Wrong type for argument 1. String expected');
        assert('is_string($column); // Wrong type for argument 2. String expected');

        if (!isset($this->queue[$table])) {
            $this->queue[$table] = array();
        }

        // error - duplicate value
        if (isset($this->queue[$table][$column])) {
            $error = new InvalidValueWarning();
            throw $error->setField($column);
        }
        // append value to queue
        $this->queue[$table][$column] = $value;
    }

    /**
     * check profile constraint
     *
     * @access  protected
     * @param   mixed   &$value value
     * @return  bool
     * @since   2.9.3
     * @ignore
     */
    protected function checkProfile(&$value)
    {
        // table has no profile constraint
        if (!$this->currentTable()->hasProfile()) {
            return true;
        }

        $session = SessionManager::getInstance();
        switch (true)
        {
            case isset($value['profile_id']) && $session->checkPermission($value['profile_id']) !== true:
            case $session->checkPermission() !== true:
                throw new InsufficientRightsWarning();
            break;
            default:
                return true;
            break;
        }
    }

    /**
     * send query to server
     *
     * This sends the query to the database and returns a result-object.
     *
     * @access  public
     * @return  FileDbResult
     * @throws  DbError  when a query fails
     * @ignore
     */
    public function sendQuery()
    {
        // send query
        $result = parent::sendQuery();

        // query failed
        if ($this->db->isError($result)) {
            return $result;
        }

        // execute queue
        if (!empty($this->queue)) {
            // retrieve and submit queries in queue
            $row = $this->getRow();
            // deactivate table-inhertitance (will be reverted later)
            $prevSetting = $this->useInheritance;
            $this->useInheritance(false);
            $result = null;
            assert('!isset($table); // Cannot redeclare var $table');
            assert('!isset($values); // Cannot redeclare var $values');
            foreach ($this->queue as $table => $values)
            {
                // Table not found
                $this->setTable($table);
                // Row-definition is invalid
                $this->setRow($row);
                // Values are invalid
                $this->setValues($values);
                // submit query
                $result = $this->db->query($this);
                // break on error
                if ($this->db->isError($result)) {
                    break;
                }
            }
            unset($table, $values);
            // re-activate inheritance
            $this->useInheritance($prevSetting);
            $this->queue = array();
        }

        // upload new files
        if (!$this->db->isError($result)) {
            $this->deleteFiles($this->files);
            $this->uploadFiles($this->files);
        }

        // return result object
        return $result;
    }

    /**
     * upload new files
     *
     * When a row is updated, blobs associated with it old values need to be removed.
     *
     * A list of these files was created before the row was updated.
     * Now we need to remove the old files and upload the new ones.
     *
     * @access  protected
     * @param   array  $files  list of files to upload
     * @ignore
     */
    protected function uploadFiles(array $files = array())
    {
        foreach ($files as $file)
        {
            /* @var $column DDLColumn */
            $column = $file['column'];
            $columnName = $column->getName();
            $fileId = $this->values[$columnName];
            if (!empty($fileId)) {
                if ($column->getType() === 'image') {
                    DbBlob::uploadImage($file, $fileId, $column->getImageSettings());
                } else {
                    DbBlob::uploadFile($file, $fileId);
                }
            }
        }
    }

    /**
     * get unique id
     *
     * @access  public
     * @return  string
     * @since   2.9.3
     * @ignore
     */
    public function toId()
    {
        if (!isset($this->id)) {
            $this->id = serialize(array($this->type, $this->tableName, $this->column, $this->row,
            $this->where, $this->orderBy, $this->desc, $this->values));
        }
        return $this->id;
    }

    /**
     * build a SQL-query
     *
     * @access  public
     * @param   string $stmt sql statement
     * @return  string
     */
    public function toString($stmt = "INSERT INTO %TABLE% (%KEYS%) VALUES (%VALUES%)")
    {
        /*
         * replace %KEYS% and %VALUES%
         *
         * Note: this is done here, since all other types
         * of statements do not have these token.
         */
        if (strpos($stmt, '%KEYS%') !== false) {
            if (!is_array($this->values) || count($this->values) === 0) {
                return "";
            }
            assert('!isset($keys);   // Cannot redeclare $keys');
            assert('!isset($values); // Cannot redeclare $values');
            $keys = "";
            // quote id's to avoid conflicts with reserved keywords
            assert('!isset($value); // Cannot redeclare var $value');
            foreach (array_keys($this->values) as $value)
            {
                if ($keys != "") {
                    $keys .= ", ";
                }
                $keys .= $this->db->quoteId($value);
            }
            unset($value);
            $values = '';
            assert('!isset($value); // Cannot redeclare $values');
            foreach ($this->values as $value)
            {
                $values .= (($values !== '') ? ', ' : '' );
                if (is_array($value)) {
                    $values .= $this->db->quote(json_encode($value));
                } else {
                    $values .= $this->db->quote($value);
                }
            }
            unset($value);
            $stmt = str_replace('%KEYS%', $keys, $stmt);
            $stmt = str_replace('%VALUES%', $values, $stmt);
            unset($keys, $values);
        }

        return parent::toString($stmt);
    }

    /**
     * parse SQL query into query object
     *
     * This is the opposite of toString().
     * It takes a SQL query string as input and returns
     * a query object of the specific type that
     * corresponds to the given type of query.
     *
     * The result object is always a subclass of DbQuery.
     *
     * @access  public
     * @static
     * @param   string    $sqlStmt   SQL statement
     * @param   DbStream  $database  database connection
     * @return  DbInsert
     * @throws  InvalidArgumentException  if the query is invalid or could not be parsed
     */
    public static function parseSQL($sqlStmt, DbStream $database)
    {
        // this is a parser/lexer, that parses a given SQL string into an AST
        if (!is_array($sqlStmt)) {
            assert('is_string($sqlStmt); // Wrong argument type for argument 1. String expected.');
            $parser = new \SQL_Parser();
            $sqlStmt = $parser->parse($sqlStmt); // get abstract syntax tree (AST)
        }

        $table = current($sqlStmt['tables']); // array of table names
        $keys = $sqlStmt['columns']; // array of column names
        $values = $sqlStmt['values']; // array of value settings
        $set = array(); // combined array of $keys and $values

        $query = new self($database);
        $query->setTable($table);

        // combine arrays of keys and values
        $set = $query->parseSet($keys, $values);
        if (empty($set)) {
            $message = 'SQL syntax error. The statement contains illegal values.';
            throw new DbWarningLog($message);
        }
        unset($keys, $values);

        // set values
        $query->setValues($set);

        // check security constraint
        if ($query->getExpectedResult() !== DbResultEnumeration::ROW) {
            if (!$query->table->getColumn($query->table->getPrimaryKey())->isAutoFill()) {
                $message = "SQL security restriction. Cannot insert a table (only rows).";
                throw new InvalidArgumentException($message, E_USER_WARNING);
            }
        }
        return $query;
    }

    /**
     * combine a list of keys and values
     *
     * Returns the row-array on success.
     * On failure an empty array is returned.
     *
     * @access  protected
     * @param   array  $keys      keys
     * @param   array  $values    values
     * @return  array
     * @throws  InvalidArgumentException  when given column does not exist
     * @ignore
     */
    protected function parseSet(array $keys, array $values)
    {
        assert('count($keys) == count($values);');
        // prepare values
        assert('!isset($value); // Cannot redeclare var $value');
        assert('!isset($i); // Cannot redeclare var $i');
        foreach ($values as $i => $value)
        {
            if (array_key_exists('value', $value)) {
                $values[$i] = $value['value'];
            }
        }
        unset($i, $value);
        // combine keys and values
        $set = array();
        $table = $this->currentTable();
        assert('!isset($column); // Cannot redeclare var $column');
        assert('!isset($i); // Cannot redeclare var $i');
        for ($i = 0; $i < count($keys); $i++)
        {
            $column = $table->getColumn($keys[$i]);
            if (! $column instanceof DDLColumn) {
                throw new InvalidArgumentException("Column '".$keys[$i]."' does not exist " .
                    "in table '" .$this->tableName."'.", E_USER_WARNING);
            }
            if ($column->getType() === 'array') {
                $set[mb_strtoupper($keys[$i])] = json_decode($values[$i]);
            } else {
                $set[mb_strtoupper($keys[$i])] = $values[$i];
            }
        } // end foreach
        unset($i, $column);

        assert('is_array($set);');
        return $set;
    }
}

?>