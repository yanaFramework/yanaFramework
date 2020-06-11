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
 * <<wrapper, facade>> A context-sensitive form wrapper.
 *
 * This class is meant to provide a context-aware form object by binding a form to
 * its current context and identifying the fields that apply to it.
 *
 * Note: this implements the Iterator interface, since it extends a collection,
 * and all collections in this framework MUST implement this interface.
 *
 * @package     yana
 * @subpackage  form
 * @ignore
 */
interface IsFieldCollectionWrapper extends \Yana\Core\IsCollection
{

    /**
     * Get form context.
     *
     * @return  \Yana\Forms\Setups\IsContext
     */
    public function getContext(): \Yana\Forms\Setups\IsContext;

    /**
     * Get form facade.
     *
     * @return  \Yana\Forms\Facade
     */
    public function getForm(): \Yana\Forms\Facade;

    /**
     * Get primary key of the current row.
     *
     * If there is no current row, the function returns NULL instead.
     *
     * @return  scalar
     */
    public function getPrimaryKey();

    /**
     * Check if the form has rows.
     *
     * Rows are sets of values for forms, that have a table-structure.
     *
     * Returns bool(true) if the form has at least 1 row.
     * Returns bool(false) if the form is empty.
     * Always returns bool(false) if the form does not have rows at all,
     * e.g. if it is an insert- or search-form (this is: it is using an insert- or search-context).
     *
     * @return  bool
     */
    public function hasRows(): bool;

    /**
     * Returns the number of rows.
     *
     * If the form has no rows, the function returns int(0).
     *
     * @return  int
     */
    public function getRowCount(): int;

    /**
     * Advances the pointer one row.
     *
     * If there is no next row, this does nothing.
     */
    public function nextRow();

}

?>