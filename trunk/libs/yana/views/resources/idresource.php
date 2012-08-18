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
 * @subpackage  core
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
        assert('is_string($id); // Wrong argument type argument 1. String expected');

        $skin = $this->_getSkin();
        if ($skin->isId($id)) {
            $id = $skin->getFile($id); // throws NotFoundException
        }
        parent::fetch($id, $output, $mtime);
    }

    /**
     * Retrieve last modification time of the requested resource
     *
     * @param   string  $id       the template id
     * @return  int
     */
    protected function fetchTimestamp($id)
    {
        assert('is_string($id); // Wrong argument type argument 1. String expected');
        $skin = $this->_getSkin();
        if ($skin->isId($id)) {
            $this->_loadDependencies($id);
            $id = $this->_getSkin()->getFile($id); // throws NotFoundException
        }
        return parent::fetchTimestamp($id);
    }

    /**
     * load dependencies for template
     *
     * This function takes the name of a template, looks up any language files,
     * scripts and stylesheets that the template depends on and loads them.
     *
     * @access  public
     * @param   string  $key  template id
     * @throws  \Yana\Core\Exceptions\NotFoundException when the given template id does not exist
     */
    private function _loadDependencies($key)
    {
        assert('is_string($key); // Wrong type for argument 1. String expected');
        $key = mb_strtoupper("$key");

        $skin = $this->_getSkin();

        // load language files associated with the template
        $language = \Yana\Translations\Language::getInstance();
        foreach ($skin->getLanguage($key) as $languageFile)
        {
            try {

                $language->readFile($languageFile); // may throw exception

            } catch (\Yana\Core\Exceptions\Translations\TranslationException $e) {
                // no need to throw an exception here, since we may safely continue without the file
            }
        }

        $manager = $this->_getViewManager();

        // prepare a list of css styles associated with the template
        $manager->addStyles($skin->getStyle($key));

        // prepare a list of javascript files associated with the template
        $manager->addScripts($skin->getScript($key));
    }

}

?>