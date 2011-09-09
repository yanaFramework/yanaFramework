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

/**
 * Use this class to read a buffer of text from a file.
 *
 * Example:
 * <code>
 * $file = new BufferedReader('myFile.txt');
 * while ($file->hasMoreContent())
 * {
 *     $file->read();
 * }
 * </code>
 *
 * @access     public
 * @package    yana
 * @subpackage plugins
 */
class BufferedReader extends FileReadonly
{

    /**
     * File pointer
     *
     * @access  private
     * @var     resource
     */
    private $_file = null;

    /**
     * Buffer size in byte
     *
     * @access  private
     * @var     int
     */
    private $_bufferSize = 8192;

    /**
     * Create a new instance of this class.
     *
     * @access  public
     * @param   string  $filename     absolute or relative path to file
     * @param   int     $bufferSize   buffer size in byte
     * @throws  NotReadableException  when the file cannot be opened
     */
    public function __construct($filename, $bufferSize = 8192)
    {
        assert('is_int($bufferSize); // Invalid argument $bufferSize: int expected');
        $this->_bufferSize = (int) $bufferSize;
        $this->_file = fopen($filename, "r");
        if ($this->_file === false) {
            throw new NotReadableException("Unable to open file '$filename'.\n", E_USER_NOTICE);
        }
        parent::__construct($filename);
    }

    /**
     * Returns TRUE if the file pointer is not yet at the end of the file.
     *
     * @access  public
     * @return  bool
     */
    public function hasMoreContent()
    {
        return (bool) !feof($this->_file);
    }

    /**
     * Tries to read the file contents and throws an exception on error.
     *
     * @access  public
     * @throws  NotFoundException  if the file does not exist
     */
    public function read()
    {
        if (!$this->exists()) {
            throw new NotFoundException("No such file: '{$this->getPath()}'.", E_USER_NOTICE);
        }
        if ($this->hasMoreContent()) {
            $this->content = array(fread($this->_file, $this->_bufferSize));
        }
    }

    /**
     * Close file connection
     */
    public function __destruct()
    {
        fclose($this->_file);
    }

}

?>