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

namespace Yana\Db\FileDb\Helpers;

/**
 * This helper class assists in mapping table names to file names.
 *
 * @package     yana
 * @subpackage  db
 */
class FilenameMapper extends \Yana\Core\StdObject
{
 
    /**
     * @var string
     */
    private static $_baseDir = null;

    /**
     * Set directory where database files are to be stored.
     *
     * Note: the directory must be read- and writeable.
     *
     * @param  string  $directory  new base directory
     * @ignore
     */
    public static function setBaseDirectory(string $directory)
    {
        assert(is_dir($directory), 'Wrong type for argument 1. Directory expected');
        self::$_baseDir = $directory;
    }

    /**
     * Get directory where database files are to be stored.
     *
     * If no directory was given, this will load the default directory from DDL config.
     *
     * @return  string
     * @ignore
     */
    public static function getBaseDirectory(): string
    {
        if (!isset(self::$_baseDir)) {
            // @codeCoverageIgnoreStart
            self::$_baseDir = \Yana\Db\Ddl\DDL::getDirectory();
            // @codeCoverageIgnoreEnd
        }
        return self::$_baseDir;
    }

    /**
     * Return path to database SML file.
     *
     * @param   string  $database   database name in lower-cased letters
     * @param   string  $extension  extension
     * @param   string  $tableName  name of the table in lower-cased letters
     * @return  string
     */
    public function __invoke(string $database, string $extension, string $tableName): string
    {
        $directory = realpath(self::getBaseDirectory()) . \DIRECTORY_SEPARATOR . $database;
        if (!is_dir($directory)) {
            // @codeCoverageIgnoreStart
            mkdir($directory);
            chmod($directory, 0700);
            // @codeCoverageIgnoreEnd
        }
        return $directory . \DIRECTORY_SEPARATOR . $tableName . '.' . $extension;
    }

}

?>