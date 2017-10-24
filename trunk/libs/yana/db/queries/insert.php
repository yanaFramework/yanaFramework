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
 * Database query builder
 *
 * This class is a query builder that can be used to build SQL statements to insert new rows
 * in a database-table. The inserted row must not exist.
 *
 * Note: this class does NOT untaint input data for you.
 *
 * @package     yana
 * @subpackage  db
 */
class Insert extends \Yana\Db\Queries\AbstractQuery
{

    /**
     * @var int
     * @ignore
     */
    protected $type = \Yana\Db\Queries\TypeEnumeration::INSERT;

    /**
     * @var array
     * @ignore
     */
    protected $column = array();

    /**
     * @var array
     */
    protected $profile = array();

    /**
     * @var array
     * @ignore
     */
    protected $values = null;

    /**
     * @var array
     * @ignore
     */
    protected $queue = array();

    /**
     * @var array
     * @ignore
     */
    protected $files = array();

    /**
     * @var \Yana\Db\Helpers\IsSanitizer
     */
    private $_sanitizer = null;

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
        parent::__construct($database);
        $this->_sanitizer = new \Yana\Db\Helpers\ValueSanitizer($this->getDatabase()->getDBMS());
    }

    /**
     * @param \Yana\Db\Helpers\IsSanitizer $sanitizer
     * @return \Yana\Db\Queries\AbstractQuery
     */
    public function setSanitizer(\Yana\Db\Helpers\IsSanitizer $sanitizer)
    {
        $this->_sanitizer = $sanitizer;

        return $this;
    }

    /**
     * Returns the sanitizer algorithm used to clean input values.
     *
     * If no sanitizer has been set, it will create one.
     *
     * @return \Yana\Db\Helpers\IsSanitizer
     */
    protected function _getSanitizer()
    {
        return $this->_sanitizer;
    }

    /**
     * <<magic>> Called to create copies of the object when using the "clone" keyword.
     *
     * It will empty the query queue for the cloned object.
     * Note: the cloned object will use the same database connection, since
     * connection are resources, which may not be cloned.
     *
     */
    public function __clone()
    {
        $this->queue = array();
    }

    /**
     * Reset query.
     *
     * Resets all properties of the query object, except
     * for the database connection and the properties
     * "table", "type", "useInheritance".
     *
     * This function allows you to "recycle" a query object
     * and reuse it without creating another one. This can
     * help to improve the performance of your application.
     *
     * @return  \Yana\Db\Queries\Insert
     */
    public function resetQuery()
    {
        parent::resetQuery();
        $this->profile = array();
        $this->values  = null;
        $this->queue   = array();
        $this->files   = array();
        return $this;
    }

    /**
     * set value(s) for current query
     *
     * This takes an associative array, where the keys are column names.
     * When updating a single column, it may also be a scalar value.
     *
     * @param   mixed  $values  value(s) for current query
     * @return  \Yana\Db\Queries\Insert
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException          if a given argument is invalid
     * @throws  \Yana\Db\Queries\Exceptions\InvalidPrimaryKeyException  when the primary key is invalid or ambigious
     * @throws  \Yana\Db\Queries\Exceptions\ConstraintException         when a constraint violation is detected
     * @throws  \Yana\Db\Queries\Exceptions\InvalidResultTypeException  when trying to insert anything but a row.
     */
    public function setValues($values)
    {
        $this->id = null;
        /*
         * 1.a) lowercase array keys
         */
        if (is_array($values)) {
            $values = \Yana\Util\Hashtable::changeCase($values, CASE_LOWER);

        } elseif ($this->type === \Yana\Db\Queries\TypeEnumeration::INSERT) {
            throw new \Yana\Db\Queries\Exceptions\InvalidResultTypeException("Invalid type. " .
                "Database values must be an array for insert-statements.");

        /*
         * 1.b) error - wrong argument type
         */
        } elseif (!is_scalar($values)) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException("Invalid type. " .
                "Database values must be an array or scalar.");
        }
        $tableName = $this->getTable();
        assert('!empty($tableName); // Cannot set values - need to set table first!');

        /*
         * 1.d) handle table inheritance
         */
        if ($this->getParent()) {
            assert('!isset($columns); // Cannot redeclare var $columns');
            $columns = $this->getColumns();
            if (empty($columns)) {
                assert('!isset($columnName); // Cannot redeclare var $columnName');
                foreach (array_keys($values) as $columnName)
                {
                    $columns[] = array($tableName, $columnName);
                }
                unset($columnName);
            }

            assert('!isset($column); // Cannot redeclare var $column');
            foreach ($columns as $column)
            {
                assert('is_array($column); // Invalid property "column". Two-dimensional array expected.');
                assert('!isset($parent); // Cannot redeclare var $parent');
                assert('!isset($columnName); // Cannot redeclare var $columnName');
                $columnName = $column[1];
                $parent = $this->getParentByColumn($columnName);
                if (false !== $parent && isset($values[$columnName])) {
                    $this->_appendValue($parent, $columnName, $values[$columnName]);
                    unset($values[$columnName]);
                }
                unset($parent, $columnName);
            }
            unset($column, $columns);
        }

        $table = $this->currentTable();

        /*
         * 2.a) inserting a row
         */
        assert('!isset($primaryKey); // Cannot redeclare var $primaryKey');
        $primaryKey = $table->getPrimaryKey();

        // copy primary key to row property
        if ($this->expectedResult === \Yana\Db\ResultEnumeration::TABLE && isset($values[$primaryKey])) {
            $this->setRow($values[$primaryKey]);
        }

        assert('!isset($isInsert); // Cannot redeclare var $isInsert');
        $isInsert = false;
        if ($this->type === \Yana\Db\Queries\TypeEnumeration::INSERT) {
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
                    $values[$primaryKey] = $this->_getSanitizer()->sanitizeValueByColumn($column, $this->row, $this->files);
                } else {
                    $message = "Cannot insert a row without a primary key. Operation aborted.";
                    throw new \Yana\Db\Queries\Exceptions\InvalidPrimaryKeyException($message);
                }
            } elseif ($this->row !== '*' && strcasecmp($this->row, $values[$primaryKey]) !== 0) {
                assert('!isset($message); // Cannot redeclare $message');
                assert('!isset($level); // Cannot redeclare $level');
                $message = "Cannot set values. The primary key is ambigious.\n\t\t" .
                    "The primary key has been set via " . __CLASS__ . "->setRow() or " .
                    __CLASS__ . "->setKey() to '" . $this->row . "'.\n\t\t" .
                    "However, the primary key provided with " . __CLASS__ . "->setValues() is '" .
                    $values[$primaryKey] . "'.";
                $level = \Yana\Log\TypeEnumeration::WARNING;
                throw new \Yana\Db\Queries\Exceptions\ConstraintException($message, $level);
            }

        } // end INSERT-statement

        switch ($this->getExpectedResult())
        {
            // INSERT and UPDATE statements
            case \Yana\Db\ResultEnumeration::ROW:
                assert('is_array($values); // Row must be an array');
                // upper-case primary key
                if (isset($values[$primaryKey])) {
                    $values[$primaryKey] = mb_strtoupper($values[$primaryKey]);
                }
                // check if row is valid
                $values = $this->_getSanitizer()->sanitizeRowByTable($table, $values, $isInsert, $this->files);
            break;

            // UPDATE statements only
            default:
                if ($isInsert) {
                    // this point should be impossible to reach
                    $_message = "You may only insert rows - not cells or columns.";
                    throw new \Yana\Db\Queries\Exceptions\InvalidResultTypeException($_message);
                }
                assert('!$isInsert; // May only insert rows, not tables, cells or columns');
                if (empty($this->arrayAddress) && isset($this->column[0]) && is_array($this->column[0])) {
                    assert('count($this->column) === 1;');
                    assert('count($this->column[0]) === 2;');
                    assert('isset($this->column[0][1]);');
                    assert('$this->tableName === $this->column[0][0];');
                    assert('$table->isColumn($this->column[0][1]);');
                    assert('!isset($column); // Cannot redeclare var $column');
                    $column = $table->getColumn($this->column[0][1]);
                    assert('$column instanceof \Yana\Db\Ddl\Column;');
                    $values = $this->_getSanitizer()->sanitizeValueByColumn($column, $values, $this->files);
                    unset($column);
                }
            break;
        }
        unset($primaryKey);

        /*
         * 3) error - access denied
         */
        if (!$this->checkProfile($values)) {
            assert('!isset($message); // Cannot redeclare $message');
            assert('!isset($level); // Cannot redeclare $level');
            $message = "Cannot set values. Profile constraint mismatch.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Db\Queries\Exceptions\ConstraintException($message, $level);
        }

        /*
         * 4) input is valid - update values
         */
        $this->values =& $values;
        return $this;
    }

    /**
     * Get the list of values.
     *
     * If none are available, NULL (not bool(false)!) is returned.
     *
     * @return  mixed
     */
    public function &getValues()
    {
        return $this->values;
    }

    /**
     * Append value to query queue.
     *
     * @param   string  $table   table name
     * @param   string  $column  column name
     * @param   mixed   $value   value
     * @throws  \Yana\Db\Queries\Exceptions\DuplicateValueException  value cannot be appended because another value is already
     *                                                    set for the given table and column
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
            assert('!isset($level); // Cannot redeclare $level');
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Db\Queries\Exceptions\DuplicateValueException($column, $level);
        }
        // append value to queue
        $this->queue[$table][$column] = $value;
    }

    /**
     * Check profile constraint.
     *
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

        $builder = new \Yana\ApplicationBuilder();
        $application = $builder->buildApplication();
        $security = $application->getSecurity();
        switch (true)
        {
            case isset($value['profile_id']) && $security->checkRules($value['profile_id']) !== true:
            case $security->checkRules() !== true:
                return false;
            default:
                return true;
        }
    }

    /**
     * Sends the query to the database server and returns a result-object.
     *
     * Note: This function may throw a number of exception.
     * If you wish to handle them all at once, catch a {@see \Yana\Db\Queries\Exceptions\QueryException}.
     *
     * @return  \Yana\Db\IsResult
     * @throws  \Yana\Db\Queries\Exceptions\TableNotFoundException      when the table does not exist
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException          if a given argument is invalid
     * @throws  \Yana\Db\Queries\Exceptions\InvalidPrimaryKeyException  when the primary key is invalid or ambigious
     * @throws  \Yana\Db\Queries\Exceptions\ConstraintException         when a constraint violation is detected
     * @ignore
     */
    public function sendQuery()
    {
        // send query
        $result = parent::sendQuery();

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
                $result = $this->db->sendQueryObject($this);
            }
            unset($table, $values);
            // re-activate inheritance
            $this->useInheritance($prevSetting);
            $this->queue = array();
        }

        // upload new files
        $this->deleteFiles($this->files);
        $this->uploadFiles($this->files);

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
     * @param   array  $files  list of files to upload
     * @return  \Yana\Db\Queries\Insert
     * @ignore
     */
    protected function uploadFiles(array $files = array())
    {
        foreach ($files as $file)
        {
            /* @var $column \Yana\Db\Ddl\Column */
            $column = $file['column'];
            $columnName = $column->getName();
            $fileId = $this->values[$columnName];
            if (!empty($fileId)) {
                assert('!isset($helper); // Cannot redeclare var $helper');
                if ($column->getType() === 'image') {
                    $helper = new \Yana\Db\Binaries\Uploads\FileUploader();
                    $helper->upload($file, $fileId, $column->getImageSettings());
                } else {
                    $helper = new \Yana\Db\Binaries\Uploads\ImageUploader();
                    $helper->upload($file, $fileId);
                }
                unset($helper);
            }
        }
        return $this;
    }

    /**
     * Get unique id.
     *
     * @return  string
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
     * Build a SQL-query.
     *
     * @param   string $stmt sql statement
     * @return  string
     */
    protected function toString($stmt = "INSERT INTO %TABLE% (%KEYS%) VALUES (%VALUES%)")
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
            assert('!isset($keys); // Cannot redeclare $keys');
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

}

?>