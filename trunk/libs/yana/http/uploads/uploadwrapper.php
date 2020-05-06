<?php
/**
 * YANA library
 *
 * Primary controller class
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

namespace Yana\Http\Uploads;

/**
 * To handle access to $_FILES array.
 *
 * @package     yana
 * @subpackage  http
 */
class UploadWrapper extends \Yana\Http\Uploads\AbstractUploadWrapper
{

    /**
     * Returns a list of keys at the given address.
     *
     * Say you are uploading files and you do so by using an array like this:
     * <code>
     * &lt;input type="file" name="myfiles[]"/&gt;
     * </code>
     *
     * Then to iterate over said files you would call:
     * <code>
     * foreach ($uploadWrapper->keys("myfiles") as $key)
     * {
     *     $file = $uploadWrapper->file("myfiles." . $key);
     * }
     * </code>
     *
     * @param   string  $key  address of file
     * @return  array
     */
    public function keys(string $key = '*'): array
    {
        $entries = $this->_getEntry($key);
        return is_array($entries) ? array_keys($entries) : array();
    }

    /**
     * Returns bool(true) if an entry with the given name exists.
     *
     * @param   string  $key  address of file
     * @return  bool
     */
    public function has(string $key): bool
    {
        return !is_null($this->_getEntry($key));
    }

    /**
     * Returns bool(true) if an entry with the given name is a file.
     * 
     * @param   string  $key  address of file
     * @return  bool
     */
    public function isFile(string $key): bool
    {
        return $this->_isValidFile($this->_getEntry($key));
    }

    /**
     * Returns bool(true) if an entry with the given name is a list of files.
     * 
     * @param   string  $key  address of file
     * @return  bool
     */
    public function isListOfFiles(string $key): bool
    {
        return $this->_isList($this->_getEntry($key));
    }

    /**
     * Retrieve file object.
     *
     * @param   string  $key  address of file
     * @return  \Yana\Http\Uploads\IsFile
     * @throws  \Yana\Http\Uploads\NotFoundException  when the file was not found
     */
    public function file(string $key): \Yana\Http\Uploads\IsFile
    {
        assert(!isset($file), 'Cannot redeclare var $file');
        $file = $this->_getEntry($key);
        if (!$this->_isValidFile($file)) {
            throw new \Yana\Http\Uploads\NotFoundException('No such file "' . $key . '"');
        }
        return new \Yana\Http\Uploads\File(
            (string) $file['name'], (string) $file['type'], (string) $file['tmp_name'], (int) $file['size'], (int) $file['error']
        );
    }

    /**
     * Retrieve file collection.
     *
     * Use this function with a dot as array delimiter.
     * Meaning, if you want to retrieve this HTML form field: "outerfield[innerfield]",
     * write your key as: "outerfield.innerfield".
     *
     * Will return a collection of file objects.
     * Note that this function will never return an empty collection.
     * If no entry is found it will throw an exception.
     *
     * @param   string  $key  address of file
     * @return  \Yana\Http\Uploads\FileCollection
     * @throws  \Yana\Http\Uploads\NotFoundException  when the file-list was not found
     */
    public function all(string $key): \Yana\Http\Uploads\FileCollection
    {
        assert(!isset($files), 'Cannot redeclare var $files');
        $files = $this->_getEntry($key);
        if (!$this->_isList($files)) {
            throw new \Yana\Http\Uploads\NotFoundException('No such files "' . $key . '"');
        }

        assert(!isset($collection), 'Cannot redeclare $collection');
        $collection = new \Yana\Http\Uploads\FileCollection();
        assert(!isset($id), 'Cannot redeclare $id');
        assert(!isset($file), 'Cannot redeclare $file');
        foreach ($files as $id => $file)
        {
            if ($this->_isValidFile($file)) {
                $collection[$id] = new \Yana\Http\Uploads\File(
                    (string) $file['name'],
                    (string) $file['type'],
                    (string) $file['tmp_name'],
                    (int) $file['size'],
                    (int) $file['error']
                );
            }
        }
        unset($id, $file);

        return $collection;
    }

}

?>