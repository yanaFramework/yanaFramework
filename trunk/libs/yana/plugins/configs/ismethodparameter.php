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

namespace Yana\Plugins\Configs;

/**
 * <<entity>> Parameter for method call.
 *
 * This class represents a plugin method's parameter.
 *
 * @package     yana
 * @subpackage  plugins
 * @ignore
 */
interface IsMethodParameter
{

    /**
     * Get parameter type.
     *
     * @return  string
     */
    public function getType(): string;

    /**
     * Get parameter name.
     *
     * @return  string
     */
    public function getName(): string;

    /**
     * Returns bool(true) if a parameter has been provided and bool(false) otherwise.
     *
     * @return  bool
     */
    public function isDefaultValueAvailable(): bool;

    /**
     * Returns a default value.
     *
     * @return  mixed
     */
    public function getDefault();

    /**
     * Set default value of parameter.
     *
     * @param   mixed  $default  of parameter
     * @return  $this
     */
    public function setDefault($default);


}

?>