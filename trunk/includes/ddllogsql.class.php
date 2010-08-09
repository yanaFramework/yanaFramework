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
 * database change-log sql statement
 *
 * This wrapper class represents the structure of a database
 *
 * @access      public
 * @package     yana
 * @subpackage  database
 */
class DDLLogSql extends DDLLog
{
    /**#@+
     * @ignore
     * @access  protected
     */

    /**
     * tag name for persistance mapping: object <-> XDDL
     * @var  string
     */
    protected $xddlTag = "sql";

    /**
     * attributes for persistance mapping: object <-> XDDL
     * @var  array
     */
    protected $xddlAttributes = array(
        'version'     => array('version',     'string'),
        'ignoreError' => array('ignoreError', 'bool'),
        'dbms'        => array('dbms',        'string')
    );

    /**
     * tags for persistance mapping: object <-> XDDL
     * @var  array
     */
    protected $xddlTags = array(
        'description' => array('description', 'string'),
        'code'        => array('sql',         'string')
    );

    /** @var string */ protected $dbms = null;
    /** @var string */ protected $sql = null;
    /** @var string */ protected $description = null;

    /**#@-*/

    /**
     * constructor
     *
     * @param  DDLChangeLog  $parent  parent database
     */
    public function __construct(DDLChangeLog $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * get target DBMS
     *
     * Returns the name of the target DBMS for this definition as a lower-cased string.
     * The default is "generic".
     *
     * @access  public
     * @return  string
     */
    public function getDBMS()
    {
        if (is_string($this->dbms)) {
            return $this->dbms;
        } else {
            return null;
        }
    }

    /**
     * set target DBMS
     *
     * While you may settle for any target DBMS you want and provide it in any kind of writing you
     * choose, you should remind, that not every DBMS is supported by the database API provided
     * here.
     *
     * The special "generic" DBMS-value means that the constraint is suitable for any DBMS.
     * Any DBMS other than "generic" will limit the setting to that DBMS only.
     *
     * @access  public
     * @param   string  $dbms   target DBMS, defaults to "generic"
     */
    public function setDBMS($dbms = "generic")
    {
        assert('is_string($dbms); // Wrong type for argument 1. String expected');
        $dbms = strtolower($dbms);
        assert('empty($dbms) || in_array($dbms, DDLDatabase::getSupportedDBMS()); // Unsupported DBMS');
        if (empty($dbms)) {
            $this->dbms = null;
        } else {
            $this->dbms = "$dbms";
        }
    }

    /**
     * get description
     *
     * Returns a custom log-message.
     * Not that this is free-text that may contain any format.
     *
     * @access  public
     * @return  string
     */
    public function getDescription()
    {
        if (is_string($this->description)) {
            return $this->description;
        } else {
            return null;
        }
    }

    /**
     * set description
     *
     * Sets the description as a log-message of your choice.
     *
     * @access  public
     * @param   string  $description    new value of this property
     */
    public function setDescription($description)
    {
        assert('is_string($description); // Wrong type for argument 1. String expected');
        if (empty($description)) {
            $this->description = null;
        } else {
            $this->description = "$description";
        }
    }

    /**
     * get sql statement
     *
     * Returns the SQL statement for this operation.
     *
     * @access  public
     * @return  string
     */
    public function getSQL()
    {
        if (is_string($this->sql)) {
            return $this->sql;
        } else {
            return null;
        }
    }

    /**
     * set sql statement
     *
     * Set the SQL statement for this operation.
     *
     * @access  public
     * @param   string  $sql    sql statement
     */
    public function setSQL($sql)
    {
        assert('is_string($sql); // Wrong type for argument 1. String expected');
        if (empty($sql)) {
            $this->sql = null;
        } else {
            $this->sql = "$sql";
        }
    }

    /**
     * carry out the update
     *
     * Calls the provided handler function.
     * Provided arguments:
     * 1) SQL code
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @access  public
     * @return  bool
     */
    public function commitUpdate()
    {
        if (isset(self::$handler)) {
            return call_user_func(self::$handler, $this->getSQL());
        } else {
            return false;
        }
    }

    /**
     * unserialize a XDDL-node to an object
     *
     * Returns the unserialized object.
     *
     * @access  public
     * @static
     * @param   SimpleXMLElement  $node    XML node
     * @param   mixed             $parent  parent node (if any)
     * @return  DDLLogSql
     */
    public static function unserializeFromXDDL(SimpleXMLElement $node, $parent = null)
    {
        $ddl = new self($parent);
        $ddl->_unserializeFromXDDL($node);
        return $ddl;
    }
}

?>