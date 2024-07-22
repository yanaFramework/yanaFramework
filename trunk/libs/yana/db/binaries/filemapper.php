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

namespace Yana\Db\Binaries;

/**
 * Read binary large objects (blobs) from database.
 *
 * Example of usage:
 * <code>
 * $db = \Yana\Application::connect('foo');
 * $id = $db->select('foo.1.foo_file');
 *
 * $file = new \Yana\Db\Blob($id);
 * $file->read();
 *
 * // output file to screen
 * print $file->getContent();
 * // copy file to some destination
 * $file->copy('foo/bar.dat');
 * </code>
 *
 * @package     yana
 * @subpackage  db
 * @since       2.9.2
 */
class FileMapper extends \Yana\Files\Readonly implements \Yana\Db\Binaries\IsFileMapper
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
    public function __construct(?\Yana\Db\Binaries\IsConfiguration $configuration = null)
    {
        $this->_configuration = $configuration;
    }

    /**
     * Returns a file source configuration.
     *
     * @return  \Yana\Db\Binaries\IsConfiguration
     */
    protected function _getConfiguration(): \Yana\Db\Binaries\IsConfiguration
    {
        if (!isset($this->_configuration)) {
            $this->_configuration = \Yana\Db\Binaries\ConfigurationSingleton::getInstance();
        }
        return $this->_configuration;
    }

    /**
     * Extract unique file-id from a database value.
     *
     * For any given path like "path/file.extension" this returns "file".
     * 
     * @internal Note: for "path/file.ext1.ext2" this returns "ext1". (Remember this for "file.tar.gz")
     *
     * @param   string  $filename  expected to be path/file.extension
     * @return  string
     */
    public function toFileId(string $filename): string
    {
        return preg_replace('/^.*?([\w\-_]+)\.\w+$/', '$1', $filename);
    }

    /**
     * Get matching filename for a given id.
     *
     * @param   string  $fileId  file id
     * @param   string  $type    an element of \Yana\Db\Binaries\FileTypeEnumeration (e.g. 'image', 'thumbnail', 'file')
     * @return  string
     */
    public function toFileName(string $fileId, string $type): string
    {
        $directory = $this->_getConfiguration()->getDirectory();
        $file = $directory; // and ...
        switch ($type)
        {
            case \Yana\Db\Binaries\FileTypeEnumeration::IMAGE:
                $file .= $fileId . '.jpg';
            break;
            case \Yana\Db\Binaries\FileTypeEnumeration::THUMB:
                $file .= 'thumb.' . $fileId . '.png';
            break;
            case \Yana\Db\Binaries\FileTypeEnumeration::FILE:
                $file .= $fileId . '.gz';
            break;
        }
        return $file;
    }

}

?>
