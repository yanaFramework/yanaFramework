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

namespace Yana\Files;

/**
 * <<interface>> Classes representing directories.
 *
 * @package     yana
 * @subpackage  files
 */
interface IsDir extends \Yana\Files\IsReadable
{

    /**
     * Return current file filter.
     *
     * @return  string
     */
    public function getFilter();

    /**
     * This sets up a file filter.
     *
     * @param   string  $filter   current file filter
     * @return  \Yana\Files\Dir
     */
    public function setFilter($filter = "");

    /**
     * Tries to create the directory.
     *
     * @param   int  $mode  access mode, an octal number of 1 through 0777.
     * @return  $this
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException      when argument $mode is not an integer or out of range
     * @throws  \Yana\Core\Exceptions\Files\AlreadyExistsException  when the directory already exists
     * @throws  \Yana\Core\Exceptions\Files\NotWriteableException   when target location is not writeable
     */
    public function create($mode = 0777);

    /**
     * Remove this directory.
     *
     * @param   bool  $isRecursive  triggers wether to remove directories even if they are not empty, default = false
     * @return  $this
     * @throws  \Yana\Core\Exceptions\Files\NotWriteableException  when directory cannot be deleted
     * @throws  \Yana\Core\Exceptions\Files\NotFoundException      when directory is not found
     */
    public function delete($isRecursive = false);

    /**
     * Get the number of files inside the directory.
     *
     * This returns a positive integer.
     * Note that this functions counts the files in respect
     * to the currently set file filter. So the number
     * of files reported here and the number in total
     * may vary.
     *
     * @return  int
     */
    public function length();

    /**
     * List all sub-directories of a directory.
     *
     * @return  array
     * @throws  \Yana\Core\Exceptions\Files\NotFoundException  when directory doesn't exist
     */
    public function listDirectories();

    /**
     * List all files of a directory.
     *
     * The argument $filter may contain multiple file extension,
     * use a pipe '|' sign to seperate them.
     * Example: "*.xml|*.html" will find all xml- and html-files
     *
     * @param   string  $filter  only return files like ...
     * @return  array
     * @throws  \Yana\Core\Exceptions\Files\NotFoundException  when directory doesn't exist
     */
    public function listFiles($filter = "");

    /**
     * List all contents of a directory.
     *
     * The argument $filter may contain multiple file extension,
     * use a pipe '|' sign to seperate them.
     * Example: "*.xml|*.html" will find all xml- and html-files
     *
     * @param   string  $filter  only return files like ...
     * @return  array
     * @throws  \Yana\Core\Exceptions\Files\NotFoundException  when directory doesn't exist
     */
    public function listFilesAndDirectories($filter = "");

    /**
     * Returns the size of $directory in bytes.
     *
     * @param   bool  $countSubDirs   on / off
     * @return  int
     * @throws  \Yana\Core\Exceptions\Files\NotFoundException  when directory doesn't exist
     */
    public function getSize($countSubDirs = true);

    /**
     * Copy the directory to some destination.
     *
     * @param    string   $destDir      destination to copy the file to
     * @param    bool     $overwrite    setting this to false will prevent existing files from getting overwritten
     * @param    int      $mode         the access restriction that applies to the copied file, defaults to 0766
     * @param    bool     $copySubDirs  setting this to true will cause sub-directories to be copied as well
     * @param    string   $fileFilter   use this to limit the copied files to a specific extension
     * @param    string   $dirFilter    use this to limit the copied directories to those matching the filter
     * @param    bool     $useRegExp    set this to bool(true) if you want filters to be treated as a regular expression
     * @return  $this
     * @throws   \Yana\Core\Exceptions\InvalidArgumentException  when one input argument is invalid
     * @throws   \Yana\Core\Exceptions\AlreadyExistsException    if the target directory already exists
     * @throws   \Yana\Core\Exceptions\NotWriteableException     if the target location is not writeable
     * @throws   \Yana\Core\Exceptions\Files\NotCreatedException  when a file or directory could not be created at the target
     */
    public function copy($destDir, $overwrite = true, $mode = 0766, $copySubDirs = false, $fileFilter = null, $dirFilter = null,
        $useRegExp = false);

}

?>