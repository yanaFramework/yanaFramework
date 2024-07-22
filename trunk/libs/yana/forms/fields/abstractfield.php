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

namespace Yana\Forms\Fields;

/**
 * <<facade>> Transparent field wrapper base class.
 *
 * @package     yana
 * @subpackage  form
 * @ignore
 */
abstract class AbstractField extends \Yana\Core\StdObject implements \Yana\Forms\Fields\IsField
{

    /**
     * Form in which the field is defined.
     *
     * @var  \Yana\Forms\Fields\FieldCollectionWrapper
     */
    private $_form = null;

    /**
     * Structure definition of base column.
     *
     * @var  \Yana\Db\Ddl\Column
     */
    private $_column = null;

    /**
     * Field to operate on
     *
     * @var  \Yana\Db\Ddl\Field
     */
    private $_field = null;

    /**
     * Create new instance.
     *
     * @param  \Yana\Forms\Fields\FieldCollectionWrapper  $parentForm  form structure of configuration
     * @param  \Yana\Db\Ddl\Column                        $column      base column definition
     * @param  \Yana\Db\Ddl\Field                         $field       wrapped field instance
     */
    public function __construct(\Yana\Forms\Fields\FieldCollectionWrapper $parentForm, \Yana\Db\Ddl\Column $column, ?\Yana\Db\Ddl\Field $field = null)
    {
        $this->_form = $parentForm;
        $this->_column = $column;
        if (!isset($field)) {
            $field = new \Yana\Db\Ddl\Field($column->getName());
        }
        $this->_field = $field;
    }

    /**
     * Transparent wrapping functions.
     *
     * @param   string  $name       function name
     * @param   array   $arguments  function arguments
     * @return  mixed
     */
    public function __call($name, array $arguments)
    {
        if (isset($this->_field) && method_exists($this->_field, $name)) {
            return call_user_func_array(array($this->_field, $name), $arguments);
        } elseif (method_exists($this->_column, $name)) {
            return call_user_func_array(array($this->_column, $name), $arguments);
        } else {
            return call_user_func_array(array($this->_form, $name), $arguments);
        }
    }

    /**
     * Get column definition.
     *
     * @return  \Yana\Db\Ddl\Column
     */
    public function getColumn(): \Yana\Db\Ddl\Column
    {
        return $this->_column;
    }

    /**
     * Get field definition.
     *
     * @return  \Yana\Db\Ddl\Field
     */
    public function getField(): \Yana\Db\Ddl\Field
    {
        return $this->_field;
    }

    /**
     * Get form structure.
     *
     * @return  \Yana\Forms\Fields\FieldCollectionWrapper
     */
    public function getForm(): \Yana\Forms\Fields\FieldCollectionWrapper
    {
        return $this->_form;
    }

    /**
     * Create HTML form for output.
     *
     * @return  string
     */
    public function __toString()
    {
        $builder = new \Yana\Forms\Fields\AutomatedHtmlBuilder();
        return $builder->__invoke($this);
    }

}

?>
