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

namespace Yana\Util;

/**
 * <<utility>> Directory function.
 *
 * @package    yana
 * @subpackage core
 */
class Dir extends \Yana\Core\AbstractUtility
{

    /**
     * Returns the size of $directory in bytes.
     *
     * This function gets the sum of the sizes of all files in a directory.
     *
     * If $countSubDirs is not provided or true, the result will
     * include all subdirectories.
     *
     * @param   string    $directory      directory name
     * @param   bool      $countSubDirs   on / off
     * @return  int
     * @throws  \Yana\Core\Exceptions\Files\NotFoundException  when directory doesn't exist
     */
    public static function getSize(string $directory, bool $countSubDirs = true): int
    {
        /* directory does not exist */
        if (!is_string($directory) || !is_dir($directory)) {
            $message = "Argument 1 '" . print_r($directory, true) . "' is not a directory.";
            throw new \Yana\Core\Exceptions\Files\NotFoundException($message, \Yana\Log\TypeEnumeration::WARNING);

        }

        /* else determine the size */
        $dir = $directory . '/';
        $d = dir($dir);
        $dirsize = 0;
        while (false !== ($filename = $d->read()))
        {
            if ($filename == '.' || $filename == '..') {
                continue;

            } elseif (is_file($dir . $filename)) { /* accumulate file sizes */
                $dirsize += filesize($dir . $filename);

            } elseif ($countSubDirs === true) { /* only recurse if subdirs are to be included */
                $dirsize += self::getSize($dir . $filename, true, false);
            }
        }
        $d->close();

        /* return result */
        return $dirsize;
    }

    /**
     * List all files of a directory.
     *
     * The argument $filter may contain multiple file extension,
     * use a pipe '|' sign to seperate them.
     * Example: "*.xml|*.html" will find all xml- and html-files
     *
     * @param   string  $directory   search this directory
     * @param   string  $nameFilter  only return files like ...
     * @return  array
     * @throws  \Yana\Core\Exceptions\Files\NotFoundException  when directory doesn't exist
     */
    public static function listFiles(string $directory, string $nameFilter = ""): array
    {
        return self::_dirlist($directory, $nameFilter, true, false);
    }

    /**
     * List all sub-directories of a directory.
     *
     * @param   string  $directory   search this directory
     * @return  array
     * @throws  \Yana\Core\Exceptions\Files\NotFoundException  when directory doesn't exist
     */
    public static function listDirectories(string $directory): array
    {
        return self::_dirlist($directory, "", false, true);
    }

    /**
     * List all contents of a directory.
     *
     * The argument $filter may contain multiple file extension,
     * use a pipe '|' sign to seperate them.
     * Example: "*.xml|*.html" will find all xml- and html-files
     *
     * @param   string  $directory   search this directory
     * @param   string  $nameFilter  only return files like ...
     * @return  array
     * @throws  \Yana\Core\Exceptions\Files\NotFoundException  when directory doesn't exist
     */
    public static function listFilesAndDirectories(string $directory, string $nameFilter = ""): array
    {
        return self::_dirlist($directory, $nameFilter, true, true);
    }

    /**
     * List contents of a directory.
     *
     * The argument $filter may contain multiple file extension,
     * use a pipe '|' sign to seperate them.
     * Example: "*.xml|*.html" will find all xml- and html-files
     *
     * @param   string  $directory           search this directory
     * @param   string  $nameFilter          only return files like ...
     * @param   bool    $includeFiles        should file names be included?
     * @param   bool    $includeDirectories  should directory names be included?
     * @return  array
     * @throws  \Yana\Core\Exceptions\Files\NotFoundException  when directory doesn't exist
     */
    private static function _dirlist(string $directory, string $nameFilter, bool $includeFiles, bool $includeDirectories): array
    {
        if (!is_dir($directory)) {
            $message = "The directory '{$directory}' does not exist.";
            throw new \Yana\Core\Exceptions\Files\NotFoundException($message, \Yana\Log\TypeEnumeration::INFO);
        }

        /* Input handling */
        $nameFilter = preg_replace("/[\/\\\*\?]/", "", $nameFilter); // remove quantifiers and directory delimiters

        /* Quote all special chars */
        if (strpos($nameFilter, '|') !== false) {
            assert(!isset($tok), 'cannot redeclare variable $tok');
            $tok = strtok($nameFilter, "|");
            $nameFilter = "";
            while ($tok !== false)
            {
                $nameFilter .= preg_quote($tok, '/');
                $tok = strtok("|");
                if ($tok !== false) {
                    $nameFilter .= "|";
                }
            }
            unset($tok);
        } else {
            $nameFilter = preg_quote($nameFilter, '/');
        }

        /* read contents from directory */
        $dirlist = array();
        $directory .=  '/';
        $dirHandle = dir($directory);
        while($entry = $dirHandle->read())
        {
            assert(is_array($dirlist), 'Invariant condition failed: $dirlist is not an array.');
            switch (true)
            {
                case $includeFiles && is_file($directory . $entry) && ($nameFilter === "" || preg_match("/(?:{$nameFilter})$/i", $entry)):
                case $includeDirectories && $entry !== '.' && $entry !== '..' && is_dir($directory . $entry):
                    $dirlist[] = $entry;
            }
        } // end while
        unset($entry);
        $dirHandle->close();
        sort($dirlist);

        assert(is_array($dirlist), 'Unexpected result: $dirlist is not an array.');
        return $dirlist;
    }

}

?>