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

namespace Yana\Core\Autoloaders;

/**
 * <<interface>> For automatic class loaders.
 *
 * @package     yana
 * @subpackage  core
 */
interface IsLoader
{

    /**
     * Returns a collection of classname-to-filename mapper classes.
     *
     * @return  \Yana\Core\Autoloaders\MapperCollection
     */
    public function getMaps();

    /**
     * Tells whether an exception should be thrown when a class is not found.
     *
     * Note: this is set to bool(false) by default.
     *
     * @return  bool
     */
    public function doesThrowExceptionWhenClassIsNotFound();

    /**
     * Set whether an exception should be thrown when a class is not found.
     *
     * @param   bool  $throwException  true = do throw exception, false = don't throw exception
     * @return  \Yana\Core\Autoloaders\IsLoader
     */
    public function setThrowExceptionWhenClassIsNotFound($throwException);

    /**
     * Try to load a file associated with a class.
     *
     * @param   string  $className  name of class you are trying to load
     * @throws  \Yana\Core\Exceptions\ClassNotFoundException  when the class was not found (needs to be activated)
     */
    public function loadClassFile($className);

}
