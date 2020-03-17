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

namespace Plugins\Search;

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
 * @package    yana
 * @subpackage plugins
 */
class BufferedReader extends \Yana\Files\Readonly
{

    /**
     * File pointer
     *
     * @var  resource
     */
    private $_file = null;

    /**
     * Buffer size in byte
     *
     * @var  int
     */
    private $_bufferSize = 8192;

    /**
     * Create a new instance of this class.
     *
     * @param   string  $filename     absolute or relative path to file
     * @param   int     $bufferSize   buffer size in byte
     * @throws  \Yana\Core\Exceptions\NotReadableException  when the file cannot be opened
     */
    public function __construct($filename, int $bufferSize = 8192)
    {
        $this->_bufferSize = (int) $bufferSize;
        $this->_file = fopen($filename, "r");
        if ($this->_file === false) {
            $level = \Yana\Log\TypeEnumeration::INFO;
            throw new \Yana\Core\Exceptions\NotReadableException("Unable to open file '$filename'.\n", $level);
        }
        parent::__construct($filename);
    }

    /**
     * Returns TRUE if the file pointer is not yet at the end of the file.
     *
     * @return  bool
     */
    public function hasMoreContent(): bool
    {
        return (bool) !feof($this->_file);
    }

    /**
     * Tries to read the file contents and throws an exception on error.
     *
     * @throws  \Yana\Core\Exceptions\NotFoundException  if the file does not exist
     * @return  $this
     */
    public function read()
    {
        if (!$this->exists()) {
            $level = \Yana\Log\TypeEnumeration::INFO;
            throw new \Yana\Core\Exceptions\NotFoundException("No such file: '{$this->getPath()}'.", $level);
        }
        if ($this->hasMoreContent()) {
            $this->content = array(fread($this->_file, $this->_bufferSize));
        }
        return $this;
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