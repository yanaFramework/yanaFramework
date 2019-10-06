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

namespace Yana\Views\Helpers\Html\MenuLayouts;

/**
 * <<strategy>> Helper that converts an array to a HTML UL-element.
 *
 * Note: The output requires the given CSS-file to be loaded to function properly.
 * To support older browers, use the provided JavaScript files.
 *
 * You may replace the CSS by your own to create alternative layouts.
 * The following CSS classes are used:
 * <ul>
 *  <li> ul.gui_array_list </li>
 *  <li> ul.gui_array_list &gt; li.gui_array_list </li>
 *  <li> ul.gui_array_list &gt; li.gui_array_head </li>
 *  <li> ul.gui_array_list &gt; li.gui_array_list &gt; span.gui_array_key </li>
 *  <li> ul.gui_array_list &gt; li.gui_array_list &gt; span.gui_array_value </li>
 *  <li> ul.gui_array_list &gt; li.gui_array_head &gt; span.gui_array_key </li>
 *  <li> ul.gui_array_list &gt; li.gui_array_head &gt; span.gui_array_value </li>
 * </ul>
 *
 * @package     yana
 * @subpackage  views
 */
class VerticalLayout extends \Yana\Views\Helpers\Html\MenuLayouts\AbstractLayout
{

    /**
     * Vertical, clickable menu.
     *
     * This implements layout #2.
     *
     * Converts the array elements to a vertical menu with foldable submenus and clickable items.
     * Items are listed vertical, menues are opened to the right on click.
     *
     * A screenshot can be found in the online manual.
     *
     * @param   array   $array      data
     * @param   int     $keys       convert keys to href
     * @param   bool    $allowHtml  allow HTML values
     * @param   bool    $isRoot     (true = root , false otherweise)
     * @return  string
     * @see     \Yana\Views\Helpers\Html\MenuLayouts\KeyEnumeration
     */
    public function __invoke(array $array, $keys = KeyEnumeration::DONT_CONVERT_HREF, $allowHtml = false, $isRoot = true)
    {
        if ($isRoot) {
            $ul = '<ul class="menu root">';
        } else {
            $ul = '<ul class="menu">';
        }
        foreach ($array as $key => $element)
        {
            /* print key */
            if (is_array($element)) {
                $ul .= '<li class="menu">';
                $ul .= '<div class="menu_head" onclick="yanaMenu(this)">' .
                    \Yana\Util\Strings::htmlSpecialChars((string) $key) . '</div>';
            } else {
                $ul .= '<li class="entry">';
                if ($keys === 2) {
                    /* intentionally left blank */
                } elseif ($keys == 1 && is_scalar($element)) {
                    $ul .= '<a href="'.\Yana\Util\Strings::htmlSpecialChars((string) $key).'">';
                } else {
                    $ul .= '<span class="gui_array_key">';
                    if ($allowHtml) {
                        $ul .= $key;
                    } else {
                        $ul .= \Yana\Util\Strings::htmlSpecialChars((string) $key).':';
                    }
                    $ul .= '</span>';
                }
            }

            /* print value */
            if (is_array($element)) {
                $ul .= $this->__invoke($element, $keys, $allowHtml, false);
            } else {
                if ($keys == 0) {
                    $ul .= '<span class="gui_array_value">';
                }
                if (is_bool($element)) {
                    if ($element) {
                        $ul .= '<span class="icon_true">&nbsp;</span>';
                    } else {
                        $ul .= '<span class="icon_false">&nbsp;</span>';
                    }
                } elseif (is_scalar($element)) {
                    if ($allowHtml) {
                        $ul .= $element;
                    } else {
                        $ul .= \Yana\Util\Strings::htmlSpecialChars((string) $element);
                    }
                } else {
                    $ul .= \Yana\Util\Strings::htmlSpecialChars((string) $element);
                }
                if ($keys == 0) {
                    $ul .= '</span>';
                }
            }

            /* close open 'a' tag */
            if ($keys == 1 && is_scalar($element)) {
                $ul .= '</a>';
            }
            $ul .= '</li>';
        }
        $ul .= '</ul>';
        return $ul;
    }

}

?>