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

namespace Yana\Forms\Dependencies;

/**
 * <<trait>> For classes that have a dependency container.
 *
 * @package     yana
 * @subpackage  form
 * @ignore
 */
trait HasContainer
{

    /**
     * injected dependencies
     *
     * @var  \Yana\Core\Dependencies\IsFormContainer
     */
    private $_dependencyContainer = null;

    /**
     * Set injected dependencies.
     *
     * @param   \Yana\Core\Dependencies\IsFormContainer  $container  dependencies
     * @return  $this
     */
    protected function _setDependencyContainer(\Yana\Core\Dependencies\IsFormContainer $container)
    {
        $this->_dependencyContainer = $container;
        return $this;
    }

    /**
     * Get injected dependencies.
     *
     * @return  \Yana\Core\Dependencies\IsFormContainer
     */
    protected function _getDependencyContainer()
    {
        return $this->_dependencyContainer;
    }

}

?>