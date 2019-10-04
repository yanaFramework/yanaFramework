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

namespace Plugins\DbTools;

/**
 * Table information.
 *
 * Exports as (example):
 *
 * Array
 * (
 *     [name] => foo
 *     [comment] => table of foos
 *     [length] => 6
 *     [primarykey] => foo_id
 *     [foreignkeys] => Array
 *         (
 *             [0] => Array
 *                 (
 *                     [column] => bar_id
 *                     [foreigntable] => bar
 *                     [foreigncolumn] => bar_id
 *                 )
 *
 *         )
 *     -- columns --
 * )
 *
 * Note: you should set the column's properties
 * before you add them to the table and set
 * the table properties after adding the columns.
 *
 * If you do so, properties that are inherited
 * to or from some columns, like the primary key,
 * are applied automatically.
 * Otherwise you will have to do it yourself.
 *
 * @package     yana
 * @subpackage  database
 * @ignore
 */
class InfoTable extends \Yana\Core\AbstractObject
{

    /**
     * @var string
     */
    private $primaryKey = null;

    /**
     * @var string
     */
    private $comment = null;

    /**
     * @var string
     */
    private $table = null;

    /**
     * @var array
     */
    private $foreignKeys = array();

    /**
     * @var array
     */
    private $init = array();

    /**
     * @var array
     */
    private $columns = array();

    /**
     * This function creates a new instance of this class.
     *
     * @param   string  $table table name
     */
    public function __construct($table)
    {
        assert('is_string($table); // Wrong type for argument 1. String expected.');
        $this->table = (string) $table;
    }

    /**
     * Returns table name on success and bool(false) on error.
     *
     * @return  string|bool(false)
     */
    public function getName()
    {
        return (is_string($this->table)) ? $this->table : false;
    }

    /**
     * get initialization record
     *
     * Returns a numeric list of sql statements, which ought to be run, when the table is created,
     * or bool(false) on error.
     *
     * @return  array|bool(false)
     * @since   2.9.7
     */
    public function getInit()
    {
        return (is_array($this->init)) ? $this->init : false;
    }

    /**
     * Set initialization record.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * Note: this does not check the syntax of the statements.
     *
     * @param   array  $init  numeric list of sql statements
     * @return  \Plugins\DbTools\InfoTable
     */
    public function setInit(array $init)
    {
        $this->init = array_values($init);
        return $this;
    }

    /**
     * eturns the comment as a string or bool(false) on error.
     *
     * @return  string|bool(false)
     */
    public function getComment()
    {
        return (is_string($this->comment)) ? $this->comment : false;
    }

    /**
     * Set comment.
     *
     * @param   string  $comment  set comment
     * @return  \Plugins\DbTools\InfoTable
     */
    public function setComment($comment)
    {
        assert('is_string($comment); // Wrong type for argument 1. String expected.');
        $this->comment = (string) $comment;
        return $this;
    }

    /**
     * Get the name of the primary key.
     *
     * Returns the name of the primary key as a lower-cased string,
     * or bool(false) on error.
     *
     * @return  string|bool(false)
     */
    public function getPrimaryKey()
    {
        return (is_string($this->primaryKey)) ? $this->primaryKey : false;
    }

    /**
     * Set primary key.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @param   string  $column  name of column containing the primary key
     * @return  bool
     */
    public function setPrimaryKey($column)
    {
        assert('is_string($column); // Wrong type for argument 1. String expected.');
        $column = mb_strtolower($column);

        if (!isset($this->columns[$column])) {
            $message = "No such column '{$column}'.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            \Yana\Log\LogManager::getLogger()->addLog($message, $level);
            return false;

        } else {
            $this->columns[$column]->setPrimaryKey(true);
            $this->primaryKey = $column;
            return true;
        }
    }

    /**
     * Get array of foreign keys.
     *
     * Returns bool(false) on error.
     *
     * @return  array|bool(false)
     */
    public function getForeignKeys()
    {
        return (is_array($this->foreignKeys)) ? $this->foreignKeys : false;
    }

    /**
     * Set a foreign key constraint.
     *
     * Sets a foreign key constraint on $column.
     * If $foreignColumn is not set, it is assumed,
     * the name of the referenced column is the same
     * as $column.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @param   string  $column         name of column in source table
     * @param   string  $foreignTable   name of destination table
     * @param   string  $foreignColumn  name of column in destination table
     * @return  bool
     */
    public function setForeignKey($column, $foreignTable, $foreignColumn = "")
    {
        assert('is_string($column); // Wrong type for argument 1. String expected.');
        assert('is_string($foreignTable); // Wrong type for argument 2. String expected.');
        assert('is_string($foreignColumn); // Wrong type for argument 3. String expected.');

        /* apply default value */
        $column = mb_strtolower($column);
        $foreignTable = mb_strtolower($foreignTable);
        $foreignColumn = mb_strtolower($foreignColumn);
        if ($foreignColumn === "") {
            $foreignColumn = $column;
        }

        // Error: Foreign key set on a target column that does not exist
        if (!isset($this->columns[$column])) {
            $message = "No such column '{$column}'.";
            $level = \Yana\Log\TypeEnumeration::ERROR;
            \Yana\Log\LogManager::getLogger()->addLog($message, $level);
            return false;
        }

        $this->columns[$column]->setForeignKey(true);
        $this->columns[$column]->setReference($foreignTable, $foreignColumn);

        $this->foreignKeys[] = array(
            'column' => $column,
            'foreigntable' => $foreignTable,
            'foreigncolumn' => $foreignColumn
        );
        return true;
    }

    /**
     * Add column object.
     *
     * @param   \Plugins\DbTools\InfoColumn  $column  info object
     * @return  bool
     */
    public function addColumn(\Plugins\DbTools\InfoColumn $column)
    {
        $this->columns[$column->getName()] = $column;
        $column->setTable($this->table);
        $reference = $column->getReference();
        if ($column->isPrimaryKey()) {
            return $this->setPrimaryKey($column->getName());

        } elseif ($column->isForeignKey() && is_array($reference)) {
            return $this->setForeignKey($column->getName(), $reference[0], $reference[1]);

        }
        return true;
    }

    /**
     * Returns a numeric array of \Plugins\DbTools\InfoColumn objects.
     *
     * @return  \Plugins\DbTools\InfoColumn[]
     */
    public function getColumns()
    {
        assert('is_array($this->columns);');
        return $this->columns;
    }

    /**
     * export object as associative array
     *
     * @return  array
     */
    public function toArray()
    {
        $array = array(
            'name' => $this->table,
            'comment' => $this->comment,
            'length' => count($this->columns),
            'primarykey' => $this->primaryKey,
            'init' => $this->init,
            'foreignkeys' => $this->foreignKeys
        );
        foreach ($this->columns as $column)
        {
            $array[] = $column->toArray();
        }
        return $array;
    }

}

?>