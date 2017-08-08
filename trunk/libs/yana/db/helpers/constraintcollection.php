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

namespace Yana\Db\Helpers;

/**
 * <<strategy>> This class is meant to be used to evaluate PHP-style row-level constraints.
 *
 * @package     yana
 * @subpackage  db
 */
class ConstraintCollection extends \Yana\Core\AbstractCollection
{

    /**
     * @var  array
     */
    private $_row = array();

    /**
     * Constraint syntax
     *
     * @ignore
     */
    const CONSTRAINT_SYNTAX = "/^\s*(?:(?:(?:-| |\!)?\\\$[\w\d_]+(?:\[[\"'][\w\d_]+[\"']\])? ?|true|false|null|-?\d+|\&\&?|(?:empty|isset|preg_match|ereg|eregi)\((?:'[^']*'|\"[^\"]*\"),\s*\\\$[\w\d_]+(?:\[[\"'][\w\d_]+[\"']\])?\)|[\&\|\!\~\-\*\/\%\+\<\>]|\[\"[^\"\]\[]+\"\]|\[\'[^\'\]\[]+\'\]|\"[^\"]*\"|\'[^\']*\'|\d+(?:\.\d*)?|(?:\=|\!|\<|\>)\={1,2})(?:\s+|$))*\s*$/i";

    /**
     * Initializes the collection.
     *
     * @param  \Yana\Db\Ddl\Constraint[]  $items  expects a list of constraints
     * @param  array                      $row    database row to evaluate
     */
    public function __construct(array $items = array(), array $row = array())
    {
        foreach ($items as $key => $item)
        {
            $this->offsetSet($key, $item);
        }
        $this->_row = $row;
    }

    /**
     * Insert or replace item.
     *
     * Examples of usage:
     * <code>
     * $collection[$offset] = $item;
     * $collection->_offsetSet($offset, $item);
     * </code>
     *
     * @param   scalar                   $key   offset
     * @param   \Yana\Db\Ddl\Constraint  $item  constraint to add to the collection
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException
     * @return  \Yana\Db\Ddl\Constraint
     */
    public function offsetSet($key, $item)
    {
        if (!$item instanceof \Yana\Db\Ddl\Constraint) {
            $message = "Item must be instance of \Yana\Db\Ddl\Constraint.";
            $level = \Yana\Log\TypeEnumeration::ERROR;
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, $level);
        }
        $code = $item->getConstraint();
        if (!preg_match(self::CONSTRAINT_SYNTAX, $code)) {
            $message = "Syntax error in constraint '$code' .";
            $level = \Yana\Log\TypeEnumeration::ERROR;
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, $level);
        }
        return $this->_offsetSet($key, $code);
    }

    /**
     * Evaluates a constraint.
     *
     * Returns bool(true) if the row is acceptable and bool(false) if a constraint check fails.
     *
     * @return  bool
     */
    public function __invoke()
    {
        foreach ($this->toArray() as $code)
        {
            $function = create_function('$ROW', "return (bool) ($code);");
            if ($function($this->_row) === false) {
                return false;
            }
        }
        unset($code);

        return true;
    }

}

?>