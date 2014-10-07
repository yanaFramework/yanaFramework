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

namespace Yana\Core\Autoloaders;

/**
 * Standard generic mapping of class names to file paths.
 *
 * This uses the namespace to determine the directory and the class-name to
 * build the name of the file.
 *
 * The implementation herein follows the suggestions given in the PSR-0
 * "community-standard".
 *
 * @package     yana
 * @subpackage  core
 */
class MapperCollection extends \Yana\Core\AbstractCollection
{

    /**
     * Add a new mapper to the collection.
     *
     * @param   scalar  $offset  mapper id
     * @param   \Yana\Core\Autoloaders\IsMapper  $value  mapper that shoud be added
     * @return  \Yana\Core\Autoloaders\IsMapper
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the value is not a mapper
     */
    public function offsetSet($offset, $value)
    {
        if (!$value instanceof \Yana\Core\Autoloaders\IsMapper) {
            $message = "Instance of \Yana\Core\Autoloaders\IsMapper expected. " .
                "Found " . gettype($value) . "(" . get_class($value) . ") instead.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message);
        }
        return $this->_offsetSet($offset, $value);
    }

}

?>