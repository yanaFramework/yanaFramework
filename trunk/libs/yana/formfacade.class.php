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
 * <<facade>> Form wrapper base class.
 *
 * @access      public
 * @package     yana
 * @subpackage  form
 * @ignore
 */
class FormFacade extends FormFacadeAbstract
{

    /**
     * List of foreign key references.
     *
     * @access  protected
     * @var     array
     * @ignore
     */
    protected $references = null;

    /**
     * List of foreign key values.
     *
     * @access  private
     * @var     array
     * @ignore
     */
    private $_referenceValues = null;

    /**
     * create new instance
     *
     * @access  public
     */
    public function __construct()
    {
        $this->setup = new FormSetup();
    }

    /**
     * Relay function call to wrapped object.
     *
     * @access  public
     * @param   string  $name       method name
     * @param   array   $arguments  list of arguments to pass to function
     * @return  mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array(array($this->form, $name), $arguments);
    }

    /**
     * has next row
     *
     * Returns bool(true) if the iterator has more rows.
     * Returns bool(false) if it has no rows.
     *
     * @access  public
     * @return  bool
     */
    public function hasRows()
    {
        return $this->getRowCount() > 0;
    }

    /**
     * returns the number of rows
     *
     * @access  public
     * @return  string
     */
    public function getRowCount()
    {
        return 0;
    }

    /**
     * get name attribute of form element
     *
     * @access  protected
     * @return  string
     */
    private function _getName()
    {
        return $this->form->getName() . "[" . $this->getClass() . "][" . $this->key() . "]";
    }

    /**
     * get id attribute of form element
     *
     * @access  private
     * @return  string
     */
    private function _getId()
    {
        return $this->form->getName() . "-" . $this->getClass() . "-" . $this->key();
    }

    /**
     * get list of foreign-key reference settings
     *
     * This returns an array of the following contents:
     * <code>
     * array(
     *   'primaryKey1' => array(
     *     'table' => 'name of target table'
     *     'column' => 'name of target column'
     *     'label' => 'name of a column in target table that should be used as a label'
     * }
     * </code>
     *
     * @access  private
     * @return  array
     * @ignore
     *
     * @todo    move to builder class
     */
    private function _getReferences()
    {
        if (!isset($this->references)) {
            $this->references = array();
            assert('!isset($field);');
            /* @var $field DDLDefaultField */
            foreach ($this->toArray() as $field)
            {
                if ($field->getType() !== 'reference') {
                    continue;
                }
                assert('!isset($column);');
                $column = $field->getColumnDefinition();
                $reference = $column->getReferenceSettings();
                if (!isset($reference['column'])) {
                    $reference['column'] = $column->getReferenceColumn()->getName();
                }
                if (!isset($reference['label'])) {
                    $reference['label'] = $reference['column'];
                }
                if (!isset($reference['table'])) {
                    $reference['table'] = $column->getReferenceColumn()->getParent()->getName();
                }
                $this->references[$field->getName()] = $reference;
                unset($column);
            } // end foreach
            unset($field);
        }
        return $this->references;
    }

    /**
     * get reference values
     *
     * This function returns an array, where the keys are the values of the primary keys in the
     *
     * @access  private
     * @param   string  $fieldName  name of field to look up
     * @return  array
     * @ignore
     *
     * @todo    move to builder class
     */
    private function _getReferenceValues($fieldName)
    {
        if (!isset($this->_referenceValues[$fieldName])) {
            $this->_referenceValues[$fieldName] = array();
            $references = $this->_getReferences();
            if (isset($references[$fieldName])) {
                $reference = $references[$fieldName];
                $db = $this->form->getQuery()->getDatabase();
                $select = new DbSelect($db);
                $select->setTable($reference['table']);
                $columns = array('LABEL' => $reference['label'], 'VALUE' => $reference['column']);
                $select->setColumns($columns);
                $values = array();
                foreach ($select->getResults() as $row)
                {
                    $values[$row['VALUE']] = $row['LABEL'];
                }
                $this->_referenceValues[$fieldName] = $values;
            }
        }
        return $this->_referenceValues[$fieldName];
    }

}

?>