<?php
/**
 * PHPUnit test-suite: Full
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
 * @package  test
 * @license  http://www.gnu.org/licenses/gpl.txt
 */

/**
 * @ignore
 */
require_once dirname(__FILE__) . '/include.php';

/**
 * test suite containing all tests
 *
 * @package  test
 */
class SuiteFull extends PHPUnit_Framework_TestSuite
{
    /**
     * suite factory
     *
     * This function creates a PHPUnit test-suite,
     * scans the directory for test-cases,
     * adds all test-cases found and then returns
     * a test-suite containing all available tests.
     *
     * @access  public
     * @static
     * @return  SuiteFull
     */
    public static function suite()
    {
        $suite = new SuiteFull();
        $cwd = dirname(__FILE__);
        foreach (self::getFiles($cwd) as $file)
        {
            $suite->addTestFile($file);
        }
        return $suite;
    }

    /**
     * recursively list contents of a directory
     *
     * Returns a numeric list of all PHP-files found
     * in the directory and it's sub-directories.
     *
     * Each file is given with full path and file
     * name.
     *
     * @access  private
     * @static
     * @param   string  $directory  base directory
     * @param   array   &$files     used for recursion only
     * @return  array
     */
    private static function getFiles($directory, array &$files = array())
    {
        // read contents from directory
        if (is_dir($directory)) {
            foreach (scandir($directory) as $entry)
            {
                // skip entries '.' and '..'
                if ($entry[0] !== '.') {
                    $entry = $directory . DIRECTORY_SEPARATOR . $entry;
                    // recursion for sub-directories
                    if (is_dir($entry)) {
                        self::getFiles($entry, $files);
                    // add php-files to list
                    } elseif (preg_match('/test\.php$/i', $entry)) {
                        $files[] = $entry;
                    } else {
                        // ignore non-PHP files
                    }
                }
            } // end foreach
            return $files;

        // target directory does not exist
        } else {
            // you might want to throw an exception here
            return array();
        }
    }
}

?>