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

namespace Yana\Db\Ddl;

/**
 * Database foreign-key constraints.
 *
 * Foreign-key constraints are meant to ensure referential integrity between tables.
 * This feature is not supported by all DBMS but may be emulated using software.
 *
 * Each foreign-key constists at least of a source and a target.
 * Note that the types of the source columns of a foreign-key depend on the type of the target
 * columns.
 *
 * @package     yana
 * @subpackage  db
 */
class ForeignKey extends \Yana\Db\Ddl\AbstractUnnamedObject
{

    /**
     * tag name for persistance mapping: object <-> XDDL
     *
     * @var  string
     * @ignore
     */
    protected $xddlTag = "foreign";

    /**
     * attributes for persistance mapping: object <-> XDDL
     *
     * @var  array
     * @ignore
     */
    protected $xddlAttributes = array(
        'name'       => array('name',        'nmtoken'),
        'table'      => array('targetTable', 'nmtoken'),
        'match'      => array('_match',      'string'),
        'ondelete'   => array('_onDelete',   'string'),
        'onupdate'   => array('_onUpdate',   'string'),
        'deferrable' => array('deferrable',  'bool')
    );

    /**
     * tags for persistance mapping: object <-> XDDL
     *
     * @var  array
     * @ignore
     */
    protected $xddlTags = array(
        'description' => array('description', 'string'),
        'key'         => array('columns',     'array', null, 'name', 'column')
    );

    /**
     * @var  string
     * @ignore
     */
    protected $description = null;

    /**
     * @var  string
     * @ignore
     */
    protected $targetTable = null;

    /**
     * @var  array
     * @ignore
     */
    protected $columns = array();

    /**
     * @var  int
     * @ignore
     */
    protected $match = \Yana\Db\Ddl\KeyMatchStrategyEnumeration::SIMPLE;

    /**
     * @var  int
     * @ignore
     */
    protected $onDelete = \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::NOACTION;

    /**
     * @var  int
     * @ignore
     */
    protected $onUpdate = \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::NOACTION;

    /**
     * @var  bool
     * @ignore
     */
    protected $deferrable = null;

    /**
     * @var  \Yana\Db\Ddl\Table
     * @ignore
     */
    protected $parent = null;

    /**
     * properties for persistance mapping: object <-> XDDL
     *
     * @var  string
     * @ignore
     */
    protected $_match = null;

    /**
     * properties for persistance mapping: object <-> XDDL
     *
     * @var  string
     * @ignore
     */
    protected $_onDelete = null;

    /**
     * properties for persistance mapping: object <-> XDDL
     *
     * @var  string
     * @ignore
     */
    protected $_onUpdate = null;

    /**
     * Initialize instance.
     *
     * @param  string    $name    foreign key name
     * @param  \Yana\Db\Ddl\Table  $parent  parent table
     */
    public function __construct(string $name = "", ?\Yana\Db\Ddl\Table $parent = null)
    {
        parent::__construct($name);
        $this->parent = $parent;
    }

    /**
     * Get parent table.
     *
     * @return  \Yana\Db\Ddl\Table
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Get name of target table.
     *
     * The target table is where the foreign-keys refer to.
     *
     * @return  string
     */
    public function getTargetTable()
    {
        if (is_string($this->targetTable)) {
            return $this->targetTable;
        } else {
            return null;
        }
    }

    /**
     * Set name of target table.
     *
     * The target table is where the foreign-keys refer to.
     *
     * Not that reseting the name of the target table also resets the list
     * of referencing columns.
     *
     * @param   string  $name  name of target table
     * @return  \Yana\Db\Ddl\ForeignKey
     */
    public function setTargetTable($name)
    {
        assert(is_string($name), 'Invalid argument $name: string expected');
        if (empty($name)) {
            $this->targetTable = null;
        } else {
            $this->targetTable = mb_strtolower("$name");
        }
        return $this;
    }

    /**
     * Get name of the source .
     *
     * The source table is where the foreign-key-constraint is defined.
     *
     * @return  string
     */
    public function getSourceTable()
    {
        if (isset($this->parent)) {
            return $this->parent->getName();
        } else {
            return null;
        }
    }

    /**
     * Get list of referencing columns.
     *
     * Returns an associative array of referencing columns, where the keys are
     * the source columns and the values are the target columns.
     *
     * @return  array
     */
    public function getColumns()
    {
        assert(is_array($this->columns), 'member "columns" is expected to be an array');
        return $this->columns;
    }

    /**
     * Set column references.
     *
     * Sets the list of references for the primary key constraint (and
     * overwrites any previous settings).
     *
     * The parameter $columns must be an associative array, where the keys are
     * the source columns and the values are the target columns.
     * If you set a target column to NULL, the primary-key of the target table
     * will be taken.
     *
     * Use an empty array to reset the column references.
     *
     * @param   array  $columns list of columns in current table
     * @return  \Yana\Db\Ddl\ForeignKey
     * @throws  \Yana\Core\Exceptions\NotFoundException  if any of the targets does not exists
     */
    public function setColumns(array $columns)
    {
        if (isset($this->parent)) {
            foreach ($columns as $column)
            {
                if (!$this->parent->isColumn($column)) {
                    $message = "No such column '$column' in table '{$this->getSourceTable()}'.";
                    throw new \Yana\Core\Exceptions\NotFoundException($message, \Yana\Log\TypeEnumeration::WARNING);
                }
            }
        }
        $this->columns = $columns;
        return $this;
    }

    /**
     * set a column reference
     *
     * Set a column in the source table to reference a key-column in the
     * target table. If you leave the second parameter off, it will be set to
     * the primary-key of the target table.
     *
     * @param   string  $source  name of source column in current table
     * @param   string  $target  name of target column in referenced table
     * @return  \Yana\Db\Ddl\ForeignKey
     * @throws  \Yana\Core\Exceptions\NotFoundException  if any of the targets does not exists
     */
    public function setColumn($source, $target = "")
    {
        assert(is_string($source), 'Invalid argument $source: String expected');
        assert(is_string($target), 'Invalid argument $target: String expected');

        $source = mb_strtolower($source);
        // set target column to primary key
        if (isset($this->parent) && (empty($target) || YANA_DB_STRICT)) {
            if (!$this->parent->isColumn($source)) {
                $message = "No such column '$source' in table '{$this->getSourceTable()}'.";
                throw new \Yana\Core\Exceptions\NotFoundException($message, \Yana\Log\TypeEnumeration::WARNING);
            }
            $targetTable = $this->getTargetTable();
            if (!is_string($targetTable)) {
                $message = "Target table is undefined.";
                throw new \Yana\Core\Exceptions\NotFoundException($message, \Yana\Log\TypeEnumeration::WARNING);
            }
            $database = $this->parent->getParent();
            if (is_null($database)) {
                $message = "Database is undefined.";
                throw new \Yana\Core\Exceptions\NotFoundException($message, \Yana\Log\TypeEnumeration::ERROR);
            }
            $table = $database->getTable($targetTable);
            if (! $table instanceof \Yana\Db\Ddl\Table) {
                $message = "No such table '$targetTable' in Database.";
                throw new \Yana\Core\Exceptions\NotFoundException($message, \Yana\Log\TypeEnumeration::WARNING);
            }
            if (empty($target)) {
                $target = $table->getPrimaryKey();
            }
            if (is_null($target)) {
                $message = "No suitable target column in table '$targetTable'.";
                throw new \Yana\Core\Exceptions\NotFoundException($message, \Yana\Log\TypeEnumeration::WARNING);
            } elseif (YANA_DB_STRICT && !$table->isColumn($target)) {
                $message = "No such column '$target' in table '$targetTable'.";
                throw new \Yana\Core\Exceptions\NotFoundException($message, \Yana\Log\TypeEnumeration::WARNING);
            }
        }

        if (empty($target)) {
            $this->columns[$source] = "";
        } else {
            $this->columns[$source] = $target;
        }
        return $this;
    }

    /**
     * Get match type.
     *
     * Returns one of the following constants:
     * <ul>
     *   <li> \Yana\Db\Ddl\KeyMatchStrategyEnumeration::SIMPLE </li>
     *   <li> \Yana\Db\Ddl\KeyMatchStrategyEnumeration::FULL </li>
     *   <li> \Yana\Db\Ddl\KeyMatchStrategyEnumeration::PARTIAL </li>
     * </ul>
     *
     * This applies to compound foreign-keys including multiple columns only.
     * The default is SIMPLE.
     *
     * SIMPLE: Any column that has a value must match (some columns may be null)
     * FULL: All columns must match
     * PARTIAL: At least one column must match
     *
     * Note that not all DBMS may implement all or any of these options.
     *
     * @return  int
     * @name    \Yana\Db\Ddl\ForeignKey::getMatch()
     */
    public function getMatch()
    {
        assert(is_int($this->match), 'Member "match" is expected to be an integer');
        return $this->match;
    }

    /**
     * Set match type.
     *
     * Parameter $match must be one of the following constants:
     * <ul>
     *   <li> \Yana\Db\Ddl\KeyMatchStrategyEnumeration::SIMPLE </li>
     *   <li> \Yana\Db\Ddl\KeyMatchStrategyEnumeration::FULL </li>
     *   <li> \Yana\Db\Ddl\KeyMatchStrategyEnumeration::PARTIAL </li>
     * </ul>
     *
     * @param   int  $match  match type
     * @return  \Yana\Db\Ddl\ForeignKey
     * @see     \Yana\Db\Ddl\ForeignKey::getMatch()
     */
    public function setMatch($match)
    {
        assert(is_numeric($match), 'Invalid argument $match: Integer expected');
        switch($match)
        {
            case \Yana\Db\Ddl\KeyMatchStrategyEnumeration::SIMPLE:
            case \Yana\Db\Ddl\KeyMatchStrategyEnumeration::PARTIAL:
            case \Yana\Db\Ddl\KeyMatchStrategyEnumeration::FULL:
                $this->match = $match;
            break;
            default:
                $this->match = \Yana\Db\Ddl\KeyMatchStrategyEnumeration::SIMPLE;
            break;
        }
        return $this;
    }

    /**
     * Get on-delete action.
     *
     * Returns one of the following constants:
     * <ul>
     *   <li> \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::NOACTION </li>
     *   <li> \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::RESTRICT </li>
     *   <li> \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::CASCADE </li>
     *   <li> \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::SETNULL </li>
     *   <li> \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::SETDEFAULT </li>
     * </ul>
     *
     * The default is NOACTION.
     *
     * <ul>
     *   <li> NOACTION: you may not set the reference in the CHILD table to a
     *        value, that has no corresponding row in the parent table </li>
     *   <li> RESTRICT: you may not change the referenced key column of the
     *        PARENT table, if there is a reference to them </li>
     *   <li> CASCADE: if the key in the parent table is deleted or updated, all
     *        referencing rows in the child table are deleted or updated as
     *        well </li>
     *   <li> SETNULL: if the row in the parent table is changed, the reference
     *        is set to null </li>
     *   <li> SETDEFAULT:  if the row in the parent table is changed, the reference
     *        is set to the default value </li>
     * </ul>
     *
     * Note that not all DBMS may implement all or any of these options:
     * <ul>
     *   <li> MySQL MyISAM implements no referential integrity at all </li>
     *   <li> SQLite has no support for referential integrity </li>
     *   <li> MySQL InnoDB implements all, but SETDEFAULT </li>
     *   <li> PostgreSQL implements all, but RESTRICT </li>
     *   <li> MSSQL implements NOACTION and CASCADE </li>
     *   <li> Oracle implements all </li>
     *   <li> IBM DB2 implements all, but SETDEFAULT (CASCADE and SETNULL are
     *        only available for ON DELETE) </li>
     *   <li> Yana Framework's FileDB implements NOACTION </li>
     * </ul>
     *
     * @return  int
     * @name    \Yana\Db\Ddl\ForeignKey::getOnDelete()
     */
    public function getOnDelete()
    {
        assert(is_int($this->onDelete), 'Member "onDelete" is expected to be an integer');
        return $this->onDelete;
    }

    /**
     * Set on-delete action.
     *
     * Param $action may be one of the following constants:
     * <ul>
     *   <li> \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::NOACTION </li>
     *   <li> \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::RESTRICT </li>
     *   <li> \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::CASCADE </li>
     *   <li> \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::SETNULL </li>
     *   <li> \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::SETDEFAULT </li>
     * </ul>
     *
     * The default is NOACTION.
     *
     * @param   int  $match  type name (allowed params are on the top of this comment)
     * @return  \Yana\Db\Ddl\ForeignKey
     * @see     \Yana\Db\Ddl\ForeignKey::getOnDelete()
     */
    public function setOnDelete($match)
    {
        assert(is_int($match), 'Invalid argument $match: Integer expected');

        switch($match)
        {
            case \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::NOACTION:
            case \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::RESTRICT:
            case \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::CASCADE:
            case \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::SETNULL:
            case \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::SETDEFAULT:
                $this->onDelete = $match;
            break;
            default:
                $this->onDelete = \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::NOACTION;
            break;
        }
        return $this;
    }

    /**
     * Get on-update action.
     *
     * Returns one of the following constants:
     * <ul>
     *   <li> \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::NOACTION </li>
     *   <li> \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::RESTRICT </li>
     *   <li> \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::CASCADE </li>
     *   <li> \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::SETNULL </li>
     *   <li> \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::SETDEFAULT </li>
     * </ul>
     *
     * The default is NOACTION.
     *
     * @return  int
     * @see     \Yana\Db\Ddl\ForeignKey::getOnDelete()
     */
    public function getOnUpdate()
    {
        assert(is_int($this->onUpdate), 'Member "onUpdate" is expected to be an integer');
        return $this->onUpdate;
    }

    /**
     * Set on-delete action.
     *
     * Param $action may be one of the following constants:
     * <ul>
     *   <li> \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::NOACTION </li>
     *   <li> \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::RESTRICT </li>
     *   <li> \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::CASCADE </li>
     *   <li> \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::SETNULL </li>
     *   <li> \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::SETDEFAULT </li>
     * </ul>
     *
     * The default is NOACTION.
     *
     * @param   int  $match  type name (allowed params are on the top of this comment)
     * @return  \Yana\Db\Ddl\ForeignKey
     * @see     \Yana\Db\Ddl\ForeignKey::getOnDelete()
     */
    public function setOnUpdate($match)
    {
        assert(is_int($match), 'Invalid argument $match: Integer expected');
        switch($match)
        {
            case \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::NOACTION:
            case \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::RESTRICT:
            case \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::CASCADE:
            case \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::SETNULL:
            case \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::SETDEFAULT:
                $this->onUpdate = $match;
            break;
            default:
                $this->onUpdate = \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::NOACTION;
            break;
        }
        return $this;
    }

    /**
     * Check wether integrity check is deferrable.
     *
     * Returns bool(true) if an integrity check is deferrable and bool(false)
     * otherwise. Deferrable means, the DBS should wait till the end of a
     * transaction before it check inserted or updated foreign keys.
     *
     * This is meant for situations, where you push data in both: the parent
     * and the child table within one transaction, or when you use circular
     * references (if supported by your DBMS).
     *
     * Note that this feature is not supported by all DBMS.
     * The default is false.
     * <ul>
     *   <li> Oracle also distinguishes between: deferrable, not deferrable,
     *        initially deferred and initially immediate </li>
     *   <li> PostgreSQL allows: deferrable, not deferrable and
     *        initially deferred </li>
     *   <li> AFAIK not supported by MySQL, IBM DB2, MSSQL and others </li>
     * </ul>
     *
     * For DB2 and others you would instead temporarily deactivate
     * constraint-checks while insterting or updating rows.
     *
     * @return  bool
     * @name    \Yana\Db\Ddl\ForeignKey::isDeferrable()
     */
    public function isDeferrable()
    {
        return !empty($this->deferrable);
    }

    /**
     * Check wether integrity check is deferrable.
     *
     * Deferrable means, the DBS should wait till the end of a transaction
     * before it check inserted or updated foreign keys.
     *
     * @param   bool  $isDeferrable  true = is deferable, false = is not deferable
     * @return  \Yana\Db\Ddl\ForeignKey
     * @see     \Yana\Db\Ddl\ForeignKey::isDeferrable()
     */
    public function setDeferrable($isDeferrable)
    {
        assert(is_bool($isDeferrable), 'Invalid argument $isDeferrable: Boolean expected');
        $this->deferrable = (bool) $isDeferrable;
        return $this;
    }

    /**
     * Serialize this object to XDDL.
     *
     * Returns the serialized object as a string in XML-DDL format.
     *
     * @param   \SimpleXMLElement $parentNode  parent node
     * @return  \SimpleXMLElement
     */
    public function serializeToXDDL(?\SimpleXMLElement $parentNode = null): \SimpleXMLElement
    {
        switch ($this->match)
        {
            case \Yana\Db\Ddl\KeyMatchStrategyEnumeration::PARTIAL:
                $this->_match = 'partial';
            break;
            case \Yana\Db\Ddl\KeyMatchStrategyEnumeration::FULL:
                $this->_match = 'full';
            break;
            default:
                $this->_match = 'simple';
            break;
        }
        switch ($this->onDelete)
        {
            case \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::RESTRICT:
                $this->_onDelete = 'restrict';
            break;
            case \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::CASCADE:
                $this->_onDelete = 'cascade';
            break;
            case \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::SETNULL:
                $this->_onDelete = 'set-null';
            break;
            case \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::SETDEFAULT:
                $this->_onDelete = 'set-default';
            break;
            default:
                $this->_onDelete = 'no-action';
            break;
        }
        switch ($this->_onUpdate)
        {
            case \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::RESTRICT:
                $this->_onUpdate = 'restrict';
            break;
            case \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::CASCADE:
                $this->_onUpdate = 'cascade';
            break;
            case \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::SETNULL:
                $this->_onUpdate = 'set-null';
            break;
            case \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::SETDEFAULT:
                $this->_onUpdate = 'set-default';
            break;
            default:
                $this->_onUpdate = 'no-action';
            break;
        }
        return parent::serializeToXDDL($parentNode);
    }

    /**
     * Unserialize a XDDL-node to an object.
     *
     * Returns the unserialized object.
     *
     * @param   \SimpleXMLElement  $node    XML node
     * @param   mixed              $parent  parent node (if any)
     * @return  \Yana\Db\Ddl\ForeignKey
     */
    public static function unserializeFromXDDL(\SimpleXMLElement $node, $parent = null)
    {
        $attributes = $node->attributes();
        $name = "";
        if (isset($attributes['name'])) {
            $name = (string) $attributes['name'];
        }
        $ddl = new self($name, $parent);
        $ddl->_unserializeFromXDDL($node);
        switch ($ddl->_match)
        {
            case 'partial':
                $ddl->match = \Yana\Db\Ddl\KeyMatchStrategyEnumeration::PARTIAL;
            break;
            case 'full':
                $ddl->match = \Yana\Db\Ddl\KeyMatchStrategyEnumeration::FULL;
            break;
            default:
                $ddl->match = \Yana\Db\Ddl\KeyMatchStrategyEnumeration::SIMPLE;
            break;
        }
        switch ($ddl->_onDelete)
        {
            case 'restrict':
                $ddl->onDelete = \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::RESTRICT;
            break;
            case 'cascade':
                $ddl->onDelete = \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::CASCADE;
            break;
            case 'set-null':
                $ddl->onDelete = \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::SETNULL;
            break;
            case 'set-default':
                $ddl->onDelete = \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::SETDEFAULT;
            break;
            default:
                $ddl->onDelete = \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::NOACTION;
            break;
        }
        switch ($ddl->_onUpdate)
        {
            case 'restrict':
                $ddl->onUpdate = \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::RESTRICT;
            break;
            case 'cascade':
                $ddl->onUpdate = \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::CASCADE;
            break;
            case 'set-null':
                $ddl->onUpdate = \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::SETNULL;
            break;
            case 'set-default':
                $ddl->onUpdate = \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::SETDEFAULT;
            break;
            default:
                $ddl->onUpdate = \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::NOACTION;
            break;
        }
        if (!empty($ddl->columns)) {
            $ddl->columns = array_change_key_case($ddl->columns, CASE_LOWER);
            $ddl->columns = array_map('mb_strtolower', $ddl->columns);
        }
        return $ddl;
    }

}

?>
