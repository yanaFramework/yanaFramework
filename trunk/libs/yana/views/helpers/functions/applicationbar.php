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
     * The quick-launch bar is an icon bar that is intended to be an eye-catcher.
     * It should, however, never be the sole means of navigation.
     *
     * @param   array                      $params  any list of arguments
     * @param   \Smarty_Internal_Template  $smarty  reference to currently rendered template
     * @return  scalar
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty)
    {
        assert(!isset($result), 'Cannot redeclare var $result');
        $result = "";

        assert(!isset($pluginMenu), 'Cannot redeclare var $pluginMenu');
        $pluginMenu = $this->_getMenuBuilder()->buildMenu(); // using default settings

        assert(!isset($formatter), 'Cannot redeclare var $formatter');
        $formatter = $this->_getDependencyContainer()->getUrlFormatter();
        assert(!isset($template), 'Cannot redeclare var $template');
        $template = '<a class="applicationBar" href="' . $formatter("action=", false, false) . '%s">' .
            '<img src="%s" alt="%s"/><span class="applicationBarLabel">%s</span></a>';

        /* The current working/base directory */
        assert(!isset($baseDirectory), 'Cannot redeclare var $baseDirectory');
        $baseDirectory = \str_replace('\\', '/', \YANA_INSTALL_DIR); /* replace Windows-style directory seperators */

        assert(!isset($action), 'Cannot redeclare var $action');
        assert(!isset($entry), 'Cannot redeclare var $entry');
        assert(!isset($title), 'Cannot redeclare var $title');
        assert(!isset($icon), 'Cannot redeclare var $icon');
        assert(!isset($iconUri), 'Cannot redeclare var $iconUri');
        foreach ($pluginMenu->getMenuEntries('start') as $action => $entry)
        {
            /* @var $entry \Yana\Plugins\Menus\IsEntry */
            $title = $entry->getTitle();
            $icon = \str_replace('\\', '/', $entry->getIcon()); /* replace Windows-style directory seperators */

            if (!is_file($icon)) {
                /**
                 * You may wonder why we skip the entire entry if we can't find the icon.
                 * The answer is: The application bar is an ICON bar. An entry in an icon bar that is no icon doesn't work.
                 *
                 * Yes, we could have fallen back on a default icon. We did not. Deal with it. ;-)
                 */
                // @codeCoverageIgnoreStart
                continue;
                // @codeCoverageIgnoreEnd
            }
            assert(!isset($iconUri), 'Cannot redeclare var $iconUri');

            /**
             * While the string $icon is usually a relative path, it could also be an absolute file path instead: including
             * the current working directory as a prefix. We don't want that prefix, so the following code gets rid of it.
             *
             * This is usually removed elsewhere automatically, so in practice we could rely on this side-effect.
             * We could - but we should (and will) not.
             *
             * Reason 1: In unit tests this would not work and we would get an absolute path string anyway.
             * We don't want the function to behave differently in unit tests because we relied on a side-effect we don't control.
             *
             * Reason 2: If for whatever reason the side-effect that is controlled elsewhere in the code is removed or no longer
             * called, not handling the prefix would mean that the icon URL is not just invalid, it would also reveal the
             * path of the htdocs directory on the server. We do NOT want that to happen.
             *
             * So in other words: Even though this line may look like it is not necessary, it most certainly is. LEAVE IT ALONE!
             */
            $iconUri = preg_replace('/^' . preg_quote($baseDirectory, '/') . '/', '', $icon);

            $result .= sprintf($template, $action, $iconUri, $title, $title);
        } // end foreach
        unset($action, $entry, $title, $icon, $iconUri);

        return $this->_getLanguage()->replaceToken($result);
    }

}

?>