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

namespace Yana\Views\Helpers\Formatters;

/**
 * <<collection>> Type-safe collection of formatters.
 *
 * @package     yana
 * @subpackage  views
 */
class FormatterCollection extends \Yana\Core\AbstractCollection implements \Yana\Views\Helpers\IsFormatter
{

    /**
     * Invoke all formatters.
     *
     * Converts a value using all registered formatters in the given order.
     *
     * @param   mixed  $source  some source
     * @return  scalar
     */
    public function __invoke($source)
    {
        foreach ($this->toArray() as $formatter)
        {
            $source = $formatter($source);
        }
        return $source;
    }

    /**
     * Add or replace item at the given offset
     *
     * @param   scalar                           $offset  valid array key
     * @param   \Yana\Views\Helpers\IsFormatter  $value   new item to add to the collection
     * @return  \Yana\Views\Helpers\IsFormatter
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the value is not of the expected type
     */
    public function offsetSet($offset, $value)
    {
        if (!($value instanceof \Yana\Views\Helpers\IsFormatter)) {
            $message = "Instance of \Yana\Views\Helpers\IsFormatter expected.";
            $e = new \Yana\Core\Exceptions\InvalidArgumentException($message);
            $e->setData($value);
            throw $e;
        }
        return parent::_offsetSet($offset, $value);
    }

}

?>