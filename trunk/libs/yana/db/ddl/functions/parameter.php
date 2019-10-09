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

namespace Yana\Db\Ddl\Functions;

/**
 * database function parameter definition
 *
 * This wrapper class represents the structure of a database
 *
 * Note that functions are not supported by all DBMS. The implementation is
 * DBMS and language specific.
 *
 * @package     yana
 * @subpackage  db
 */
class Parameter extends \Yana\Db\Ddl\AbstractNamedObject
{

    /**
     * tag name for persistance mapping: object <-> XDDL
     *
     * @var  string
     * @ignore
     */
    protected $xddlTag = "param";

    /**
     * attributes for persistance mapping: object <-> XDDL
     *
     * @var  array
     * @ignore
     */
    protected $xddlAttributes = array(
        'name' => array('name', 'nmtoken'),
        'type' => array('type', 'string'),
        'mode' => array('_mode', 'string')
    );

    /**
     * @var int
     * @ignore
     */
    protected $mode = \Yana\Db\Ddl\Functions\ParameterTypeEnumeration::IN;

    /**
     * @var string
     * @ignore
     */
    protected $type = "";

    /**
     * properties for persistance mapping: object <-> XDDL
     *
     * @var string
     * @ignore
     */
    protected $_mode = null;

    /**
     * Get data type.
     *
     * Returns the data type of the parameter as a string.
     *
     * @return  string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set data type.
     *
     * Sets the data type of the parameter. The list of supported data types depends on the chosen
     * DBMS. For "generic" it is equivalent to all simple data types known in PHP.
     * The setting is mandatory.
     *
     * @param   string  $type   data type of the parameter
     * @return  \Yana\Db\Ddl\Functions\Parameter 
     */
    public function setType($type)
    {
        assert(is_string($type), 'Invalid argument $type: string expected');
        $this->type = "$type";
        return $this;
    }

    /**
     * Get parameter input mode.
     *
     * Returns one of the following constants:
     * <ul>
     *   <li> \Yana\Db\Ddl\Functions\ParameterTypeEnumeration::IN </li>
     *   <li> \Yana\Db\Ddl\Functions\ParameterTypeEnumeration::OUT </li>
     *   <li> \Yana\Db\Ddl\Functions\ParameterTypeEnumeration::INOUT </li>
     * </ul>
     *
     * @return  int
     */
    public function getMode()
    {
        return (int) $this->mode;
    }

    /**
     * Set parameter input mode.
     *
     * The parameter $mode must be one of the following constants:
     * <ul>
     *   <li> \Yana\Db\Ddl\Functions\ParameterTypeEnumeration::IN </li>
     *   <li> \Yana\Db\Ddl\Functions\ParameterTypeEnumeration::OUT </li>
     *   <li> \Yana\Db\Ddl\Functions\ParameterTypeEnumeration::INOUT </li>
     * </ul>
     *
     * The default is \Yana\Db\Ddl\Functions\ParameterTypeEnumeration::IN.
     *
     * @param   int  $mode  parameter input mode
     * @return  \Yana\Db\Ddl\Functions\Parameter 
     */
    public function setMode($mode = \Yana\Db\Ddl\Functions\ParameterTypeEnumeration::IN)
    {
        assert(is_int($mode), 'Invalid argument $mode: integer expected');
        switch($mode)
        {
            case \Yana\Db\Ddl\Functions\ParameterTypeEnumeration::IN:
            case \Yana\Db\Ddl\Functions\ParameterTypeEnumeration::OUT:
            case \Yana\Db\Ddl\Functions\ParameterTypeEnumeration::INOUT:
                $this->mode = $mode;
            break;
            default:
                $this->mode = \Yana\Db\Ddl\Functions\ParameterTypeEnumeration::IN;
            break;
        }
        return $this;
    }

    /**
     * Returns the serialized object as a string in XML-DDL format.
     *
     * @param   \SimpleXMLElement $parentNode  parent node
     * @return  \SimpleXMLElement
     */
    public function serializeToXDDL(\SimpleXMLElement $parentNode = null)
    {
        switch ($this->mode)
        {
            case \Yana\Db\Ddl\Functions\ParameterTypeEnumeration::OUT:
                $this->_mode = 'out';
            break;
            case \Yana\Db\Ddl\Functions\ParameterTypeEnumeration::INOUT:
                $this->_mode = 'inout';
            break;
            default:
                $this->_mode = 'in';
            break;
        }
        return parent::serializeToXDDL($parentNode);
    }

    /**
     * Unserializes a XDDL-node to an instance of this class and returns it.
     *
     * @param   \SimpleXMLElement  $node    XML node
     * @param   mixed              $parent  parent node (if any)
     * @return  \Yana\Db\Ddl\Functions\Parameter
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
        switch ($ddl->_mode)
        {
            case 'out':
                $ddl->mode = \Yana\Db\Ddl\Functions\ParameterTypeEnumeration::OUT;
            break;
            case 'inout':
                $ddl->mode = \Yana\Db\Ddl\Functions\ParameterTypeEnumeration::INOUT;
            break;
            default:
                $ddl->mode = \Yana\Db\Ddl\Functions\ParameterTypeEnumeration::IN;
            break;
        }
        return $ddl;
    }

}

?>