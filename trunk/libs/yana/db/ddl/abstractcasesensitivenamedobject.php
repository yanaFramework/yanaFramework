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
 * database structure
 *
 * This is a base class for most DDL objects.
 *
 * @package     yana
 * @subpackage  db
 */
abstract class AbstractCaseSensitiveNamedObject extends \Yana\Db\Ddl\AbstractObject
{

    /**
     * Initialize instance and set a name.
     *
     * @param   string  $name  name
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException when an empty or invalid name is provided
     */
    public function __construct(string $name)
    {
        assert(is_string($name), 'Invalid argument $name: string expected');
        $this->setName($name);
    }

    /**
     * Set object name.
     *
     * The name is mandatory.
     *
     * @param   string  $name  object name
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when name is invalid
     * @return  $this
     */
    public function setName($name)
    {
        assert(is_string($name), 'Invalid argument $name: string expected');
        if ($name === "") {
            $message = "Object name cannot be empty.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message);
        }

        return parent::setName($name);
    }

}

?>