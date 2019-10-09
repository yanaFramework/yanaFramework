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

namespace Yana\Forms\Fields;

/**
 * <<builder>> Builds a field collection based on a given context.
 *
 * @package     yana
 * @subpackage  form
 */
class FieldCollectionBuilder extends \Yana\Core\StdObject implements \Yana\Forms\Fields\IsFieldCollectionBuilder
{

    /**
     * Build a field collection from a given context.
     *
     * @param   \Yana\Forms\Fields\IsFieldCollectionWrapper  $parentForm  source of context and setup
     * @return  \Yana\Forms\Fields\FieldCollection
     */
    public function __invoke(\Yana\Forms\Fields\IsFieldCollectionWrapper $parentForm)
    {
        assert(!isset($form), 'Cannot redeclare var $form');
        $form = $parentForm->getForm();
        assert(!isset($context), 'Cannot redeclare var $context');
        $context = $parentForm->getContext();
        assert(!isset($collection), 'Cannot redeclare var $collection');
        $collection = new \Yana\Forms\Fields\FieldCollection();
        try {
            assert(!isset($columnName), 'Cannot redeclare var $columnName');
            foreach ($form->getTable()->getColumnNames() as $columnName)
            {
                assert(\is_string($columnName));
                $doesNotExistYet = !$collection->offsetExists($columnName); // if we don't know the column yet
                $isInContext = $context->hasColumnName($columnName); // if the column is listed in the context
                $isInForm = $form->hasAllInput() || $form->isField($columnName); // if the forms either allows any column, or lists it explicitly

                if ($doesNotExistYet && $isInContext && $isInForm) {
                    assert(!isset($fieldFacade), 'Cannot redeclare var $fieldFacade');
                    $fieldFacade = $this->_buildFormFieldFacade($collection, $parentForm, $columnName);
                    $collection->offsetSet($columnName, $fieldFacade);
                    unset($fieldFacade);
                }
                unset($doesNotExistYet, $isInContext, $isInForm);
            }
            unset($columnName);
        } catch (\Yana\Core\Exceptions\NotFoundException $e) {
            // No table - collection will remain unchanged (probably empty)
        }
        return $collection;
    }

    /**
     * Find a column in the table and create a field facade based on that.
     *
     * @param   \Yana\Forms\Fields\FieldCollection           $collection  contains the list of columns
     * @param   \Yana\Forms\Fields\IsFieldCollectionWrapper  $parentForm  contains context and setup
     * @param   string                                       $columnName  must be valid column in table associated with form
     * @return  \Yana\Forms\Fields\IsField
     */
    private function _buildFormFieldFacade(\Yana\Forms\Fields\FieldCollection $collection, \Yana\Forms\Fields\IsFieldCollectionWrapper $parentForm, $columnName)
    {
        assert(!isset($form), 'Cannot redeclare var $form');
        $form = $parentForm->getForm();
        assert(!isset($table), 'Cannot redeclare var $table');
        $table = $form->getTable();
        assert(!isset($column), 'Cannot redeclare var $column');
        $column = $table->getColumn($columnName);

        assert(!isset($field), 'Cannot redeclare var $field');
        $field = null;
        if ($form->isField($columnName)) {
            $field = $form->getField($columnName);
        }
        assert(!isset($facade), 'Cannot redeclare var $facade');
        $facade = new \Yana\Forms\Fields\Field($parentForm, $column, $field);
        return $facade;
    }

}

?>