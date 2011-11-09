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
 *
 * @ignore
 */

namespace Yana\Templates\Helpers\Modifiers;

/**
 * Smarty-compatible modifier.
 *
 * This class is registered when instantiating the Smarty Engine.
 *
 * @package     yana
 * @subpackage  templates
 */
class EntitiesModifier extends \Yana\Core\Object implements \Yana\Templates\Helpers\IsModifier
{


    /**
     * <<smarty modifier>> entities
     *
     * Calls the PHP function htmlspecialchars().
     * See the PHP manual for details.
     *
     * @param   string  $string     string
     * @return  string
     */
    public function __invoke($string)
    {
        return htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
    }

}

?>