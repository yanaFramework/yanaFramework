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
 * database function parameter definition
 *
 * This wrapper class represents the structure of a database
 *
 * Note that functions are not supported by all DBMS. The implementation is
 * DBMS and language specific.
 *
 * @access      public
 * @package     yana
 * @subpackage  database
 */
class DDLFunctionParameter extends DDLNamedObject
{
    /**#@+
     * Function parameter modes
     */
    const IN = 0;
    const OUT = 1;
    const INOUT = 2;
    /**#@-*/
    /**#@+
     * @ignore
     * @access  protected
     */

    /**
     * tag name for persistance mapping: object <-> XDDL
     * @var  string
     */
    protected $xddlTag = "param";

    /**
     * attributes for persistance mapping: object <-> XDDL
     * @var  array
     */
    protected $xddlAttributes = array(
        'name' => array('name', 'nmtoken'),
        'type' => array('type', 'string'),
        'mode' => array('_mode', 'string')
    );

    /** @var int    */ protected $mode = self::IN;
    /** @var string */ protected $type = "";

    /**#@-*/
    /**#@+
     * properties for persistance mapping: object <-> XDDL
     *
     * @ignore
     * @access  protected
     */

    /** @var string  */ protected $_mode = null;
    /** @var array   */ protected $_map = array(
                            self::IN => 'in',
                            self::OUT => 'out',
                            self::INOUT => 'inout'
                        );
    /**#@-*/

    /**
     * get data type
     *
     * Returns the data type of the parameter as a string.
     *
     * @access  public
     * @return  string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * set data type
     *
     * Sets the data type of the parameter. The list of supported data types depends on the chosen
     * DBMS. For "generic" it is equivalent to all simple data types known in PHP.
     * The setting is mandatory.
     *
     * @access  public
     * @param   string  $type   data type of the parameter
     */
    public function setType($type)
    {
        assert('is_string($type); // Wrong type for argument 1. String expected');
        $this->type = "$type";
    }

    /**
     * get parameter input mode
     *
     * Returns one of the following constants:
     * <ul>
     *   <li> DDLFunctionParameter::IN </li>
     *   <li> DDLFunctionParameter::OUT </li>
     *   <li> DDLFunctionParameter::INOUT </li>
     * </ul>
     *
     * @access  public
     * @return  int
     */
    public function getMode()
    {
        return (int) $this->mode;
    }

    /**
     * set parameter input mode
     *
     * The parameter $mode must be one of the following constants:
     * <ul>
     *   <li> DDLFunctionParameter::IN </li>
     *   <li> DDLFunctionParameter::OUT </li>
     *   <li> DDLFunctionParameter::INOUT </li>
     * </ul>
     *
     * The default is DDLFunctionParameter::IN.
     *
     * @access  public
     * @param   int  $mode  parameter input mode
     */
    public function setMode($mode = self::IN)
    {
        assert('is_int($mode); // Wrong type for argument 1. Integer expected');
        switch($mode)
        {
            case self::IN:
            case self::OUT:
            case self::INOUT:
                $this->mode = $mode;
            break;
            default:
                $this->mode = self::IN;
            break;
        }
    }

    /**
     * serialize this object to XDDL
     *
     * Returns the serialized object as a string in XML-DDL format.
     *
     * @access  public
     * @param   SimpleXMLElement $parentNode  parent node
     * @return  SimpleXMLElement
     */
    public function serializeToXDDL(SimpleXMLElement $parentNode = null)
    {
        switch ($this->mode)
        {
            case self::OUT:
                $this->_mode = $this->_map[self::OUT];
            break;
            case self::INOUT:
                $this->_mode = $this->_map[self::INOUT];
            break;
            default:
                $this->_mode = $this->_map[self::IN];
            break;
        }
        return parent::serializeToXDDL($parentNode);
    }

    /**
     * unserialize a XDDL-node to an object
     *
     * Returns the unserialized object.
     *
     * @access  public
     * @static
     * @param   SimpleXMLElement  $node    XML node
     * @param   mixed             $parent  parent node (if any)
     * @return  DDLFunctionParameter
     */
    public static function unserializeFromXDDL(SimpleXMLElement $node, $parent = null)
    {
        $attributes = $node->attributes();
        if (isset($attributes['name'])) {
            $ddl = new self((string) $attributes['name'], $parent);
        } else {
            throw new InvalidArgumentException("Missing name attribute.", E_USER_WARNING);
        }
        $ddl->_unserializeFromXDDL($node);
        switch ($ddl->_mode)
        {
            case $ddl->_map[self::OUT]:
                $ddl->mode = self::OUT;
            break;
            case $ddl->_map[self::INOUT]:
                $ddl->mode = self::INOUT;
            break;
            default:
                $ddl->mode = self::IN;
            break;
        }
        return $ddl;
    }
}
?>