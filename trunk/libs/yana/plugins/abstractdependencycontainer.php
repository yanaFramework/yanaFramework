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

namespace Yana\Plugins;

/**
 * <<abstract>> Dependency ccntainer.
 *
 * This class is used as base-class for both the actual dependency containe and
 * the container factory.
 *
 * This allows the factory to access protected functions and members of the
 * class that should later by readonly to the plugin that uses the container.
 *
 * For C-programmers: this is a work-around to emulate the "friend" keyword you
 * know from C++ in PHP and other languages that don't have this feature.
 *
 * @package     yana
 * @subpackage  plugins
 */
abstract class AbstractDependencyContainer extends \Yana\Core\Object
{

    /**
     * @var  \Yana\Application
     */
    private $_application = null;

    /**
     * @var  \Yana\Db\IsConnectionFactory
     */
    private $_connectionFactory = null;

    /**
     * Add application settings to the container.
     *
     * @param   \Yana\Application  $application  representing the currently running application and its settings
     * @return  \Yana\Plugins\AbstractDependencyContainer
     */
    protected function _setApplication(\Yana\Application $application)
    {
        $this->_application = $application;
        return $this;
    }

    /**
     * Add a connection factory to the container.
     *
     * @param   \Yana\Db\IsConnectionFactory  $connectionFactory  aids in creating and (re-)using database connections
     * @return  \Yana\Plugins\AbstractDependencyContainer
     */
    protected function _setConnectionFactory(\Yana\Db\IsConnectionFactory $connectionFactory)
    {
        $this->_connectionFactory = $connectionFactory;
        return $this;
    }


    /**
     * Get application settings.
     *
     * @return  \Yana\Application
     */
    protected function _getApplication()
    {
        return $this->_application;
    }

    /**
     * Get a connection factory.
     *
     * @return  \Yana\Db\IsConnectionFactory
     */
    protected function _getConnectionFactory()
    {
        return $this->_connectionFactory;
    }

}

?>