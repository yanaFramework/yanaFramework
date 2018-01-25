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
 *
 * @ignore
 */

namespace Yana\Views\Icons;

/**
 * <<abstract>> Data adapter to load files configuration from XML.
 *
 * @package     yana
 * @subpackage  views
 */
abstract class AbstractXmlAdapter extends \Yana\Core\Object implements \Yana\Data\Adapters\IsDataAdapter
{

    /**
     * @var  \Yana\Files\IsTextFile
     */
    private $_file = null;

    /**
     * @var  string
     */
    private $_directory = null;

    /**
     * @var  \Yana\Views\Icons\Collection
     */
    private $_collection = null;

    /**
     * <<constructor>> Initialize source file.
     *
     * @param  \Yana\Files\IsTextFile  $file       XML config file
     * @param  string                  $directory  base directory the file refers to (doesn't have to be same as file)
     */
    public function __construct(\Yana\Files\IsTextFile $file, $directory)
    {
        $this->_file = $file;
        $this->_directory = (string) $directory;
    }

    /**
     * Get collection of file entities.
     *
     * Reads and assigns files from file to collection, then returns it.
     *
     * @return  \Yana\Views\Icons\Collection
     */
    protected function _getCollection()
    {
        if (!isset($this->_collection)) {
            $this->_collection = new \Yana\Views\Icons\Collection();
            if ($this->_file->exists()) {
                $this->_file->read();
                assert('!isset($file); // Cannot redeclare var $file');
                assert('!isset($entity); // Cannot redeclare var $entity');
                foreach (\simplexml_load_string($this->_file->getContent()) as $file)
                {
                    if (empty($file['id']) || empty($file['path']) || empty($file['regex'])) {
                        continue;
                    }
                    $entity = new \Yana\Views\Icons\File();
                    $this->_collection[] = $entity
                            ->setId((string) $file['id'])
                            ->setPath((string) $this->_directory . '/' . (string) $file['path'])
                            ->setRegularExpression((string) $file['regex'])
                            ->setDataAdapter($this);
                }
                unset($directory, $entity, $file);
            }
        }
        return $this->_collection;
    }

    /**
     * Convert to XML and save changes to file.
     *
     * @throws  \Yana\Data\Adapters\AdapterException  when the changes were not saved
     */
    protected function _saveChangesToFile()
    {
        assert('!isset($xmlRootNode); // Cannot redeclare var $xmlRootNode');
        $xmlRootNode = new \SimpleXMLElement('<files/>');

        assert('!isset($entity); // Cannot redeclare var $entity');
        assert('!isset($fileNode); // Cannot redeclare var $fileNode');
        foreach ($this->_getCollection() as $entity)
        {
            /* @var $entity \Yana\Views\Icons\IsFile */
            $fileNode = $xmlRootNode->addChild('file');
            $fileNode->addAttribute('id', $entity->getId());
            $fileNode->addAttribute('path', $entity->getPath());
            $fileNode->addAttribute('regex', $entity->getRegularExpression());
        }
        unset($fileNode, $entity);

        $this->_file->setContent($xmlRootNode->asXML());
        // @codeCoverageIgnoreStart
        try {
            $this->_file->write();

        } catch (\Yana\Core\Exceptions\Files\NotWriteableException $e) {
            throw new \Yana\Data\Adapters\AdapterException('Target file not writeable.', \Yana\Log\TypeEnumeration::WARNING, $e);

        } catch (\Yana\Core\Exceptions\Files\UncleanWriteException $e) {
            throw new \Yana\Data\Adapters\AdapterException('Target file modified by third party.', \Yana\Log\TypeEnumeration::WARNING, $e);

        }
        // @codeCoverageIgnoreEnd
    }

}

?>