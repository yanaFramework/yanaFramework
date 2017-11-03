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
 * Reference implementation for Yana class loaders.
 *
 * @package     yana
 * @subpackage  core
 */
class Loader extends \Yana\Core\Autoloaders\AbstractLoader
{

    /**
     * Try to load a file associated with a class.
     *
     * @param   string  $className  name of class you are trying to load
     * @throws  \Yana\Core\Exceptions\ClassNotFoundException  when the class was not found (needs to be activated)
     */
    public function loadClassFile($className)
    {
        assert('is_string($className); // $className expected to be String');
        assert('!isset($fileName); // Cannot redeclare var $fileName');
        $fileName = "";
        assert('!isset($mapper); // Cannot redeclare var $mapper');
        foreach ($this->getMaps() as $mapper)
        {
            assert($mapper instanceof \Yana\Core\Autoloaders\IsMapper);
            // We skip those mappers that are not meant for the given namespace
            if ($mapper->getNameSpace() > "" && stripos($className, $mapper->getNameSpace()) !== 0) {
                continue;
            }
            // Next we resolve the filename
            $fileName = $mapper->mapClassNameToFilePath($className);
            // If the mapper delivers absolute paths we check if the file exists.
            // If the path is relative we just rely on PHP include-path to find it.
            if (!$mapper->getBaseDirectory() || \file_exists($fileName)) {
                @include_once $fileName;
                return true;
            }
        }
        unset($mapper);
        // @codeCoverageIgnoreStart
        if ($this->doesThrowExceptionWhenClassIsNotFound()) {
            // The exception is only thrown when the loader is told to do so. By default this is: false
            $message = "No such class: '" . $className . "'.";
            throw new \Yana\Core\Exceptions\ClassNotFoundException($message);
        }
        // @codeCoverageIgnoreEnd
    }

}
