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

namespace Yana\Forms\Setups;

/**
 * <<enumeration>> Of valid context names.
 *
 * @package     yana
 * @subpackage  form
 */
class ContextNameEnumeration extends \Yana\Core\AbstractEnumeration
{

    /**
     * For forms that are meant to browse data only.
     */
    const READ = 'read';

    /**
     * If the form is meant to browse AND update data.
     */
    const UPDATE = 'update';

    /**
     * For search forms (not including the result list).
     */
    const SEARCH = 'search';

    /**
     * For forms that don't show any pre-existing data, and are meant to create a new data sets only.
     */
    const INSERT = 'insert';

    /**
     * A meta-context, that stores a collection of editable fields to describe other "editable" contexts, such as "update".
     *
     * @ignore
     */
    const EDITABLE = 'editable';

}

?>