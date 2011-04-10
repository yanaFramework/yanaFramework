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
 * database trigger definition
 *
 * Triggers are a database feature that allows to execute code on certain database events.
 * The implementation however depends heavily on the chosen DBMS. Not all DBMS support all features.
 *
 * For example: not all DBMS support a trigger to be executed "instead" of a statement, thus
 * replacing it.
 *
 * Note that a trigger implies a user defined function. Some DBMS require thus that you specify
 * the function explicitly and expect the trigger to specify the name of the called function.
 * Other DBMS allow that you specify the function body along with the trigger.
 *
 * Triggers may be emulated using PHP-code, when you choose the DBMS-type "generic". In that case
 * the trigger should be a valid callback. See the manual of the database-API for more details on
 * that topic.
 *
 * @access      public
 * @package     yana
 * @subpackage  database
 */
class DDLTrigger extends DDLObject
{
    /**#@+
     * @ignore
     * @access  protected
     */

    /**
     * tag name for persistance mapping: object <-> XDDL
     * @var  string
     */
    protected $xddlTag = "trigger";

    /**
     * attributes for persistance mapping: object <-> XDDL
     * @var  array
     */
    protected $xddlAttributes = array(
        'name'    => array('name',    'nmtoken'),
        'dbms'    => array('dbms',    'string'),
        'on'      => array('on',      'string'),
        'insert'  => array('insert',  'bool'),
        'update'  => array('update',  'bool'),
        'delete'  => array('delete',  'bool'),
        '#pcdata' => array('trigger', 'string')
    );

    /** @var string */ protected $dbms = "generic";
    /** @var string */ protected $trigger = null;
    /** @var string */ protected $on = "before";
    /** @var bool   */ protected $insert = false;
    /** @var bool   */ protected $update = null;
    /** @var bool   */ protected $delete = null;

    /**#@-*/

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
        return $this->dbms;
    }

    /**
     * Set target DBMS.
     *
     * While you may settle for any target DBMS you want and provide it in any kind of writing you
     * choose, you should remind, that not every DBMS is supported by the database API provided
     * here.
     *
     * The special "generic" DBMS-value means that the constraint is suitable for any DBMS.
     * Usually this is used as a fall-back option for DBMS you haven't thought of when creating the
     * database structure or for those that simply doesn't have the feature in question.
     *
     * Generic values are usually simulated using PHP-code.
     *
     * @access  public
     * @param   string  $dbms  target DBMS, defaults to "generic"
     * @return  DDLTrigger
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
        return $this;
    }

    /**
     * Get trigger code.
     *
     * Retrieve the trigger code and return it.
     * The syntax of the code depends on the type of DBMS used.
     *
     * @access  public
     * @return  string
     */
    public function getTrigger()
    {
        if (is_string($this->trigger)) {
            return $this->trigger;
        } else {
            return null;
        }
    }

    /**
     * Set trigger code.
     *
     * Set the trigger code that should be executed when the trigger is fired.
     *
     * Note: This function can't ensure that your codes makes sense.
     * So keep in mind that it is your job in the first place to ensure it is valid!
     * The syntax depends on the target DBMS. For type "generic" the feature is emulated using PHP
     * code.
     *
     * BE WARNED: As always - do NOT use this function with any unchecked user input.
     *
     * Note that some DBMS require that the code is a function call, not a function by itself.
     *
     * This setting is mandatory.
     *
     * @access  public
     * @param   string  $trigger  code that should be executed (possibly a function call)
     * @return  DDLTrigger
     */
    public function setTrigger($trigger)
    {
        assert('is_string($trigger); // Wrong type for argument 1. String expected');
        $this->trigger = "$trigger";
        return $this;
    }

    /**
     * Check wether triggered before statement.
     *
     * Before refers to triggers that fire BEFORE the statement is carried out.
     *
     * @access  public
     * @return  bool
     */
    public function isBefore()
    {
        return ($this->on === 'before');
    }

    /**
     * Check wether triggered after statement.
     *
     * After refers to triggers that fire AFTER the statement or transaction has been successfully
     * carried out. It is not fired if the statement results in an error.
     *
     * @access  public
     * @return  bool
     */
    public function isAfter()
    {
        return ($this->on === 'after');
    }

    /**
     * Check wether triggered instead of statement.
     *
     * Instead referes to triggers that fire INSTEAD of the statement. The statement is not
     * executed. This option is not supported by all DBMS. However: if it is not, you may emulate
     * this (with some limitations) by using PHP code.
     *
     * @access  public
     * @return  bool
     */
    public function isInstead()
    {
        return ($this->on === 'instead');
    }

    /**
     * Set trigger on before statement.
     *
     * Before refers to triggers that fire BEFORE the statement is carried out.
     *
     * A trigger may either fire before, after or instead of a statement, but not on a combination
     * of these. This setting is mandatory. There is no default value.
     *
     * @access  public
     * @return  DDLTrigger
     */
    public function setBefore()
    {
        $this->on = 'before';
        return $this;
    }

    /**
     * Set trigger on after statement.
     *
     * After refers to triggers that fire AFTER the statement or transaction has been successfully
     * carried out. It is not fired if the statement results in an error.
     *
     * A trigger may either fire before, after or instead of a statement, but not on a combination
     * of these. This setting is mandatory. There is no default value.
     *
     * @access  public
     * @return  DDLTrigger
     */
    public function setAfter()
    {
        $this->on = 'after';
        return $this;
    }

    /**
     * Set trigger to replace statement.
     *
     * Instead referes to triggers that fire INSTEAD of the statement. The statement is not
     * executed. This option is not supported by all DBMS. However: if it is not, you may emulate
     * this (with some limitations) by using PHP code.
     *
     * A trigger may either fire before, after or instead of a statement, but not on a combination
     * of these. This setting is mandatory. There is no default value.
     *
     * @access  public
     * @return  DDLTrigger
     */
    public function setInstead()
    {
        $this->on = 'instead';
        return $this;
    }

    /**
     * check if triggered on insert statements
     *
     * This option selects when a trigger is fired. This is either on insert or update or delete
     * statements or a combination of those.
     *
     * Note though, that not all DBMS support triggers that react on multiple events.
     * The API will create seperate triggers for each event in that case.
     *
     * @access  public
     * @return  bool
     */
    public function isInsert()
    {
        return !empty($this->insert);
    }

    /**
     * Check if triggered on update statements.
     *
     * This option selects when a trigger is fired. This is either on insert or update or delete
     * statement or a combination of those.
     *
     * Note though, that not all DBMS support triggers that react on multiple events.
     * The API will create seperate triggers for each event in that case.
     *
     * @access  public
     * @return  bool
     */
    public function isUpdate()
    {
        return !empty($this->update);
    }

    /**
     * Check if triggered on delete statements.
     *
     * This option selects when a trigger is fired. This is either on insert or update or delete
     * statement or a combination of those.
     *
     * Note though, that not all DBMS support triggers that react on multiple events.
     * The API will create seperate triggers for each event in that case.
     *
     * @access  public
     * @return  bool
     */
    public function isDelete()
    {
        return !empty($this->delete);
    }

    /**
     * Set trigger on insert statement.
     *
     * This option selects when a trigger is fired. This is either on insert or update or delete
     * statement or a combination of those.
     *
     * Note though, that not all DBMS support triggers that react on multiple events.
     * The API will create seperate triggers for each event in that case.
     *
     * @access  public
     * @param   bool  $isInsert  true: fire on insert, false: ignore insert
     * @return  DDLTrigger
     */
    public function setInsert($isInsert = true)
    {
        assert('is_bool($isInsert); // Wrong type for argument 1. Boolean expected');
        $this->insert = (bool) $isInsert;
        return $this;
    }

    /**
     * Set trigger on update statement.
     *
     * This option selects when a trigger is fired. This is either on insert or update or delete
     * statement or a combination of those.
     *
     * Note though, that not all DBMS support triggers that react on multiple events.
     * The API will create seperate triggers for each event in that case.
     *
     * @access  public
     * @param   bool  $isUpdate  true: fire on update, false: ignore update
     * @return  DDLTrigger
     */
    public function setUpdate($isUpdate = true)
    {
        assert('is_bool($isUpdate); // Wrong type for argument 1. Boolean expected');
        $this->update = (bool) $isUpdate;
        return $this;
    }

    /**
     * Set trigger on delete statement.
     *
     * This option selects when a trigger is fired. This is either on insert or update or delete
     * statement or a combination of those.
     *
     * Note though, that not all DBMS support triggers that react on multiple events.
     * The API will create seperate triggers for each event in that case.
     *
     * @access  public
     * @param   bool  $isDelete  true: fire on delete, false: ignore delete
     * @return  DDLTrigger
     */
    public function setDelete($isDelete = true)
    {
        assert('is_bool($isDelete); // Wrong type for argument 1. Boolean expected');
        $this->delete = (bool) $isDelete;
        return $this;
    }

    /**
     * unserialize a XDDL-node to an object
     *
     * Returns the unserialized object.
     *
     * @access  public
     * @static
     * @param   \SimpleXMLElement  $node    XML node
     * @param   mixed             $parent  parent node (if any)
     * @return  DDLTrigger
     */
    public static function unserializeFromXDDL(\SimpleXMLElement $node, $parent = null)
    {
        $attributes = $node->attributes();
        $name = "";
        if (isset($attributes['name'])) {
            $name = $attributes['name'];
        }
        $ddl = new self($name);
        $ddl->_unserializeFromXDDL($node);
        return $ddl;
    }

}

?>