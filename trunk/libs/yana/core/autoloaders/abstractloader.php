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
 * <<abstract>> Base class for automatic class loaders.
 *
 * @package     yana
 * @subpackage  core
 */
abstract class AbstractLoader extends \Yana\Core\Object implements \Yana\Core\Autoloaders\IsLoader
{

    /**
     * Collection of classname-to-filename mapper classes.
     *
     * @var  \Yana\Core\Autoloaders\MapperCollection
     */
    private $_maps = null;

    /**
     * Collection of classname-to-filename mapper classes.
     *
     * @var  bool
     */
    private $_throwExceptionWhenClassIsNotFound = false;

    /**
     * Returns a collection of classname-to-filename mapper classes.
     *
     * @return  \Yana\Core\Autoloaders\MapperCollection
     */
    public function getMaps(): \Yana\Core\Autoloaders\MapperCollection
    {
        if (!isset($this->_maps)) {
            $this->_maps = new \Yana\Core\Autoloaders\MapperCollection();
        }
        return $this->_maps;
    }

    /**
     * Tells whether an exception should be thrown when a class is not found.
     *
     * @return  bool
     */
    public function doesThrowExceptionWhenClassIsNotFound(): bool
    {
        return $this->_throwExceptionWhenClassIsNotFound;
    }

    /**
     * Set whether an exception should be thrown when a class is not found.
     *
     * @param   bool  $throwException  true = do throw exception, false = don't throw exception
     * @return  $this
     */
    public function setThrowExceptionWhenClassIsNotFound(bool $throwException): \Yana\Core\Autoloaders\IsLoader
    {
        $this->_throwExceptionWhenClassIsNotFound = $throwException;
        return $this;
    }

}
