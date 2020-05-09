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
 * @package     yana
 * @subpackage  db
 */
class Trigger extends \Yana\Db\Ddl\AbstractUnnamedObject
{

    /**
     * tag name for persistance mapping: object <-> XDDL
     *
     * @var  string
     * @ignore
     */
    protected $xddlTag = "trigger";

    /**
     * attributes for persistance mapping: object <-> XDDL
     *
     * @var  array
     * @ignore
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

    /**
     * @var  string
     * @ignore
     */
    protected $dbms = \Yana\Db\DriverEnumeration::GENERIC;

    /**
     * @var  string
     * @ignore
     */
    protected $trigger = null;

    /**
     * @var  string
     * @ignore
     */
    protected $on = "before";

    /**
     * @var  bool
     * @ignore
     */
    protected $insert = false;

    /**
     * @var  bool
     * @ignore
     */
    protected $update = null;

    /**
     * @var  bool
     * @ignore
     */
    protected $delete = null;

    /**
     * Get target DBMS.
     *
     * Returns the name of the target DBMS for this definition as a lower-cased string.
     * The default is "generic".
     *
     * @return  string|NULL
     */
    public function getDBMS(): ?string
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
     * @param   string  $dbms  target DBMS, defaults to "generic"
     * @return  $this
     */
    public function setDBMS(string $dbms = \Yana\Db\DriverEnumeration::GENERIC)
    {
        if (empty($dbms)) {
            $this->dbms = null;
        } else {
            $this->dbms = strtolower($dbms);
        }
        return $this;
    }

    /**
     * Get trigger code.
     *
     * Retrieve the trigger code and return it.
     * The syntax of the code depends on the type of DBMS used.
     *
     * @return  string|NULL
     */
    public function getTrigger(): ?string
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
     * @param   string  $trigger  code that should be executed (possibly a function call)
     * @return  $this
     */
    public function setTrigger(string $trigger)
    {
        $this->trigger = $trigger;
        return $this;
    }

    /**
     * Check wether triggered before statement.
     *
     * Before refers to triggers that fire BEFORE the statement is carried out.
     *
     * @return  bool
     */
    public function isBefore(): bool
    {
        return ($this->on === 'before');
    }

    /**
     * Check wether triggered after statement.
     *
     * After refers to triggers that fire AFTER the statement or transaction has been successfully
     * carried out. It is not fired if the statement results in an error.
     *
     * @return  bool
     */
    public function isAfter(): bool
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
     * @return  bool
     */
    public function isInstead(): bool
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
     * @return  $this
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
     * @return  $this
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
     * @return  $this
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
     * @return  bool
     */
    public function isInsert(): bool
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
     * @return  bool
     */
    public function isUpdate(): bool
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
     * @return  bool
     */
    public function isDelete(): bool
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
     * @param   bool  $isInsert  true: fire on insert, false: ignore insert
     * @return  $this
     */
    public function setInsert(bool $isInsert = true)
    {
        $this->insert = $isInsert;
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
     * @param   bool  $isUpdate  true: fire on update, false: ignore update
     * @return  $this
     */
    public function setUpdate(bool $isUpdate = true)
    {
        $this->update = $isUpdate;
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
     * @param   bool  $isDelete  true: fire on delete, false: ignore delete
     * @return  $this
     */
    public function setDelete(bool $isDelete = true)
    {
        $this->delete = $isDelete;
        return $this;
    }

    /**
     * Unserialize a XDDL-node to an object.
     *
     * Returns the unserialized object.
     *
     * @param   \SimpleXMLElement  $node    XML node
     * @param   mixed             $parent  parent node (if any)
     * @return  $this
     */
    public static function unserializeFromXDDL(\SimpleXMLElement $node, $parent = null)
    {
        $attributes = $node->attributes();
        $name = "";
        if (isset($attributes['name'])) {
            $name = (string) $attributes['name'];
        }
        $ddl = new self($name);
        $ddl->_unserializeFromXDDL($node);
        return $ddl;
    }

}

?>