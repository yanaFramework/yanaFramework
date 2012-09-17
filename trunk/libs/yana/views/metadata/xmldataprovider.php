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

namespace Yana\Views\MetaData;

/**
 * XML skin description file.
 *
 * @package     yana
 * @subpackage  views
 */
class XmlDataProvider extends \Yana\Core\Object implements \Yana\Views\MetaData\IsDataProvider
{

    /**
     * basic directory
     *
     * @var  string
     */
    private $_directory = "";

    /**
     * file extension for language definition files
     *
     * @var  string
     */
    private $_fileExtension = ".skin.xml";

    /**
     * @param  string  $directory  base directory
     */
    public function __construct($directory)
    {
        assert('is_string($directory); // Invalid argument $directory: string expected');

        $this->_directory = $directory;
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
     * @param   string  $id  identifier for the skin
     * @return  string
     */
    protected function _getSkinPath($id)
    {
        assert('is_string($id); // Invalid argument $id: string expected');

        return $this->_getDirectory() .'/' . $id . $this->_fileExtension;
    }

    /**
     * Load skin data object.
     *
     * @param   string  $id  name of the skin to load
     * @return  \Yana\Views\MetaData\SkinMetaData
     * @throws  \Yana\Core\Exceptions\NotFoundException
     */
    public function loadOject($id)
    {
        assert('is_string($id); // Invalid argument $id: string expected');
        $metaData = new \Yana\Views\MetaData\SkinMetaData();

        $file = $this->_getSkinPath($id);
        $dir = $this->_getDirectory() . '/';
        if (!is_file($file)) {
            throw new \Yana\Core\Exceptions\NotFoundException("Skin definition not found: '{$id}'.");
        }
        // load definition
        $xml = new \Yana\Views\MetaData\SkinXmlFile($file, LIBXML_NOWARNING | LIBXML_NOERROR | LIBXML_NOENT, true);

        // get information
        if (!empty($xml)) {
            $metaData->setTitle($xml->getTitle())
                ->setAuthor($xml->getAuthor())
                ->setUrl($xml->getUrl())
                ->setPreviewImage($dir . $id . "/icon.png")
                ->setLastModified(filemtime($file));

            assert('!isset($template); /* cannot redeclare variable $template */');
            foreach ($xml->getTemplates($dir) as $template)
            {
                /* @var $template \Yana\Views\MetaData\TemplateMetaData */
                $metaData->addTemplate($template);
            }
            unset($template);
        } // end if
        unset($file);

        return $metaData;
    }

}

?>