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

namespace Yana\Db\Ddl\Logs;

/**
 * database change-log rename operation
 *
 * This wrapper class represents the structure of a database
 *
 * @package     yana
 * @subpackage  db
 */
class Rename extends \Yana\Db\Ddl\Logs\Create
{

    /**
     * tag name for persistance mapping: object <-> XDDL
     *
     * @var  string
     * @ignore
     */
    protected $xddlTag = "rename";

    /**
     * attributes for persistance mapping: object <-> XDDL
     *
     * @var  array
     * @ignore
     */
    protected $xddlAttributes = array(
        'name'        => array('name',        'nmtoken'),
        'version'     => array('version',     'string'),
        'ignoreError' => array('ignoreError', 'bool'),
        'subject'     => array('subject',     'string'),
        'oldname'     => array('oldName',     'string')
    );

    /**
     * tags for persistance mapping: object <-> XDDL
     *
     * @var  array
     * @ignore
     */
    protected $xddlTags = array(
        'description' => array('description', 'string')
    );

    /**
     * @var string
     * @ignore
     */
    protected $oldName = null;

    /**
     * Returns the old name of the object that has changed.
     *
     * Note: For columns the returned name includes the table ("table.column").
     *
     * @return  string
     */
    public function getOldName()
    {
        if (is_string($this->oldName)) {
            return $this->oldName;
        } else {
            return null;
        }
    }

    /**
     * Set the old name of the object that has changed.
     *
     * @param   string  $oldName  old name of changed object
     * @return  \Yana\Db\Ddl\Logs\Rename
     */
    public function setOldName($oldName)
    {
        assert('is_string($oldName); // Wrong type for argument 1. String expected');
        if (empty($oldName)) {
            $this->oldName = null;
        } else {
            $this->oldName = "$oldName";
        }
        return $this;
    }

    /**
     * Carry out the update.
     *
     * Calls the provided handler function.
     * Provided arguments:
     * 1) object type (table|column|view|...)
     * 2) object's old name
     * 3) object's new name
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @return  bool
     */
    public function commitUpdate()
    {
        $result = false;
        if (isset(self::$handler)) {
            $result = call_user_func(self::$handler, $this->getSubject(), $this->getOldName(), $this->getName());
        }
        return $result;
    }

    /**
     * Unserializes a XDDL-node to an instance of this class and returns it.
     *
     * @param   \SimpleXMLElement  $node    XML node
     * @param   mixed              $parent  parent node (if any)
     * @return  \Yana\Db\Ddl\Logs\Rename
     * @throws   \Yana\Core\Exceptions\InvalidArgumentException  when the name attribute is missing
     */
    public static function unserializeFromXDDL(\SimpleXMLElement $node, $parent = null)
    {
        $attributes = $node->attributes();
        if (!isset($attributes['name'])) {
            $message = "Missing name attribute.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, $level);
        }
        $ddl = new self((string) $attributes['name'], $parent);
        $ddl->_unserializeFromXDDL($node);
        return $ddl;
    }

}

?>