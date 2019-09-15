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
 * Base class for most DDL objects.
 *
 * @package     yana
 * @subpackage  db
 */
abstract class AbstractObject extends \Yana\Db\Ddl\DDL
{

    /**
     * Object name
     *
     * @var     string
     * @ignore
     */
    protected $name = null;

    /**
     * Returns the object name.
     *
     * @return  string|NULL
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set object name.
     *
     * The name is optional. To reset the property, leave the parameter $name empty.
     *
     * @param   string  $name   object name
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when given name is invalid
     * @return  $this
     */
    public function setName($name)
    {
        assert('is_string($name); // Invalid argument $name: string expected');
        if ($name === "") {
            $this->name = null;

        } elseif (!preg_match('/^[a-z][\w\d-_]*$/uis', $name)) {
            $message = "Not a valid object name: '$name'. Must start with a letter and may only contain: " .
                "a-z, 0-9, '-' and '_'.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message);

        } else {
            $this->name = $name;
        }
        return $this;
    }

}

?>