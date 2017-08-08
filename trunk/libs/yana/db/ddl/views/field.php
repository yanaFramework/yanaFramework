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

namespace Yana\Db\Ddl\Views;

/**
 * database view field structure
 *
 * A column reference that identifies the name of a column in the view (see attribute "alias")
 * with the names of the physical table and column they are based on.
 *
 * @package     yana
 * @subpackage  db
 */
class Field extends \Yana\Db\Ddl\AbstractNamedObject
{

    /**
     * tag name for persistance mapping: object <-> XDDL
     *
     * @var  string
     * @ignore
     */
    protected $xddlTag = "field";

    /**
     * attributes for persistance mapping: object <-> XDDL
     *
     * @var  array
     * @ignore
     */
    protected $xddlAttributes = array(
        'column' => array('name',  'nmtoken'),
        'table'  => array('table', 'nmtoken'),
        'alias'  => array('alias', 'nmtoken')
    );

    /**
     * @var string
     */
    protected $table = null;

    /**
     * @var string
     */
    protected $alias = null;

    /**
     * Get table name.
     *
     * @return  string
     */
    public function getTable()
    {
        if (is_string($this->table)) {
            return $this->table;
        } else {
            return null;
        }
    }

    /**
     * Set table name.
     *
     * @param   string  $table  table name
     * @return  \Yana\Db\Ddl\Views\Field
     */
    public function setTable($table)
    {
        assert('is_string($table); // Wrong type for argument 1. String expected');
        if (empty($table)) {
            $this->table = null;
        } else {
            $this->table = "$table";
        }
        return $this;
    }

    /**
     * Get column alias.
     *
     * @return  string
     */
    public function getAlias()
    {
        if (is_string($this->alias)) {
            return $this->alias;
        } else {
            return null;
        }
    }

    /**
     * Set column alias.
     *
     * @param   string  $alias  column alias
     * @return  \Yana\Db\Ddl\Views\Field
     */
    public function setAlias($alias)
    {
        assert('is_string($alias); // Wrong type for argument 1. String expected');
        if (empty($alias)) {
            $this->alias = null;
        } else {
            $this->alias = "$alias";
        }
        return $this;
    }

    /**
     * Unserialize a XDDL-node to an object.
     *
     * Returns the unserialized object.
     *
     * @param   \SimpleXMLElement  $node    XML node
     * @param   mixed              $parent  parent node (if any)
     * @return  Field
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the column attribute is missing
     */
    public static function unserializeFromXDDL(\SimpleXMLElement $node, $parent = null)
    {
        $attributes = $node->attributes();
        if (!isset($attributes['column'])) {
            $message = "Missing name attribute.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, $level);
        }
        $ddl = new self((string) $attributes['column'], $parent);
        $ddl->_unserializeFromXDDL($node);
        return $ddl;
    }

}

?>