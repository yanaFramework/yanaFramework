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
 * database initializing SQL-statement
 *
 * Initializing SQL-statements, which are carried out right after the database structure has been
 * published on a database.
 * The syntax may either be portable or DBMS-sepcific.
 *
 * @access      public
 * @package     yana
 * @subpackage  database
 */
class DDLDatabaseInit extends DDL
{
    /**#@+
     * @ignore
     * @access  protected
     */

    /**
     * tag name for persistance mapping: object <-> XDDL
     * @var  string
     */
    protected $xddlTag = "initialization";

    /**
     * attributes for persistance mapping: object <-> XDDL
     * @var  array
     */
    protected $xddlAttributes = array(
        'dbms'    => array('dbms', 'string'),
        '#pcdata' => array('sql',  'string')
    );

    /** @var string */ protected $dbms = "generic";
    /** @var string */ protected $sql = null;

    /**#@-*/

    /**
     * get target DBMS
     *
     * Returns the name of the target DBMS for this definition as a lower-cased string or NULL if
     * there is none specified. The default is "generic".
     *
     * The special "generic" DBMS-value means that the value is suitable for any DBMS.
     * Usually this is used as a fall-back option for DBMS you haven't thought of when creating the
     * database structure or for those that simply doesn't have the feature in question.
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
     * The special "generic" DBMS-value means that the value is suitable for any DBMS.
     * Usually this is used as a fall-back option for DBMS you haven't thought of when creating the
     * database structure or for those that simply doesn't have the feature in question.
     *
     * @access  public
     * @param   string  $dbms  target DBMS, defaults to "generic"
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
     * @param   string  $sql    SQL statement
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
     * unserialize a XDDL-node to an object
     *
     * Returns the unserialized object.
     *
     * @access  public
     * @static
     * @param   SimpleXMLElement  $node    XML node
     * @param   mixed             $parent  parent node (if any)
     * @return  DDLDatabaseInit
     */
    public static function unserializeFromXDDL(SimpleXMLElement $node, $parent = null)
    {
        $ddl = new self();
        $ddl->_unserializeFromXDDL($node);
        return $ddl;
    }
}

?>