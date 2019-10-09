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

namespace Yana\Data\Adapters;

/**
 * <<adapter>> data adapter
 *
 * Session adapter, that stores and restores the given object from the session settings.
 *
 * @package     yana
 * @subpackage  data
 */
class FileCacheAdapter extends \Yana\Core\AbstractCountableArray implements \Yana\Data\Adapters\IsDataAdapter
{

    /**
     * Lifetime of the entries.
     *
     * @var  int
     */
    private $_lifetime = 0;

    /**
     * Cache directory to store the files in.
     *
     * @var  \Yana\Files\IsDir
     */
    private $_directory = null;

    /**
     * Constructor.
     *
     * @param  \Yana\Files\IsDir  $directory  directory to store the files in
     * @param  int                $lifetime   0 = forever, or seconds (max 30 days), or timestamp
     * @throws \Yana\Core\Exceptions\Files\NotWriteableException  when the cache directory is not writable
     */
    public function __construct(\Yana\Files\IsDir $directory, $lifetime = 0)
    {
        assert(is_int($lifetime), 'Invalid argument $lifetime: int expected');

        $this->_directory = $directory;
        $this->_lifetime = (int) $lifetime;

        // create when required
        if (!$directory->exists()) {
            $directory->create();
        }
        // check if directory is writable
        if (!$directory->isWriteable()) {
            $message = "Cache-Directory is not writable. Please update permissions.";
            $level = \Yana\Log\TypeEnumeration::INFO;
            throw new \Yana\Core\Exceptions\Files\NotWriteableException($message, $level);
        }
        // In order to be countable the class keeps track of all valid ids.
        $keyList = $this->_readCacheDirectory();
        $this->_setItems($keyList);
        // check timeout settings
        if ($lifetime > 0) {
            $this->_cleanUpFilesOnTimeout();
        }
    }

    /**
     * This function deletes cache-files that have passed the set timeout.
     */
    protected function _cleanUpFilesOnTimeout()
    {
        foreach ($this->_getItems() as $offset => $timeModified)
        {
            if (time() - $this->_getLifetime() > $timeModified) {
                $this->offsetUnset($offset);
            }
        }
    }

    /**
     * Reads the contents of the cache directory and returns the times when they were last modified.
     *
     * @returns  array
     */
    protected function _readCacheDirectory()
    {
        $items = array();
        $file = $this->_toFile("");
        if ($file->exists() && !$file->isEmpty()) {
            $file->read();
            $contents = $file->getContent();
            $items = \unserialize($contents);
            assert(is_array($items));
        }
        return $items;
    }

    /**
     * Returns the path to the cache directory.
     *
     * @return  string
     */
    protected function _getPath()
    {
        return $this->_directory->getPath();
    }

    /**
     * Return array of ids in use.
     *
     * @return  array
     */
    public function getIds()
    {
        return \array_keys(parent::_getItems());
    }

    /**
     * Adds the item if it is missing.
     *
     * Same as:
     * <code>
     * $array[] = $subject;
     * </code>
     *
     * @param  \Yana\Data\Adapters\IsEntity  $entity  what you want to add
     */
    public function saveEntity(\Yana\Data\Adapters\IsEntity $entity)
    {
        $offset = ($entity->getId()) ? $entity->getId() : null;
        $this->offsetSet($offset, $entity);
    }

    /**
     * Returns the maximum lifetime.
     *
     * The returned value is either:
     * <ul>
     *   <li>0: never expires</li>
     *   <li>1 - 2592000: the number of seconds</li>
     *   <li>some Unix timestamp (must be a date in the future)</li>
     * </ul>
     *
     * If the given value is a past timestamp, the values will expire immediately.
     *
     * @return int
     */
    protected function _getLifetime()
    {
        return $this->_lifetime;
    }

    /**
     * Converts a offset string to a file id.
     *
     * @param  string  $offset  base offset
     * @return string
     */
    private function _offsetToId($offset)
    {
        return md5((string) $offset);
    }

    /**
     * Converts the offset to a filename.
     *
     * @param   string  $id  file identifier
     * @return  \Yana\Files\IsTextFile
     */
    protected function _toFile($id)
    {
        assert(is_scalar($id), 'Invalid argument $id: string expected');

        $path = $this->_getPath() . '/' . $id . ".tmp";
        return new \Yana\Files\Text($path);
    }

    /**
     * Return item at offset.
     *
     * Example:
     * <code>
     * $item = $collection[$offset];
     * $item = $collection->offsetGet($offset);
     * </code>
     *
     * @param   scalar  $offset  index of item to retrieve
     * @return  mixed
     */
    public function offsetGet($offset)
    {
        $result = null;

        if ($this->offsetExists($offset)) {
            $id = $this->_offsetToId($offset);
            $file = $this->_toFile($id);
            if ($file->exists()) { // Note: this may give false positives, as the results are cached by PHP
                $file->read();
                $content = $file->getContent();
                $result = \unserialize($content);
            } else {
                $this->offsetUnset($offset); // may happen when the file has been deleted while the program was running
            }
        }

        return $result;
    }

    /**
     * Insert or replace item.
     *
     * @param   scalar  $offset  index of item to replace
     * @param   mixed   $value   new value of item
     * @return  mixed
     */
    public function offsetSet($offset, $value)
    {
        // update key list
        parent::offsetSet($offset, time()); // $offset may be NULL, in which case a new key will be added to the end

        // determine key if none is given (simulates auto-increment)
        if (\is_null($offset)) {
            assert(!isset($_keys), 'Cannot redeclare var $_keys');
            $_keys = \array_keys(parent::_getItems());
            $offset = (string) \array_pop($_keys);
            unset($_keys);
        }

        // store the value to a cache file
        $id = $this->_offsetToId($offset);
        $file = $this->_toFile($id);
        if (!$file->exists()) {
            $file->create();
        }
        $content = serialize($value);
        $file->setContent($content);
        $file->write();
        $this->_updateIndex();

        return $value;
    }

    /**
     * Remove item from collection.
     *
     * @param  scalar  $offset  index of item to remove
     */
    public function offsetUnset($offset)
    {
        $id = $this->_offsetToId($offset);
        $file = $this->_toFile($id);
        if ($file->exists()) {
            $file->delete();
            // update key list
            parent::offsetUnset($offset);
            $this->_updateIndex();
        }
    }

    /**
     * Try to write index to file
     */
    protected function _updateIndex()
    {
        $content = \serialize($this->_getItems());
        $file = $this->_toFile("");
        if (!$file->exists()) {
            $file->create();
        }
        $file->setContent($content);
        $file->write();
    }

}

?>