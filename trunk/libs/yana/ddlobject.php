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
abstract class DDLObject extends DDL
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
     * @access  public
     * @param   string        $name     name
     * @throws  InvalidArgumentException  when given name is invalid
     */
    public function __construct($name = "")
    {
        if (!empty($name)) {
            $this->setName($name);
        }
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
     * The name is optional. To reset the property, leave the parameter $name empty.
     *
     * @access  public
     * @param   string  $name   object name
     * @throws  InvalidArgumentException  when given name is invalid
     * @return  DDLObject 
     */
    public function setName($name = "")
    {
        assert('is_string($name); // Invalid argument $name: string expected');
        if (empty($name)) {
            $this->name = null;

        } elseif (!preg_match('/^[a-z]\w+$/is', $name)) {
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