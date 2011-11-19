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
 * @ignore
 */

namespace Yana\Db\Queries\Parsers;

/**
 * <<abstract>> Internal Query-Parser.
 *
 * @package     yana
 * @subpackage  db
 */
abstract class AbstractParser extends \Yana\Core\Object implements \Yana\Db\Queries\Parsers\IsParser
{

    /**
     * @var \DbStream 
     */
    private $_database = null;

    /**
     * Set up the database to build the queries upon.
     *
     * @param  \DbStream  $database 
     */
    public function __construct(\DbStream $database)
    {
        $this->_database = $database;
    }

    /**
     * @return \DbStream
     */
    protected function _getDatabase()
    {
        return $this->_database;
    }

    /**
     * Resolves the where clause and returns the parsed array.
     *
     * The syntax is as follows: ([column] [operator] [value]) ( AND (...))*
     *
     * @param   array  $syntaxTree  where clause
     * @return  array
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the given where-clause is invalid
     * @ignore
     */
    protected function _parseWhere(array $syntaxTree)
    {
        if (empty($syntaxTree)) {
            return array(); // empty where clause
        }

        $leftOperand = $syntaxTree['arg_1'];
        $operator = $syntaxTree['op'];
        $rightOperand = $syntaxTree['arg_2'];
        $negate = !empty($syntaxTree['neg']);
        switch ($operator)
        {
            case 'and':
            case 'or':
                $leftOperand = $this->_parseWhere($leftOperand);
                $rightOperand = $this->_parseWhere($rightOperand);
                return array($leftOperand, $operator, $rightOperand);
            break;
            // is test for existence
            case 'is':
                $rightOperand = null;
                if ($negate) {
                    $operator = '!=';
                } else {
                    $operator = '=';
                }
            break;
            case 'in':
                if ($negate) {
                    $operator = 'not in';
                }
            break;
            case 'exists':
                if ($negate) {
                    $operator = 'not exists';
                }
            break;
            case '<>':
                $operator = '!=';
            // fall through
            case '!=':
                if ($negate) {
                    $operator = '=';
                }
            break;
            case '=':
                if ($negate) {
                    $operator = '!=';
                }
            break;
            case '<':
                if ($negate) {
                    $operator = '>=';
                }
            break;
            case '<=':
                if ($negate) {
                    $operator = '>';
                }
            break;
            case '>':
                if ($negate) {
                    $operator = '<=';
                }
            break;
            case '>=':
                if ($negate) {
                    $operator = '<';
                }
            break;
            case 'like':
            case 'regexp':
                // intentionally left blank
            break;
            // other operators are currently not supported
            default:
                throw new \Yana\Core\Exceptions\InvalidArgumentException("Invalid where clause '$syntaxTree'.");
            break;
        }

        /* a) flip operands, where necessary */
        if ($rightOperand['type'] === 'ident') {
            $_rightOperand = $rightOperand;
            $rightOperand = $leftOperand;
            $leftOperand = $_rightOperand;
            unset($_rightOperand);
        }
        // left operand must be identifier
        if ($leftOperand['type'] !== 'ident') {
            throw new \Yana\Core\Exceptions\InvalidArgumentException("Invalid where clause '$syntaxTree'.");
        }
        $leftOperand = $leftOperand['value'];
        if (strpos($leftOperand, '.') !== false) {
            $leftOperand = explode('.', $leftOperand);
        }
        // right operand may be identifier or value
        // a) is column name
        if ($rightOperand['type'] === 'ident') {
            if (strpos($rightOperand['value'], '.') !== false) {
                $rightOperand['value'] = explode('.', $rightOperand['value']);
            }
        } elseif ($rightOperand['type'] === 'command') {
            $rightOperand['value'] = $this->parseSQL($rightOperand);
        }
        $rightOperand = $rightOperand['value'];

        return array($leftOperand, $operator, $rightOperand);
    }

}

?>