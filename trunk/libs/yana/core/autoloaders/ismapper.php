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
 * <<interface>> For mapping class names to file paths.
 *
 * @package     yana
 * @subpackage  core
 */
interface IsMapper
{

    /**
     * The namespace this mapper reacts to.
     *
     * @return  string
     */
    public function getNameSpace();

    /**
     * The part of the namespace that is not included in directory structure.
     *
     * By default empty.
     *
     * @return  string
     */
    public function getNameSpacePrefix();

    /**
     * Get absolute path of base-directory.
     *
     * @return  string
     */
    public function getBaseDirectory();

    /**
     * Get file extension including the dot.
     *
     * @return  string
     */
    public function getFileExtension();

    /**
     * Get a prefix to put in front of the file-name.
     *
     * @return  string
     */
    public function getFilePrefix();

    /**
     * Set namespace that mapper will be limited to.
     * 
     * @param   string  $nameSpace  including final namespace separator
     * @return  $this
     */
    public function setNameSpace($nameSpace);

    /**
     * Set part of the namespace that is not included in directory structure.
     *
     * By default empty.
     *
     * @param   string  $nameSpacePrefix  including final namespace separator
     * @return  $this
     */
    public function setNameSpacePrefix($nameSpacePrefix);

    /**
     * Set the path to the directory where the files are to be found.
     * 
     * @param   string  $baseDirectory  as absolute path
     * @return  $this
     */
    public function setBaseDirectory($baseDirectory);

    /**
     * Set the extension for your PHP files use including the dot.
     *
     * Usually this is ".php", which is also the default.
     *
     * @param   string  $fileExtension  should start with a dot
     * @return  $this
     */
    public function setFileExtension($fileExtension);

    /**
     * Set a prefix to put in front of the filename.
     *
     * For example if your class-files are named "class.foobar.php" you set this
     * to "class.".
     *
     * @param   string  $filePrefix  the new prefix
     * @return  $this
     */
    public function setFilePrefix($filePrefix);

    /**
     * Map a class name to a relative or absolute file path to load with include_once().
     *
     * @param   string  $className  including namespace
     * @return  string
     */
    public function mapClassNameToFilePath($className);

}
