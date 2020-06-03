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
 *
 * @ignore
 */
declare(strict_types=1);

namespace Yana\Db\FileDb\Helpers;

/**
 * Where clause helper.
 *
 * @package     yana
 * @subpackage  db
 */
class WhereClauseHelper extends \Yana\Core\StdObject
{

    /**
     * @var \Yana\Db\Ddl\Database
     */
    private $_schema = null;

    /**
     * @var \Yana\Db\Ddl\Table
     */
    private $_baseTable = null;

    /**
     * <<constructor>> Initialize object.
     *
     * @param  \Yana\Db\Ddl\Database  $schema     DDL objects
     * @param  \Yana\Db\Ddl\Table     $baseTable  table to operate on
     */
    public function __construct(\Yana\Db\Ddl\Database $schema, \Yana\Db\Ddl\Table $baseTable)
    {
        $this->_schema = $schema;
        $this->_baseTable = $baseTable;
    }

    /**
     * Implements where-clause.
     *
     * Each where clause is an array of 3 entries:
     * <ol>
     * <li> left operand </li>
     * <li> operator </li>
     * <li> right operand </li>
     * </ol>
     *
     * List of supported operators:
     * <ul>
     * <li> and, or (indicates that both operands are sub-clauses) </li>
     * <li> =, <>, !=, <, <=, >, >=, like, regexp </li>
     * </ul>
     *
     * Note that not all DBMS support the operator "regexp".
     * Also note that this simulation uses the Perl-compatible regular
     * expressions syntax (PCRE).
     *
     * Example:
     * <code>
     * array(
     *     array('col1', '=', 'val1'),
     *     'and',
     *     array(
     *         array('col2', '<', 1),
     *         'or',
     *         array('col2', '>', 3)
     *     )
     * )
     * </code>
     *
     * The example above translates to: col1 = 'val1' and (col2 < 1 or col2 > 3).
     *
     * The function returns bool(true) if the where clause matches $current,
     * returns bool(false) otherwise.
     *
     * @param   array               $current      dataset that is to be checked, keys must be upper-case
     * @param   array               $where        where clause (left operand, right, operand, operator)
     * @param   \Yana\Db\Ddl\Table  $ignoreTable  used to set an overwrite for tables during outer joins
     * @return  bool
     * @throws  \Yana\Db\Queries\Exceptions\TableNotFoundException  when a table was referenced that doesn't exist
     */
    public function __invoke(array $current, array $where, \Yana\Db\Ddl\Table $ignoreTable = null): bool
    {
        if (empty($where)) {
            return true;
        }
        /* if all required information is provided */
        assert(count($where) === 3, 'Where clause must have exactly 3 items: left + right operands + operator');
        $leftOperand = array_shift($where);
        $operator = array_shift($where);
        $rightOperand = array_shift($where);

        /**
         * 1) is sub-clause
         */
        switch ($operator)
        {
            case \Yana\Db\Queries\OperatorEnumeration::OR:
                return $this->__invoke($current, $leftOperand) || $this->__invoke($current, $rightOperand);

            case \Yana\Db\Queries\OperatorEnumeration::AND:
                return $this->__invoke($current, $leftOperand) && $this->__invoke($current, $rightOperand);

        }

        /**
         * 2) is singular clause
         */
        $table = null;
        if (is_array($leftOperand)) { // content is: table.column
            $tableName = array_shift($leftOperand); // get table name
            $leftOperand = array_shift($leftOperand); // get just the column
            $table = $this->_schema->getTable($tableName);
            if (!$table instanceof \Yana\Db\Ddl\Table) {
                throw new \Yana\Db\Queries\Exceptions\TableNotFoundException('Table not found ' . $tableName);
            }
            unset($tableName);
        } else {
            $table = $this->_baseTable;
        }
        if (isset($current[mb_strtoupper((string) $leftOperand)])) {
            $value = $current[mb_strtoupper((string) $leftOperand)];
        } elseif ($table !== $ignoreTable) {
            $value = null;
        } else {
            return true; // the table is not checked - used for ON-clause during outer joins
        }
        /* handle non-scalar values */
        if (!is_null($value) && !is_scalar($value)) {
            $value = \Yana\Files\SML::encode($value);
        }
        /* switch by operator */
        switch ($operator)
        {
            case '<>':
                // fall through
                $operator = \Yana\Db\Queries\OperatorEnumeration::NOT_EQUAL;
            case \Yana\Db\Queries\OperatorEnumeration::NOT_EQUAL:
                // fall through
            case '==':
            case \Yana\Db\Queries\OperatorEnumeration::EQUAL:
                $column = $table->getColumn($leftOperand);
                assert($column instanceof \Yana\Db\Ddl\Column, 'Column not found');
                if (is_null($rightOperand)) {
                    return is_null($value) xor $operator === \Yana\Db\Queries\OperatorEnumeration::NOT_EQUAL;
                }
                if ($column->isPrimaryKey() || $column->isForeignKey()) {
                    return strcasecmp((string) $value, (string) $rightOperand) === 0 xor $operator === \Yana\Db\Queries\OperatorEnumeration::NOT_EQUAL;
                }
                return (strcmp((string) $value, (string) $rightOperand) === 0) xor $operator === \Yana\Db\Queries\OperatorEnumeration::NOT_EQUAL;

            case \Yana\Db\Queries\OperatorEnumeration::NOT_LIKE:
                $operator = \Yana\Db\Queries\OperatorEnumeration::NOT_REGEX;
                // fall through
            case \Yana\Db\Queries\OperatorEnumeration::LIKE:
                $rightOperand = str_replace(array('%', '_'), array('.*', '.?'), preg_quote((string) $rightOperand, '/'));
                // fall through
            case \Yana\Db\Queries\OperatorEnumeration::REGEX:
                return is_scalar($value) && is_scalar($rightOperand) && preg_match('/^' . (string) $rightOperand . '$/is', (string) $value) === 1 xor $operator === \Yana\Db\Queries\OperatorEnumeration::NOT_REGEX;

            case \Yana\Db\Queries\OperatorEnumeration::LESS:
                return ($value < $rightOperand);

            case \Yana\Db\Queries\OperatorEnumeration::GREATER:
                return ($value > $rightOperand);

            case \Yana\Db\Queries\OperatorEnumeration::LESS_OR_EQUAL:
                return ($value <= $rightOperand);

            case \Yana\Db\Queries\OperatorEnumeration::GREATER_OR_EQUAL:
                return ($value >= $rightOperand);

            case \Yana\Db\Queries\OperatorEnumeration::IN:
                if ($rightOperand instanceof \Yana\Db\Queries\Select) {
                    $rightOperand = $rightOperand->getResults();
                }
                return in_array($value, (array) $rightOperand);

            case \Yana\Db\Queries\OperatorEnumeration::NOT_IN:
                if ($rightOperand instanceof \Yana\Db\Queries\Select) {
                    $rightOperand = $rightOperand->getResults();
                }
                return !in_array($value, (array) $rightOperand);

            case \Yana\Db\Queries\OperatorEnumeration::EXISTS:
                return ($rightOperand instanceof \Yana\Db\Queries\SelectExist) && $rightOperand->doesExist();

            case \Yana\Db\Queries\OperatorEnumeration::NOT_EXISTS:
                return ($rightOperand instanceof \Yana\Db\Queries\SelectExist) && !$rightOperand->doesExist();

            default:
                return true;
        } // end switch
    }

}

?>