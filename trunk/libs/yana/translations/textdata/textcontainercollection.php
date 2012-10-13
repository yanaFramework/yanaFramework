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

namespace Yana\Translations\TextData;

/**
 * Collection of text containers.
 *
 * @package     yana
 * @subpackage  translations
 * @ignore
 */
class TextContainerCollection extends \Yana\Core\AbstractCollection
{

    /**
     * Add or replace a text-container.
     *
     * @param   scalar                                        $offset  index of item to replace
     * @param   \Yana\Translations\TextData\IsTextContainer   $value   new value of item
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if the value is not a valid collection item
     * @return  \Yana\Translations\TextData\IsTextContainer
     */
    public function offsetSet($offset, $value)
    {
        if (!$value instanceof \Yana\Translations\TextData\IsTextContainer) {
            $message = 'Instance of IsTextContainer expected';
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, $level);
        }
        $this->_offsetSet($offset, $value);
        return $value;
    }

}

?>