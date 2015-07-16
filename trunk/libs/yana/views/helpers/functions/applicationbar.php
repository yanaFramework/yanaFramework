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

namespace Yana\Views\Helpers\Functions;

/**
 * Smarty-compatible function.
 *
 * This class is registered when instantiating the Smarty Engine.
 *
 * @package     yana
 * @subpackage  views
 */
class ApplicationBar extends \Yana\Views\Helpers\AbstractViewHelper implements \Yana\Views\Helpers\IsFunction
{

    /**
     * <<smarty function>> Create HTML apps quick-launch bar.
     *
     * @param   array                      $params  any list of arguments
     * @param   \Smarty_Internal_Template  $smarty  reference to currently rendered template
     * @return  scalar
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty)
    {
        $result = "";

        $builder = new \Yana\Plugins\Menus\Builder();
        $pluginMenu = $builder->buildMenu(); // using default settings
        unset($builder);

        $formatter = new \Yana\Views\Helpers\Formatters\UrlFormatter();
        $template = '<a class="applicationBar" href="' . $formatter("action=", false, false) . '%s">' .
            '<img src="%s" title="%s" alt="%s"/></a>';

        /* @var $entry PluginMenuEntry */
        foreach ($pluginMenu->getMenuEntries('start') as $action => $entry)
        {
            $title = $entry->getTitle();
            $icon = $entry->getIcon();

            if (!is_file($icon)) {
                continue;
            }
            $result .= sprintf($template, $action, $icon, $title, $title);
        } // end foreach

        $language = \Yana\Application::getInstance()->getLanguage();
        return $language->replaceToken($result);
    }

}

?>