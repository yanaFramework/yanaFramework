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
 * database structure
 *
 * This is a base class for most DDL objects.
 *
 * @access      public
 * @abstract
 * @package     yana
 * @subpackage  database
 */
abstract class DDLNamedObject extends DDL
{

    /**
     * Object name
     *
     * @access  protected
     * @var     string
     * @ignore
     */
    protected $name = null;

    /**
     * Initialize instance.
     *
     * The name is mandatory.
     * If an empty or invalid name is provided, the function throws an InvalidArgumentException.
     *
     * @access  public
     * @param   string        $name     name
     * @throws  InvalidArgumentException
     */
    public function __construct($name)
    {
        assert('is_string($name); // Invalid argument $name: string expected');
        $this->setName($name);
    }

    /**
     * Returns the object name.
     *
     * @access  public
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set object name.
     *
     * The name is mandatory.
     * If an empty or invalid name is provided, the function throws an InvalidArgumentException.
     *
     * @access  public
     * @param   string  $name  object name
     * @throws  InvalidArgumentException  when name is invalid
     * @return  DDLNamedObject 
     */
    public function setName($name)
    {
        assert('is_string($name); // Invalid argument $name: string expected');
        if (!preg_match('/^[a-z][\w-]*$/is', $name)) {
            $message = "Not a valid object name: '$name'. Must start with a letter and may only contain: " .
                "a-z, 0-9, '-' and '_'.";
            throw new InvalidArgumentException($message);

        } else {
            $this->name = mb_strtolower($name);
        }
        return $this;
    }

}

?>