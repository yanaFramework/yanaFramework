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
 * <<utility>> Name mapper.
 *
 * Helps with determining plugin class names, namespaces aso.
 *
 * @package     yana
 * @subpackage  plugins
 */
class PluginNameMapper extends \Yana\Core\AbstractUtility
{

    /**
     * Map plugin id to class name without namespace.
     *
     * @param   string  $id  Must be valid identifier. Consists of chars, numbers and underscores.
     * @return  string
     */
    public static function toClassName($id)
    {
        assert(is_string($id), 'Invalid argument $id: string expected');
        return $id . 'Plugin';
    }

    /**
     * Map plugin id to class name with namespace.
     *
     * @param   string  $id  Must be valid identifier. Consists of chars, numbers and underscores.
     * @return  string
     */
    public static function toClassNameWithNamespace($id)
    {
        assert(is_string($id), 'Invalid argument $id: string expected');
        return self::toNamespace($id) . '\\' . self::toClassName($id);
    }

    /**
     * Map plugin id to namespace.
     *
     * @param   string  $id  Must be valid identifier. Consists of chars, numbers and underscores.
     * @return  string
     */
    public static function toNamespace($id)
    {
        assert(is_string($id), 'Invalid argument $id: string expected');
        return '\\Plugins\\' . $id;
    }

    /**
     * Map plugin id to filename without directory.
     *
     * @param   string  $id  Must be valid identifier. Consists of chars, numbers and underscores.
     * @return  string
     */
    public static function toClassFilename($id)
    {
        assert(is_string($id), 'Invalid argument $id: string expected');
        return $id . '.plugin.php';
    }

    /**
     * Extract plugin id from a class name.
     *
     * @param   string  $className  Must be valid classname with or without namespace
     * @return  string
     */
    public static function fromClassname($className)
    {
        assert(is_string($className), 'Invalid argument $className: string expected');
        $lowerCaseClassName = \mb_strtolower($className);
        $id = \preg_replace('/^.*?([^\\\\]+)plugin$/', '$1', $lowerCaseClassName);
        return $id;
    }

    /**
     * Map plugin id to filename with directory.
     *
     * @param   string             $id             Must be valid identifier. Consists of chars, numbers and underscores.
     * @param   \Yana\Files\IsDir  $baseDirectory  where plugins are stored
     * @return  string
     */
    public static function toClassFilenameWithDirectory($id, \Yana\Files\IsDir $baseDirectory)
    {
        assert(is_string($id), 'Invalid argument $id: string expected');
        return self::toDirectory($id, $baseDirectory) . self::toClassFilename($id);
    }

    /**
     * Map plugin id to filename without directory.
     *
     * @param   string  $id  Must be valid identifier. Consists of chars, numbers and underscores.
     * @return  string
     */
    public static function toVDriveFilename($id)
    {
        assert(is_string($id), 'Invalid argument $id: string expected');
        return $id . '.drive.xml';
    }

    /**
     * Map plugin id to filename with directory.
     *
     * @param   string             $id             Must be valid identifier. Consists of chars, numbers and underscores.
     * @param   \Yana\Files\IsDir  $baseDirectory  where plugins are stored
     * @return  string
     */
    public static function toVDriveFilenameWithDirectory($id, \Yana\Files\IsDir $baseDirectory)
    {
        assert(is_string($id), 'Invalid argument $id: string expected');
        return self::toDirectory($id, $baseDirectory) . self::toVDriveFilename($id);
    }

    /**
     * Map plugin id to directory with trailing '/'.
     *
     * @param   string  $id                        Must be valid identifier. Consists of chars, numbers and underscores.
     * @param   \Yana\Files\IsDir  $baseDirectory  where plugins are stored
     * @return  string
     */
    public static function toDirectory($id, \Yana\Files\IsDir $baseDirectory)
    {
        assert(is_string($id), 'Invalid argument $id: string expected');
        return $baseDirectory->getPath() . '/' . $id .  '/';
    }

}

?>