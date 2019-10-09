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
 * $smarty->register_resource("template",
 *   array("SmartFileResource::getTemplate",
 *     "SmartFileResource::getTimestamp",
 *     "SmartFileResource::isSecure",
 *     "SmartFileResource::isTrusted"
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
 * {import file="template:template_id"}
 * </code>
 *
 * @package     yana
 * @subpackage  views
 */
class FileResource extends \Yana\Views\Resources\AbstractResource
{

    /**
     * @var \Yana\Views\Skins\Skin
     */
    private $_skin = null;

    /**
     * Retrieves and returns the skin/themes repository.
     *
     * @return \Yana\Views\Skins\Skin
     */
    protected function _getSkin()
    {
        if (!isset($this->_skin)) {
            assert(!isset($builder), 'Cannot redeclare var $builder');
            assert(!isset($application), 'Cannot redeclare var $application');
            $builder = new \Yana\ApplicationBuilder();
            $application = $builder->buildApplication();
            unset($builder);
            $this->_skin = $application->getSkin();
            unset($application);
        }
        return $this->_skin;
    }

    /**
     * Fetch template and its modification time from data source.
     *
     * @param string  $filename template name
     * @param string  &$source  template source
     * @param integer &$mtime   template modification timestamp (epoch)
     */
    protected function fetch($filename, &$output, &$mtime)
    {
        assert(is_string($filename), 'Wrong argument type argument 1. String expected');
        if (is_file($filename)) {
            $mtime = filemtime($filename);
            $fileContents = file_get_contents($filename);
            $baseDir = \dirname($filename) . '/';
            $filter = new \Yana\Views\Resources\Helpers\RelativePathsFilter($this->_getDependencyContainer());
            $output = $filter($fileContents, $baseDir);
        }
    }

    /**
     * Fetch template's modification timestamp from data source.
     *
     * Returns the timestamp when the template was modified, or false if not found.
     *
     * @param   string $filename template name
     * @return  int
     */
    protected function fetchTimestamp($filename)
    {
        assert(is_string($filename), 'Wrong argument type argument 1. String expected');
        if (is_file($filename)) {
            return filemtime($filename);
        } else {
            return false;
        }
    }

}

?>