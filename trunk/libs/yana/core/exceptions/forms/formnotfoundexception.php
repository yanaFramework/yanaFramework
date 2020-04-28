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

namespace Yana\Core\Exceptions\Forms;

/**
 * <<exception>> Form not found.
 *
 * Thrown when a form was requested that does not exist.
 *
 * @package     yana
 * @subpackage  core
 */
class FormNotFoundException extends \Yana\Core\Exceptions\Forms\FormException
{

    /**
     * Set form name.
     *
     * @param   string  $formName  of form object that was not found
     * @return  $this
     */
    public function setFormName(string $formName)
    {
        $this->data['FORM'] = $formName;
        return $this;
    }

}

?>