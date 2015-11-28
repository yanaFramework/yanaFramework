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

namespace Yana\Core\Autoloaders;

/**
 * This is a simple wrapper class for spl_autoload_* function.
 *
 * @package     yana
 * @subpackage  core
 */
class Wrapper extends \Yana\Core\Object
{

    /**
     * Register the given auto-loader class.
     *
     * Returns bool(true) on success and bool(false) on failure.
     *
     * @param   \Yana\Core\Autoloaders\IsLoader  $loader  to be registered
     * @return  bool
     */
    public function registerAutoLoader(\Yana\Core\Autoloaders\IsLoader $loader)
    {
        return \spl_autoload_register(array($loader, 'loadClassFile'));
    }

    /**
     * Deactivates the given auto-loader class.
     *
     * Returns bool(true) on success and bool(false) on failure.
     *
     * @param   \Yana\Core\Autoloaders\IsLoader  $loader  to be registered
     * @return  bool
     */
    public function unregisterAutoLoader(\Yana\Core\Autoloaders\IsLoader $loader)
    {
        return \spl_autoload_unregister(array($loader, 'loadClassFile'));
    }

    /**
     * Returns a list of all registered auto-loaders as an array.
     *
     * @return  array
     */
    public function getRegisteredAutoLoaders()
    {
        return \spl_autoload_functions();
    }

    /**
     * Try to load a class file.
     *
     * @param   string  $className  name of class you are trying to load.
     * @throws  \Yana\Core\Autoloaders\ClassNotFoundException  only if activated!
     */
    public function loadClass($className)
    {
        assert('is_string($className); // $className expected to be String');
        \spl_autoload_call($className);
    }

}
