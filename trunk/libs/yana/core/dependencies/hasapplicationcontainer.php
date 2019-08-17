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
declare(strict_types=1);

namespace Yana\Core\Dependencies;

/**
 * <<trait>> Any class that requires an application container.
 *
 * @package     yana
 * @subpackage  core
 */
trait HasApplicationContainer
{

    /**
     * Contains code to initialize and return sub-modules.
     *
     * @var  \Yana\Core\Dependencies\IsApplicationContainer
     */
    private $_dependencyContainer = null;

    /**
     * <<constructor>> Inject dependencies.
     *
     * @param  \Yana\Core\Dependencies\IsApplicationContainer  $container  injected dependencies
     */
    public function __construct(\Yana\Core\Dependencies\IsApplicationContainer $container)
    {
        $this->_dependencyContainer = $container;
    }

    /**
     * Returns the container.
     *
     * The dependency container contains code to initialize and return sub-modules.
     *
     * @return  \Yana\Core\Dependencies\IsApplicationContainer
     */
    protected function _getDependencyContainer()
    {
        return $this->_dependencyContainer;
    }

}

?>