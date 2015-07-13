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
class XmlDataProvider extends \Yana\Core\MetaData\XmlDataProvider
{

    /**
     * Returns the XML-file extension.
     *
     * @internal Please overwrite in sub-classes where needed.
     *
     * @return  string
     */
    protected function _getFileExtension()
    {
        return '.skin.xml';
    }

    /**
     * Create and load XML object.
     *
     * @param   string  $file  file path
     * @return  \Yana\Core\MetaData\XmlMetaData
     */
    protected function _loadXmlByFileName($file)
    {
        assert('is_string($file)', ' Invalid argument $file: string expected');
        return new \Yana\Views\MetaData\XmlMetaData($file, LIBXML_NOWARNING | LIBXML_NOERROR | LIBXML_NOENT, true);
    }

    /**
     * Create new instance of meta data class.
     *
     * @return \Yana\Views\MetaData\SkinMetaData
     */
    protected function _createMetaData()
    {
        return new \Yana\Views\MetaData\SkinMetaData();
    }

    /**
     * Fill meta data object with infos.
     *
     * This fills information on the meta data object based on the given XML.
     * It also adds the template meta data.
     *
     * @param   \Yana\Core\MetaData\IsPackageMetaData  $metaData  object that should be filled
     * @param   \Yana\Core\MetaData\XmlMetaData        $xml       provided XML meta data
     * @param   string                                 $id        identifier for the processed XML file to be loaded
     * @return  \Yana\Core\MetaData\IsPackageMetaData
     */
    protected function _fillMetaData(\Yana\Core\MetaData\IsPackageMetaData $metaData, \Yana\Core\MetaData\XmlMetaData $xml, $id)
    {
        assert('is_string($id)', ' Invalid argument $id: string expected');

        $metaData = parent::_fillMetaData($metaData, $xml, $id);

        if (!empty($xml)) {

            $directory = $this->_getDirectory() . '/';
            assert('!isset($template)', 'cannot redeclare variable $template');
            foreach ($xml->getTemplates($directory) as $template)
            {
                /* @var $template \Yana\Views\MetaData\TemplateMetaData */
                $metaData->addTemplate($template);
            }
            unset($template);
        }

        return $metaData;
    }

}

?>