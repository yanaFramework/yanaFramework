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

namespace Yana\Templates\Helpers\Functions;

/**
 * Smarty-compatible function.
 *
 * This class is registered when instantiating the Smarty Engine.
 *
 * @package     yana
 * @subpackage  templates
 */
class Rss extends \Yana\Core\Object implements \Yana\Templates\Helpers\IsFunction
{

    /**
     * <<smarty function>> Create HTML RSS link.
     *
     * @param   array                      $params  any list of arguments
     * @param   \Smarty_Internal_Template  $smarty  reference to currently rendered template
     * @return  scalar
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty)
    {
        $yana = \Yana::getInstance();
        if (isset($params['image'])) {
            $image = (string) $params['image'];
        } else {
            $image = $yana->getVar('DATADIR') .'rss.gif';
        }
        $title = $yana->getLanguage()->getVar('RSS_TITLE');
        $name = $yana->getLanguage()->getVar('PROGRAM_TITLE');
        $result = "";
        foreach (\Yana\RSS\Publisher::getFeeds() as $action)
        {
            $result .= '<a title="' . $name . ': ' . $title . '" href="' . self::url("action={$action}") . '">' .
            '<img alt="RSS" src="' . $image . '"/></a>';
        }
        return $result;
    }

}

?>