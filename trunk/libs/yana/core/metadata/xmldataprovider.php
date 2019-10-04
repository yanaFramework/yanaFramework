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
declare(strict_types=1);

namespace Yana\Core\MetaData;

/**
 * <<template method>> Provides data from XML meta data file.
 *
 * This implements the "template method" design pattern.
 * It means that this class implements an algorithm (here: "loadObject"),
 * that has various parts that are variable and may be changed by derived classes,
 * to create variations of the original behavior.
 *
 * This class will:
 * <ul>
 *   <li> Convert a given id to a filename, </li>
 *   <li> try to load that file into a XML object, </li>
 *   <li> create a new meta data object, </li>
 *   <li> fill that object based on the given XML, </li>
 *   <li> ... and return it. </li>
 * </ul>
 *
 * If you create derived classes, you may overwrite these steps.
 *
 * @package     yana
 * @subpackage  core
 */
class XmlDataProvider extends \Yana\Core\StdObject implements \Yana\Core\MetaData\IsDataProvider
{

    /**
     * basic directory
     *
     * @var  string
     */
    private $_directory = "";

    /**
     * list of valid meta packs.
     *
     * @var  array
     */
    private $_validIds = array();

    /**
     * @param  string  $directory  base directory
     */
    public function __construct($directory)
    {
        assert('is_string($directory); // Invalid argument $directory: string expected');

        $this->_directory = $directory;
    }

    /**
     * Returns the XML-file extension.
     *
     * @internal Please overwrite in sub-classes where needed.
     *
     * @return  string
     */
    protected function _getFileExtension()
    {
        return '.xml';
    }

    /**
     * Returns path to basic directory.
     *
     * @return  string
     */
    protected function _getDirectory()
    {
        return $this->_directory;
    }

    /**
     * Get path to configuration file.
     *
     * {@internal Overwrite or extend this function where needed. }}
     *
     * @param   string  $id  identifier for the file to be loaded
     * @return  string
     */
    protected function _convertIdToFilePath($id)
    {
        assert('is_string($id); // Invalid argument $id: string expected');
        $file = $this->_getDirectory() .'/' . $id . $this->_getFileExtension();
        return $file;
    }

    /**
     * Create and load XML object.
     *
     * {@internal Overwrite this function where needed. }}
     *
     * @param   string  $file  file path
     * @return  \Yana\Core\MetaData\XmlMetaData
     */
    protected function _loadXmlByFileName($file)
    {
        assert('is_string($file); // Invalid argument $file: string expected');
        return new \Yana\Core\MetaData\XmlMetaData($file, LIBXML_NOWARNING | LIBXML_NOERROR | LIBXML_NOENT, true);
    }

    /**
     * Create new instance of meta data class.
     *
     * {@internal Overwrite this function where needed. }}
     *
     * @return  \Yana\Core\MetaData\IsPackageMetaData
     */
    protected function _createMetaData()
    {
        return new \Yana\Core\MetaData\PackageMetaData();
    }

    /**
     * Fill meta data object with infos.
     *
     * This fills information on the meta data object based on the given XML.
     * If the xml file is empty, no data is changed.
     *
     * {@internal Overwrite or extend this function where needed. }}
     *
     * @param   \Yana\Core\MetaData\IsPackageMetaData  $metaData  object that should be filled
     * @param   \Yana\Core\MetaData\XmlMetaData        $xml       provided XML meta data
     * @param   string                                 $id        identifier for the processed XML file to be loaded
     * @return  \Yana\Core\MetaData\IsPackageMetaData
     */
    protected function _fillMetaData(\Yana\Core\MetaData\IsPackageMetaData $metaData, \Yana\Core\MetaData\XmlMetaData $xml, $id)
    {
        assert('is_string($id); // Invalid argument $id: string expected');

        if (!empty($xml)) {
            $file = $this->_convertIdToFilePath($id);
            $directory = $this->_getDirectory() . '/';
            $previewImage = $directory . '/' . $id . "/icon.png";
            $metaData->setTitle($xml->getTitle())
                ->setTexts($xml->getDescriptions())
                ->setAuthor($xml->getAuthor())
                ->setUrl($xml->getUrl())
                ->setPreviewImage($previewImage)
                ->setLastModified(filemtime($file));
            unset($file);

        }

        return $metaData;
    }

    /**
     * Load meta data object.
     *
     * @param   string  $id  identifier for the file to be loaded
     * @return  \Yana\Core\MetaData\IsPackageMetaData
     * @throws  \Yana\Core\Exceptions\NotFoundException  when the file for this identifier is not found
     */
    public function loadOject($id)
    {
        assert('is_string($id); // Invalid argument $id: string expected');

        $file = $this->_convertIdToFilePath((string) $id);
        if (!is_file($file)) {
            throw new \Yana\Core\Exceptions\NotFoundException("Configuration file not found: '{$file}'.");
        }
        $xml = $this->_loadXmlByFileName($file); // may throw NotFoundException
        $metaData = $this->_createMetaData();
        $filledMetaData = $this->_fillMetaData($metaData, $xml, (string) $id);
        assert($filledMetaData instanceof \Yana\Core\MetaData\IsPackageMetaData);

        return $filledMetaData;
    }

    /**
     * Returns a list of all names of language pack that can be loaded.
     *
     * @return  array
     */
    public function getListOfValidIds()
    {
        assert('is_array($this->_validIds);');
        if (empty($this->_validIds)) {
            $this->_validIds = array();
            assert('!isset($file); // Cannot redeclare var $file');
            foreach (glob($this->_convertIdToFilePath('*')) as $file)
            {
                $id = basename($file, $this->_getFileExtension());
                $this->_validIds[] = $id;
            }
            unset($file);
        }
        assert('is_array($this->_validIds);');
        return $this->_validIds;
    }

}

?>