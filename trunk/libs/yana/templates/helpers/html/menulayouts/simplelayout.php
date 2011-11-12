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

namespace Yana\Templates\Helpers\Html\MenuLayouts;

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
 * @subpackage  templates
 */
class SimpleLayout extends \Yana\Templates\Helpers\Html\MenuLayouts\AbstractLayout
{

    /**
     * Simple foldable tree display.
     *
     * This implements layout #1.
     *
     * Prints an (multidimensional) array of elements to HTML using a simple tree desing.
     * Use this to display array-contents in logging or for general purposes.
     *
     * A screenshot can be found in the online manual.
     *
     * @param   array   $array      data
     * @param   int     $keys       convert keys to href
     * @param   bool    $allowHtml  allow HTML values
     * @return  string
     * @see     \Yana\Templates\Helpers\Html\MenuLayouts\KeyEnumeration
     */
    public function __invoke(array $array, $keys = KeyEnumeration::DONT_CONVERT_HREF, $allowHtml = false)
    {
        $textFormatter = new \Yana\Templates\Helpers\Formatters\TextFormatter();
        $ul = '<ul class="gui_array_list">';
        foreach ($array as $key => $element)
        {
            /* print key */
            if (is_array($element)) {
                $ul .= '<li class="gui_array_head" onmouseover="this.className=\'gui_array_head_open\'" ' .
                    'onmouseout="this.className=\'gui_array_head\'">';
                $ul .= '<span class="gui_array_key">';
                $ul .= htmlspecialchars($key, ENT_COMPAT, 'UTF-8');
                $ul .= '</span>';
            } else {
                $ul .= '<li class="gui_array_list">';
                if ($keys === 2) {
                    /* intentionally left blank */
                } elseif ($keys == 1 && is_scalar($element)) {
                    $ul .= '<a href="' . htmlspecialchars($key, ENT_COMPAT, 'UTF-8') . '">';
                } else {
                    $ul .= '<span class="gui_array_key">';
                    if ($allowHtml) {
                        $ul .= $key;
                    } else {
                        $ul .= htmlspecialchars($key, ENT_COMPAT, 'UTF-8') . ':';
                    }
                    $ul .= '</span>';
                }
            }

            /* print value */
            if (is_bool($element)) {
                $ul .= '<span class="gui_array_value">';
                if ($element) {
                    $ul .= '<span class="icon_true">&nbsp;</span>';
                } else {
                    $ul .= '<span class="icon_false">&nbsp;</span>';
                }
                $ul .= '</span>';
            } elseif (is_string($element)) {
                if (!$allowHtml) {
                    $ul .= '<span class="gui_array_value">' .
                        $textFormatter(htmlspecialchars($element, ENT_COMPAT, 'UTF-8')) . '</span>';
                } else {
                    $ul .= '<span class="gui_array_value">' . $element . '</span>';
                }
            } elseif (is_scalar($element)) {
                $ul .= '<span class="gui_array_value">' . htmlspecialchars($element, ENT_COMPAT, 'UTF-8') . '</span>';
            } elseif (is_array($element)) {
                $ul .= $this->__invoke($element, $keys, $allowHtml);
            } elseif (is_object($element)) {
                $ul .= '<span class="gui_array_value">' . htmlspecialchars((string) $element, ENT_COMPAT, 'UTF-8') . '</span>';
            } else {
                $ul .= '<span class="gui_array_value">' . htmlspecialchars(print_r($element, true), ENT_COMPAT, 'UTF-8') .
                    '</span>';
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