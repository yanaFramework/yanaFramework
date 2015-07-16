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

namespace Yana\Db\Ddl\Logs;

/**
 * database change-log sql statement
 *
 * This wrapper class represents the structure of a database
 *
 * @access      public
 * @package     yana
 * @subpackage  db
 */
class Sql extends \Yana\Db\Ddl\Logs\AbstractLog
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
     * Initialize instance.
     *
     * @param  \Yana\Db\Ddl\ChangeLog  $parent  parent database
     */
    public function __construct(\Yana\Db\Ddl\ChangeLog $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * Get target DBMS.
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
     * Set target DBMS.
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
     * @return  \Yana\Db\Ddl\Logs\Sql
     */
    public function setDBMS($dbms = "generic")
    {
        assert('is_string($dbms); // Wrong type for argument 1. String expected');
        $dbms = strtolower($dbms);
        assert('empty($dbms) || in_array($dbms, \Yana\Db\Ddl\Database::getSupportedDBMS()); // Unsupported DBMS');
        if (empty($dbms)) {
            $this->dbms = null;
        } else {
            $this->dbms = "$dbms";
        }
        return $this;
    }

    /**
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
     * Set the SQL statement for this operation.
     *
     * @access  public
     * @param   string  $sql  sql statement
     * @return  \Yana\Db\Ddl\Logs\Sql
     */
    public function setSQL($sql)
    {
        assert('is_string($sql); // Wrong type for argument 1. String expected');
        if (empty($sql)) {
            $this->sql = null;
        } else {
            $this->sql = "$sql";
        }
        return $this;
    }

    /**
     * Carry out the update.
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
     * Unserializes a XDDL-node to an instance of this class and returns it.
     *
     * @access  public
     * @static
     * @param   \SimpleXMLElement  $node    XML node
     * @param   mixed              $parent  parent node (if any)
     * @return  \Yana\Db\Ddl\Logs\Sql
     */
    public static function unserializeFromXDDL(\SimpleXMLElement $node, $parent = null)
    {
        $ddl = new self($parent);
        $ddl->_unserializeFromXDDL($node);
        return $ddl;
    }

}

?>