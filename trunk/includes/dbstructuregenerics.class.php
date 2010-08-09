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
 * Database Generics
 *
 * This is an abstract base class for all generic, user-defined functions
 * like trigger and constraints.
 *
 * @access      public
 * @package     yana
 * @subpackage  database
 * @ignore
 */
class DbStructureGenerics extends Object
{
    /**
     * current operation
     *
     * @access  public
     * @var     string
     */
    public $operation = "";
    /**
     * current table
     *
     * @access  public
     * @var     DDLTable
     */
    public $table = null;
    /**
     * current field
     *
     * @access  public
     * @var     int
     */
    public $field = "";
    /**
     * current value
     *
     * @access  public
     * @var     mixed
     */
    public $value = null;
    /**
     * current row id
     *
     * This is the value of the primary key column.
     * It is only available if it was part of the query.
     *
     * @access  public
     * @var     mixed
     */
    public $row = "";

    /**
     * Constraint syntax
     *
     * @ignore
     */
    const CONSTRAINT_SYNTAX = "/^\s*(?:(?:(?:-| |\!)?\\\$[\w\d_]+(?:\[[\"'][\w\d_]+[\"']\])? ?|true|false|null|-?\d+|\&\&?|(?:empty|isset|preg_match|ereg|eregi)\((?:'[^']*'|\"[^\"]*\"),\s*\\\$[\w\d_]+(?:\[[\"'][\w\d_]+[\"']\])?\)|[\&\|\!\~\-\*\/\%\+\<\>]|\[\"[^\"\]\[]+\"\]|\[\'[^\'\]\[]+\'\]|\"[^\"]*\"|\'[^\']*\'|\d+(?:\.\d*)?|(?:\=|\!|\<|\>)\={1,2})(?:\s+|$))*\s*$/i";

    /**
     * Create new instance
     *
     * Creates an instance if there is none.
     * Then it returns a reference to this (single) instance.
     *
     * @access  private
     * @param   string       $operation  current operation
     * @param   string       $table      name of table
     * @param   mixed        &$value     value of column
     * @param   string       $field      name of column
     */
    private function __construct($operation, DDLTable $table, &$value, $field = "")
    {
        assert('is_string($operation); // Wrong type for argument 1. String expected.');
        assert('is_string($field); // Wrong type for argument 4. String expected.');
        if (is_string($value)) {
            $value = stripslashes($value);
        }
        $this->operation = "$operation";
        $this->table = $table;
        $this->value =& $value;
        $this->field = mb_strtoupper("$field");
    }

    /**
     * constraint
     *
     * Evaluates a constraint and returns bool(true) on success or bool(false) on error,
     * or if the constraint fails.
     *
     * @access  public
     * @static
     * @param   DDLTable  $table  expected an DDLTable object as input
     * @param   array     $row    row
     * @return  bool
     */
    public static function checkConstraint(DDLTable $table, array $row)
    {
        // evaluate constraint
        foreach ($table->getConstraints() as $constraint)
        {
            $code = $constraint->getConstraint();
            // skip NULL values
            if (YANA_DB_STRICT && !preg_match(self::CONSTRAINT_SYNTAX, $code)) {
                Log::report("Syntax error in constraint '$code' " .
                    "on table '{$table->getName()}'.", E_USER_ERROR);
                return false;
            }
            $function = create_function('$ROW', "return ($code) == true;");
            if ($function($row) === false) {
                Log::report("Constraint '$code' failed " .
                    "on table '{$table->getName()}' with value '".print_r($row, true)."'.", E_USER_WARNING);
                return false;
            }
        } // end foreach ($constraint)
        unset($code, $constraint);

        return true;
    }

    /**
     * trigger
     *
     * Evaluates a trigger and returns bool(true) on success or bool(false) on error.
     *
     * @access  public
     * @static
     * @param   DDLTable  $table    expected an DDLTable object as input
     * @param   array     &$value   value
     * @param   mixed     $rowId    value of primary key
     */
    public static function onBeforeInsert(DDLTable $table, array &$value, $rowId = null)
    {
        $functionName = $table->getTriggerBeforeInsert();
        if (!is_null($functionName)) {
            $trigger = new self("BEFORE_INSERT", $table, $value);
            $trigger->row = $rowId;
            $trigger->_evalTrigger($functionName);
        }
    }

    /**
     * trigger
     *
     * Evaluates a trigger and returns bool(true) on success or bool(false) on error.
     *
     * @access  public
     * @static
     * @param   DDLTable  $table    expected an DDLTable object as input
     * @param   array     $value    value
     * @param   mixed     $rowId    value of primary key
     */
    public static function onAfterInsert(DDLTable $table, array $value, $rowId = null)
    {
        $functionName = $table->getTriggerAfterInsert();
        if (!is_null($functionName)) {
            $trigger = new self("AFTER_INSERT", $table, $value);
            $trigger->row = $rowId;
            $trigger->_evalTrigger($functionName);
        }
    }

    /**
     * trigger
     *
     * Evaluates a trigger and returns bool(true) on success or bool(false) on error.
     *
     * @access  public
     * @static
     * @param   DDLTable  $table    expected an DDLTable object as input
     * @param   string    $field    field
     * @param   mixed     &$value   value
     * @param   mixed     $rowId    value of primary key
     */
    public static function onBeforeUpdate(DDLTable $table, $field, &$value, $rowId = null)
    {
        $functionName = $table->getTriggerBeforeUpdate();
        if (!is_null($functionName)) {
            $trigger = new self("BEFORE_UPDATE", $table, $value, $field);
            $trigger->row = $rowId;
            $trigger->_evalTrigger($functionName);
        }
    }

    /**
     * trigger
     *
     * Evaluates a trigger and returns bool(true) on success or bool(false) on error.
     *
     * @access  public
     * @static
     * @param   DDLTable  $table    expected an DDLTable object as input
     * @param   string    $field    field
     * @param   mixed     $value    value
     * @param   mixed     $rowId    value of primary key
     */
    public static function onAfterUpdate(DDLTable $table, $field, $value, $rowId = null)
    {
        $functionName = $table->getTriggerAfterUpdate();
        if (!is_null($functionName)) {
            $trigger = new self("AFTER_UPDATE", $table, $value, $field);
            $trigger->row = $rowId;
            $trigger->_evalTrigger($functionName);
        }
    }

    /**
     * trigger
     *
     * Evaluates a trigger and returns bool(true) on success or bool(false) on error.
     *
     * @access  public
     * @static
     * @param   DDLTable  $table    expected an DDLTable object as input
     * @param   array     $value    value
     * @param   mixed     $rowId    value of primary key
     */
    public static function onBeforeDelete(DDLTable $table, array $value, $rowId = null)
    {
        $functionName = $table->getTriggerBeforeDelete();
        if (!is_null($functionName)) {
            $trigger = new self("BEFORE_DELETE", $table, $value);
            $trigger->row = $rowId;
            $trigger->_evalTrigger($functionName);
        }
    }

    /**
     * trigger
     *
     * Evaluates a trigger and returns bool(true) on success or bool(false) on error.
     *
     * @access  public
     * @static
     * @param   DDLTable  $table    expected an DDLTable object as input
     * @param   array     $value    value
     * @param   mixed     $rowId    value of primary key
     */
    public static function onAfterDelete(DDLTable $table, array $value, $rowId = null)
    {
        $functionName = $table->getTriggerAfterDelete();
        if (!is_null($functionName)) {
            $trigger = new self("AFTER_DELETE", $table, $value);
            $trigger->row = $rowId;
            $trigger->_evalTrigger($functionName);
        }
    }

    /**
     * evaluate triggers
     *
     * Calls the function with the given name.
     * The current instance is provided as input.
     *
     * @access  private
     * @param   string  $functionName  function name which would be called
     * @ignore
     */
    private function _evalTrigger($functionName)
    {
        assert('is_null($functionName) || is_string($functionName);' .
            '// Wrong type for argument 1. String expected.');
        // call trigger function
        if (is_callable($functionName)) {
            call_user_func($functionName, $this);
        }
    }
}

?>