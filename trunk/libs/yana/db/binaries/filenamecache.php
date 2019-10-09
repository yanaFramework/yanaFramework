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

namespace Yana\Db\Binaries;

/**
 * Stores and retrieves filenames in cache.
 *
 * @package     yana
 * @subpackage  db
 */
class FileNameCache extends \Yana\Core\StdObject implements \Yana\Db\Binaries\IsFileNameCache
{

    /**
     * @var \Yana\Db\Binaries\IsConfiguration
     */
    private $_configuration = null;

    /**
     * <<constructor>> To inject a custom configuration if needed.
     *
     * @param  \Yana\Db\Binaries\IsConfiguration  $configuration  inject your own configuration
     */
    public function __construct(\Yana\Db\Binaries\IsConfiguration $configuration = null)
    {
        $this->_configuration = $configuration;
    }

    /**
     * Returns a file source configuration.
     *
     * @return  \Yana\Data\Adapters\IsDataAdapter
     * @codeCoverageIgnore
     */
    protected function _getCache()
    {
        if (!isset($this->_configuration)) {
            $this->_configuration = \Yana\Db\Binaries\ConfigurationSingleton::getInstance();
        }
        return $this->_configuration->getFileNameCache();
    }

    /**
     * Read the current file id from cache.
     *
     * Returns the path of a file as stored in the session.
     * Throws an exception if the id is invalid or the file is not found.
     *
     * @param   int   $id        index in files list, of the file to get
     * @param   bool  $fullsize  show full size or thumb-nail (images only)
     * @return  string
     * @throws  \Yana\Core\Exceptions\Files\NotFoundException   if the requested file does not (or no longer) exists
     */
    public function getFilename($id, $fullsize = false)
    {
        assert(is_int($id), 'Wrong type for argument 1. Integer expected');
        assert(is_bool($fullsize), 'Wrong type for argument 2. Boolean expected');

        $file = $this->_getFilenameFromCache($id); // may throw exception

        // Find thumbnails (for images only)
        if (!$fullsize && !preg_match('/\.gz$/', $file)) {
            $mapper = new \Yana\Db\Binaries\FileMapper();
            $id = $mapper->toFileId($file);
            $file = $mapper->toFileName($id, \Yana\Db\Binaries\FileTypeEnumeration::THUMB);
        }

        // If file doesn't exist
        if (!is_file($file)) {
            $message = "Database entry exists, but the corresponding file was not found '{$file}'.";
            $code = \Yana\Log\TypeEnumeration::ERROR;
            $error = new \Yana\Core\Exceptions\Files\NotFoundException($message, $code);
            $error->setFilename($file);
            throw $error;
        }
        return $file;
    }

    /**
     * Read the current file id from cache.
     *
     * Returns the path of a file as stored in the session.
     * Throws an exception if the id is invalid or the file is not found.
     *
     * @param   int   $id        index in files list, of the file to get
     * @param   bool  $fullsize  show full size or thumb-nail (images only)
     * @return  string
     * @throws  \Yana\Core\Exceptions\Files\NotFoundException  if file with index $id does not exist
     */
    protected function _getFilenameFromCache($id)
    {
        assert(is_int($id), 'Wrong type for argument 1. Integer expected');

        $fileId = (int) $id;

        $cache = $this->_getCache();
        /* check arguments */
        if (!isset($cache[$fileId])) {
            $message = "The requested file was not found.";
            $code = \Yana\Log\TypeEnumeration::WARNING;
            $error = new \Yana\Core\Exceptions\Files\NotFoundException($message, $code);
            $error->setFilename((string) $id);
            throw $error;
        }

        return (string) $cache[$fileId];
    }

    /**
     * Store filename in cache and return an ID.
     *
     * @param   string  $file
     * @return  string
     * @throws  \Yana\Core\Exceptions\Files\NotFoundException  if the given $file does not exist
     */
    public function storeFilename($file)
    {
        assert(is_string($file), 'Wrong argument type argument 1. String expected');
        if (!is_file($file)) {
            $message = "File was not found '{$file}'.";
            $code = \Yana\Log\TypeEnumeration::ERROR;
            $error = new \Yana\Core\Exceptions\Files\NotFoundException($message, $code);
            $error->setFilename($file);
            throw $error;
        }

        $cache = $this->_getCache();
        $ids = $cache->getIds();
        $id = !empty($ids) ? \max($ids) + 1 : 0;
        $cache[$id] = (string) $file;
        return $id;
    }

}

?>