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
 * column information
 *
 * exports as (example):
 *
 * Array
 * (
 *     [table] => foo
 *     [name] => foo_id
 *     [type] => int
 *     [nullable] =>
 *     [primarykey] => 1
 *     [unique] =>
 *     [index] =>
 *     [auto] => 1
 *     [default] =>
 *     [unsigned] =>
 *     [zerofill] =>
 *     [comment] => id
 *     [length] => 8
 *     [foreignkey] =>
 *     [references] =>
 *     [update] => 1
 *     [insert] => 1
 *     [select] => 1
 * )
 *
 * @package     yana
 * @subpackage  database
 * @ignore
 */
class InfoColumn extends \Yana\Core\StdObject
{
    /**#@+
     * @access  private
     */

    /**
     * table name
     *
     * This can be set automatically and may be ignored.
     *
     * @var string
     */
    private $table = "";

    /**
     * column name
     *
     * mandatory
     *
     * @var string
     */
    private $name = "";

    /**
     * type of data
     *
     * mandatory
     *
     * @var string
     */
    private $type = "";

    /**
     * NULL allowed? (true = yes, false = no)
     *
     * mandatory
     *
     * @var bool
     */
    private $nullable = true;

    /**
     * is the primary key? (true = yes, false = no)
     *
     * This can be set automatically and may be ignored.
     *
     * @var bool
     */
    private $primaryKey = false;

    /**
     * has a unique constraint? (true = yes, false = no)
     *
     * mandatory
     *
     * @var bool
     */
    private $unique = false;

    /**
     * use index on this column? (true = yes, false = no)
     *
     * optional
     *
     * @var bool
     */
    private $index = false;

    /**
     * is auto-increment, or auto-generated value? (true = yes, false = no)
     *
     * optional
     *
     * @var bool
     */
    private $auto = false;

    /**
     * default value
     *
     * optional
     *
     * @var mixed
     */
    private $default = null;

    /**
     * is unsigned integer? (true = yes, false = no)
     *
     * optional
     *
     * @var bool
     */
    private $unsigned = false;

    /**
     * use zerfofill on integer? (true = yes, false = no)
     *
     * optional
     *
     * @var bool
     */
    private $zerofill = false;

    /**
     * comment or name of column
     *
     * optional
     *
     * @var string
     */
    private $comment = "";

    /**
     * maximum length (interpretation depends on type)
     *
     * optional
     *
     * @var int
     */
    private $length = null;

    /**
     * is a foreign key? (true = yes, false = no)
     *
     * This can be set automatically and may be ignored.
     *
     * @var bool
     */
    private $foreignKey = false;

    /**
     * is updatable? (true = yes, false = no)
     *
     * optional
     *
     * @var bool
     */
    private $update = true;

    /**
     * allow new values to be created? (true = yes, false = no)
     *
     * optional
     *
     * @var bool
     */
    private $insert = true;

    /**
     * show this column? (true = visible, false = hidden)
     *
     * optional
     *
     * @var bool
     */
    private $select = true;

    /**
     * foreign key references
     *
     * optional
     *
     * @var array
     */
    private $references = null;

    /**#@-*/

    /**
     * Constructor
     *
     * This function creates a new instance of this class.
     *
     * @param   string  $column  column name
     * @access  public
     */
    public function __construct($column = null)
    {
        assert('is_null($column) || is_string($column); // Wrong type for argument 1. String expected');
        $this->setName($column);
    }

    /**
     * set data type
     *
     * @access  public
     * @param   string  $name  Set the type of a column
     */
    public function setType($name)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        $this->type = mb_strtolower("$name");
    }

    /**
     * get type of data
     *
     * Returns table name on success and bool(false) on error.
     *
     * @access  public
     * @return  string
     */
    public function getType()
    {
        assert('is_string($this->type); // Member "type" is expected to be a string');
        return (string) $this->type;
    }

    /**
     * set column nullable
     *
     * @access  public
     * @param   bool  $isNullable  defined if the column is nullable or not (true = nullable, false = not nullable)
     */
    public function setNullable($isNullable)
    {
        assert('is_bool($isNullable); // Wrong type for argument 1. Boolean expected');
        if ($isNullable) {
            $this->nullable = true;

        } else {
            $this->nullable = false;

        }
    }

    /**
     * check if column is nullable
     *
     * @access  public
     * @return  bool
     */
    public function isNullable()
    {
        assert('is_bool($this->nullable); // Member "nullable" is expected to be a boolean');
        if ($this->nullable) {
            return true;

        } else {
            return false;

        }
    }

    /**
     * set primary key
     *
     * @access  public
     * @param   bool  $isPrimaryKey  set "true" if the column is a primary key otherweise "false"
     */
    public function setPrimaryKey($isPrimaryKey)
    {
        assert('is_bool($isPrimaryKey); // Wrong type for argument 1. Boolean expected');
        if ($isPrimaryKey) {
            $this->primaryKey = true;

        } else {
            $this->primaryKey = false;

        }
    }

    /**
     * check if column is primary key
     *
     * @access  public
     * @return  bool
     */
    public function isPrimaryKey()
    {
        assert('is_bool($this->primaryKey); // Member "primaryKey" is expected to be a boolean');
        if ($this->primaryKey) {
            return true;

        } else {
            return false;

        }
    }

    /**
     * set foreign key
     *
     * @access  public
     * @param   bool  $isForeignKey  set "true" if the column is a foreign key otherweise "false"
     */
    public function setForeignKey($isForeignKey)
    {
        assert('is_bool($isForeignKey); // Wrong type for argument 1. Boolean expected');
        if ($isForeignKey) {
            $this->foreignKey = true;

        } else {
            $this->foreignKey = false;

        }
    }

    /**
     * check if column is foreign key
     *
     * @access  public
     * @return  bool
     */
    public function isForeignKey()
    {
        assert('is_bool($this->foreignKey); // Member "foreignKey" is expected to be a boolean');
        if ($this->foreignKey) {
            return true;

        } else {
            return false;

        }
    }

    /**
     * set unique constraint
     *
     * @access  public
     * @param   bool  $isUnique  set the current column as unique (true = unique otherweise false)
     * @return  bool
     */
    public function setUnique($isUnique)
    {
        assert('is_bool($isUnique); // Wrong type for argument 1. Boolean expected');
        if ($isUnique) {
            $this->unique = true;

        } else {
            $this->unique = false;

        }
    }

    /**
     * check if column is unique
     *
     * @access  public
     * @return  bool
     */
    public function isUnique()
    {
        assert('is_bool($this->unique); // Member "unique" is expected to be a boolean');
        if ($this->unique) {
            return true;

        } else {
            return false;

        }
    }

    /**
     * set index on column
     *
     * @access  public
     * @param   bool  $hasIndex  set index on column
     * @return  bool
     */
    public function setIndex($hasIndex)
    {
        assert('is_bool($hasIndex); // Wrong type for argument 1. Boolean expected');
        if ($hasIndex) {
            $this->index = true;

        } else {
            $this->index = false;

        }
    }

    /**
     * check if column has index
     *
     * @access  public
     * @return  bool
     */
    public function hasIndex()
    {
        assert('is_bool($this->index); // Member "index" is expected to be a boolean');
        if ($this->index) {
            return true;

        } else {
            return false;

        }
    }

    /**
     * set auto-increment
     *
     * @access  public
     * @param   bool  $isAuto  set auto-increment
     */
    public function setAuto($isAuto)
    {
        assert('is_bool($isAuto); // Wrong type for argument 1. Boolean expected');
        if ($isAuto) {
            $this->auto = true;

        } else {
            $this->auto = false;

        }
        return true;
    }

    /**
     * check if column is auto-increment
     *
     * @access  public
     * @return  bool
     */
    public function isAuto()
    {
        assert('is_bool($this->auto); // Member "auto" is expected to be a boolean');
        if ($this->auto) {
            return true;

        } else {
            return false;

        }
    }

    /**
     * set if column is updatable
     *
     * @access  public
     * @param   bool  $isUpdatable  set if column is updatable
     */
    public function setUpdate($isUpdatable)
    {
        assert('is_bool($isUpdatable); // Wrong type for argument 1. Boolean expected');
        if ($isUpdatable) {
            $this->update = true;

        } else {
            $this->update = false;

        }
    }

    /**
     * check if column is updatable
     *
     * @access  public
     * @return  bool
     */
    public function isUpdatable()
    {
        assert('is_bool($this->update); // Member "update" is expected to be a boolean');
        if ($this->update) {
            return true;

        } else {
            return false;

        }
    }

    /**
     * set if column is selectable
     *
     * @access  public
     * @param   bool  $isSelectable  set if column is selectable
     */
    public function setSelect($isSelectable)
    {
        assert('is_bool($isSelectable); // Wrong type for argument 1. Boolean expected');
        if ($isSelectable) {
            $this->select = true;

        } else {
            $this->select = false;

        }
    }

    /**
     * check if column is selectable
     *
     * @access  public
     * @return  bool
     */
    public function isSelectable()
    {
        assert('is_bool($this->select); // Member "select" is expected to be a boolean');
        if ($this->select) {
            return true;

        } else {
            return false;

        }
    }

    /**
     * set if column is insertable
     *
     * @access  public
     * @param   bool  $isInsertable  set if column is insertable
     * @return  bool
     */
    public function setInsert($isInsertable)
    {
        assert('is_bool($isInsertable); // Wrong type for argument 1. Boolean expected');
        if ($isInsertable) {
            $this->insert = true;

        } else {
            $this->insert = false;

        }
    }

    /**
     * check if column is insertable
     *
     * @access  public
     * @return  bool
     */
    public function isInsertable()
    {
        assert('is_bool($this->insert); // Member "insert" is expected to be a boolean');
        if ($this->insert) {
            return true;

        } else {
            return false;

        }
    }

    /**
     * set to unsigned number
     *
     * @access  public
     * @param   bool  $isUnsigned   set to unsigned number
     * @return  bool
     */
    public function setUnsigned($isUnsigned)
    {
        assert('is_bool($isUnsigned); // Wrong type for argument 1. Boolean expected');
        if ($isUnsigned) {
            $this->unsigned = true;

        } else {
            $this->unsigned = false;

        }
    }

    /**
     * check if column is unsigned number
     *
     * @access  public
     * @return  bool
     */
    public function isUnsigned()
    {
        assert('is_bool($this->unsigned); // Member "unsigned" is expected to be a boolean');
        if ($this->unsigned) {
            return true;

        } else {
            return false;

        }
    }

    /**
     * set to zerofill number
     *
     * @access  public
     * @param   bool  $isZerofill  set to zerofill number
     * @return  bool
     */
    public function setZerofill($isZerofill)
    {
        assert('is_bool($isZerofill); // Wrong type for argument 1. Boolean expected');
        if ($isZerofill) {
            $this->zerofill = true;

        } else {
            $this->zerofill = false;

        }
    }

    /**
     * check if column is zerofill number
     *
     * @access  public
     * @return  bool
     */
    public function isZerofill()
    {
        assert('is_bool($this->zerofill); // Member "zerofill" is expected to be a boolean');
        if ($this->zerofill) {
            return true;

        } else {
            return false;

        }
    }

    /**
     * set table name
     *
     * @access  public
     * @param   string  $name  table name
     * @return  bool
     */
    public function setTable($name)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected.');
        $this->table = (string) $name;
    }

    /**
     * get table name
     *
     * Returns table name.
     *
     * @access  public
     * @return  string
     */
    public function getTable()
    {
        assert('is_string($this->table); // Member "table" is expected to be a string');
        return (string) $this->table;
    }

    /**
     * set column name
     *
     * @access  public
     * @param   string  $name  column name
     * @return  bool
     */
    public function setName($name)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        $this->name = mb_strtolower("$name");
    }

    /**
     * get column name
     *
     * Returns column name.
     *
     * @access  public
     * @return  string
     */
    public function getName()
    {
        assert('is_string($this->name); // Member "name" is expected to be a string');
        return mb_strtolower($this->name);
    }

    /**
     * get default value
     *
     * Returns the default value,
     * or NULL (not bool(false)!) on error.
     *
     * @access  public
     * @return  mixed|NULL
     */
    public function getDefault()
    {
        if (isset($this->default)) {
            return $this->default;

        } else {
            return null;
        }
    }

    /**
     * set default value
     *
     * @access  public
     * @param   string  $default  default value
     */
    public function setDefault($default)
    {
        assert('is_string($default); // Wrong type for argument 1. String expected');
        $this->default = $default;
    }

    /**
     * get comment
     *
     * Returns the comment as a string.
     *
     * @access  public
     * @return  string
     */
    public function getComment()
    {
        assert('is_string($this->comment); // Member "comment" is expected to be a string');
        return (string) $this->comment;
    }

    /**
     * set comment
     *
     * @access  public
     * @param   string  $comment  set comment
     */
    public function setComment($comment)
    {
        assert('is_string($comment); // Wrong type for argument 1. String expected');
        $this->comment = "$comment";
    }

    /**
     * get length
     *
     * Returns the length as an integer,
     * or bool(false) on error.
     *
     * @access  public
     * @return  int|bool(false)
     */
    public function getLength()
    {
        if (is_int($this->length)) {
            return (int) $this->length;

        } else {
            return false;
        }
    }

    /**
     * set length
     *
     * @access  public
     * @param   int  $length  length of the column
     */
    public function setLength($length)
    {
        assert('is_int($length); // Wrong type for argument 1. Integer expected');
        if ($length > 0) {
            $this->length = (int) $length;

        } else {
            $this->length = null;

        }
    }

    /**
     * get reference to foreign table
     *
     * Returns array with name of destination table and name of target column.
     * If no reference is set, NULL is returned.
     *
     * @access  public
     * @return  array
     */
    public function getReference()
    {
        return $this->references;
    }

    /**
     * add reference to foreign table
     *
     * @access  public
     * @param   string  $foreignTable   name of destination table
     * @param   string  $foreignColumn  name of column in destination table
     */
    public function setReference($foreignTable, $foreignColumn)
    {
        assert('is_string($foreignTable); // Wrong type for argument 1. String expected');
        assert('is_string($foreignColumn); // Wrong type for argument 2. String expected');
        $this->foreignKey = true;
        $this->references = array("$foreignTable", mb_strtolower("$foreignColumn"));
    }

    /**
     * export object as associative array
     *
     * @access  public
     * @return  array
     */
    public function toArray()
    {
        $array = get_object_vars($this);
        $array = array_change_key_case($array, CASE_LOWER);
        return $array;
    }
}
?>