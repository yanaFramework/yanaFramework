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
     * Returns bool(true) if an entry with the given name exists.
     *
     * @param   string  $key  address of file
     * @return  bool
     */
    public function has($key)
    {
        assert('is_string($key); // Invalid argument type: $key. String expected');
        return !is_null($this->_getEntry((string) $key));
    }

    /**
     * Returns bool(true) if an entry with the given name is a file.
     * 
     * @param   string  $key  address of file
     * @return  bool
     */
    public function isFile($key)
    {
        assert('is_string($key); // Invalid argument type: $key. String expected');
        return $this->_isFile($this->_getEntry((string) $key));
    }

    /**
     * Returns bool(true) if an entry with the given name is a list of files.
     * 
     * @param   string  $key  address of file
     * @return  bool
     */
    public function isListOfFiles($key)
    {
        assert('is_string($key); // Invalid argument type: $key. String expected');
        return $this->_isList($this->_getEntry((string) $key));
    }

    /**
     * Retrieve file object.
     *
     * @param   string  $key  address of file
     * @return  \Yana\Http\Uploads\IsFile
     * @throws  \Yana\Http\Uploads\NotFoundException  when the file was not found
     */
    public function file($key)
    {
        assert('is_string($key); // Invalid argument type: $key. String expected');
        assert('!isset($file); // Cannot redeclare var $file');
        $file = $this->_getEntry((string) $key);
        if (!$this->_isFile($file)) {
            throw new \Yana\Http\Uploads\NotFoundException('No such file "' . (string) $key . '"');
        }
        return new \Yana\Http\Uploads\File($file['name'], $file['type'], $file['tmp_name'], $file['size'], $file['error']);
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
    public function all($key)
    {
        assert('is_string($key); // Invalid argument type: $key. String expected');
        assert('!isset($files); // Cannot redeclare var $files');
        $files = $this->_getEntry((string) $key);
        if (!$this->_isList($files)) {
            throw new \Yana\Http\Uploads\NotFoundException('No such files "' . (string) $key . '"');
        }

        assert('!isset($collection); // Cannot redeclare $collection');
        $collection = new \Yana\Http\Uploads\FileCollection();
        assert('!isset($id); // Cannot redeclare $id');
        assert('!isset($file); // Cannot redeclare $file');
        foreach ($files as $id => $file)
        {
            $collection[$id] = new \Yana\Http\Uploads\File($file['name'], $file['type'], $file['tmp_name'], $file['size'], $file['error']);
        }
        unset($id, $file);

        return $collection;
    }

}

?>