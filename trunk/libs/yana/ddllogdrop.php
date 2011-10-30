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
 * database change-log drop operation
 *
 * This wrapper class represents the structure of a database
 *
 * @access      public
 * @package     yana
 * @subpackage  database
 */
class DDLLogDrop extends DDLLogCreate
{
    /**#@+
     * @ignore
     * @access  protected
     */

    /**
     * tag name for persistance mapping: object <-> XDDL
     * @var  string
     */
    protected $xddlTag = "drop";

    /**
     * attributes for persistance mapping: object <-> XDDL
     * @var  array
     */
    protected $xddlAttributes = array(
        'name'        => array('name',        'nmtoken'),
        'version'     => array('version',     'string'),
        'ignoreError' => array('ignoreError', 'bool'),
        'subject'     => array('subject',     'string')
    );

    /**
     * tags for persistance mapping: object <-> XDDL
     * @var  array
     */
    protected $xddlTags = array(
        'description' => array('description', 'string')
    );

    /**#@-*/

    /**
     * Calls the provided handler function.
     *
     * Provided arguments:
     * 1) object type (table|column|view|...)
     * 2) object name
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @access  public
     * @return  bool
     */
    public function commitUpdate()
    {
        if (isset(self::$handler)) {
            return call_user_func(self::$handler, $this->getSubject(), $this->getName());
        } else {
            return false;
        }
    }

    /**
     * Unserializes a XDDL-node to an instance of this class and returns it.
     *
     * @access  public
     * @static
     * @param   \SimpleXMLElement  $node    XML node
     * @param   mixed              $parent  parent node (if any)
     * @return  DDLLogDrop
     * @throws   \Yana\Core\Exceptions\InvalidArgumentException  when the name attribute is missing
     */
    public static function unserializeFromXDDL(\SimpleXMLElement $node, $parent = null)
    {
        $attributes = $node->attributes();
        if (!isset($attributes['name'])) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException("Missing name attribute.", E_USER_WARNING);
        }
        $ddl = new self((string) $attributes['name'], $parent);
        $ddl->_unserializeFromXDDL($node);
        return $ddl;
    }

}

?>