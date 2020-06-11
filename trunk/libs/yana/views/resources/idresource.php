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

namespace Yana\Views\Resources;

/**
 * <<utility>> Smarty id-resource.
 *
 * This is a resource wrapper class for use with the smarty template engine.
 *
 * To register use this code:
 * <code>
 * $smarty->register_resource("id",
 *   array("SmartIdResource::getTemplate",
 *     "SmartIdResource::getTimestamp",
 *     "SmartIdResource::isSecure",
 *     "SmartIdResource::isTrusted"
 *   )
 * );
 * </code>
 *
 * To use the ressource wrapper, call Smarty as follows:
 * <code>
 * $smarty->display("id:template_id");
 * </code>
 *
 * May even be used in templates:
 * <code>
 * {import file="id:template_id"}
 * </code>
 *
 * @package     yana
 * @subpackage  views
 */
class IdResource extends \Yana\Views\Resources\FileResource
{

    /**
     * Retrieve the resource.
     *
     * @param string  $id       template name
     * @param string  &$source  template source
     * @param integer &$mtime   template modification timestamp (epoch)
     */
    protected function fetch($id, &$output, &$mtime)
    {
        assert(is_string($id), 'Wrong argument type argument 1. String expected');

        try {

            $fileName = $this->_getSkin()->getTemplateData($id)->getFile(); // throws NotFoundException
            parent::fetch($fileName, $output, $mtime);

        } catch (\Yana\Core\Exceptions\NotFoundException $e) {
            unset($e); // So this was not a valid template id
        }
    }

    /**
     * Retrieve last modification time of the requested resource.
     *
     * @param   string  $id  the template id
     * @return  int
     */
    protected function fetchTimestamp($id)
    {
        assert(is_string($id), 'Wrong argument type argument 1. String expected');

        try {

            $templateData = $this->_getSkin()->getTemplateData($id); // may throw NotFoundException
            $this->_loadDependencies($templateData);
            $fileName = $templateData->getFile();
            return parent::fetchTimestamp($fileName);

        } catch (\Yana\Core\Exceptions\NotFoundException $e) { // this must not throw an exception
            unset($e);
            return false; // So this is not a template
        }
    }

    /**
     * Load dependencies for template.
     *
     * This function looks up any language files, scripts and stylesheets that the template depends on and loads them.
     *
     * @param   \Yana\Views\MetaData\TemplateMetaData  $templateData  template definition
     * @throws  \Yana\Core\Exceptions\NotFoundException when the given template id does not exist
     */
    private function _loadDependencies(\Yana\Views\MetaData\TemplateMetaData $templateData)
    {
        // load language files associated with the template
        $language = $this->_getLanguage();
        foreach ($templateData->getLanguages() as $languageFile)
        {
            try {

                $language->readFile($languageFile); // may throw exception

            } catch (\Yana\Core\Exceptions\Translations\TranslationException $e) {
                unset($e); // no need to throw an exception here, since we may safely continue without the file
            }
        }
        unset($language, $languageFile);

        $manager = $this->_getViewManager();

        // prepare a list of css styles associated with the template
        $manager->addStyles($templateData->getStyles());

        // prepare a list of javascript files associated with the template
        $manager->addScripts($templateData->getScripts());
    }

}

?>