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

namespace Yana\Files;

/**
 * This class allows to iterate over directory contents.
 *
 * @package     yana
 * @subpackage  core
 */
class DirectoryIterator extends \Yana\Core\StdObject implements \Yana\Core\IsCountableIterator
{

    /**
     * Directory to iterate over.
     *
     * @var  \Yana\Files\Dir
     */
    private $_directory = null;

    /**
     * Current position of the iterator.
     *
     * @var  int
     */
    private $_position = 0;

    /**
     * Initialize target directory.
     *
     * @param \Yana\Files\Dir $directory
     */
    public function __construct(\Yana\Files\Dir $directory)
    {
        $this->_directory = $directory;
    }

    /**
     * Returns the directory containing the files to iterate over.
     *
     * @return  \Yana\Files\Dir
     */
    protected function _getDirectory()
    {
        return $this->_directory;
    }

    /**
     * Returns the number of items in the directory.
     *
     * @return  int
     */
    public function count()
    {
        return $this->_directory->length();
    }

    /**
     * Get current file.
     *
     * @return  string
     * @throws  \Yana\Core\Exceptions\OutOfBoundsException  if the iterator is out of bounds
     */
    public function current()
    {
        if (!$this->valid()) {
            throw new \Yana\Core\Exceptions\OutOfBoundsException("Iterator index out of bounds");
        }
        return $this->_directory->getContent($this->_position);
    }

    /**
     * Get field key.
     *
     * @return  int
     */
    public function key()
    {
        return $this->_position;
    }

    /**
     * Increment iterator to next item.
     */
    public function next()
    {
        $this->_position++;
    }

    /**
     * Rewind iterator.
     */
    public function rewind()
    {
        $this->_position = 0;
    }

    /**
     * Check if iterator position is valid.
     *
     * @return  bool
     */
    public function valid()
    {
        return $this->_position < $this->count();
    }

}

?>