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

namespace Yana\Views\Helpers\Html;

/**
 * <<command>> Helper that converts an array to a HTML UL-element.
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
class MenuHelper extends \Yana\Core\Object
{

    /**
     * @var \Yana\Views\Helpers\Html\MenuLayouts\IsLayout
     */
    private $_layout = null;

    /**
     * @var int
     */
    private $_useKeys = \Yana\Views\Helpers\Html\MenuLayouts\KeyEnumeration::DONT_CONVERT_HREF;

    /**
     * @var bool
     */
    private $_allowHtml = false;

    /**
     * Select the layout to create.
     *
     * @param  \Yana\Views\Helpers\Html\MenuLayouts\IsLayout  $layout  a class that does the conversion
     */
    public function __construct(\Yana\Views\Helpers\Html\MenuLayouts\IsLayout $layout)
    {
        $this->_layout = $layout;
    }

    /**
     * <<factory>> Creates a MenuHelper.
     *
     * This function is here for your convenience.
     * It lets you choose between a number of known predefined layouts,
     * without having to look up class names.
     *
     * @param   int  $layoutNumber  select a registered layout
     * @return  \Yana\Views\Helpers\Html\MenuHelper
     */
    public static function factory($layoutNumber = 1)
    {
        switch ($layoutNumber)
        {
            case 2:
                $layout = new \Yana\Views\Helpers\Html\MenuLayouts\VerticalLayout();
                break;
            case 3:
                $layout = new \Yana\Views\Helpers\Html\MenuLayouts\HorizontalLayout();
                break;
            case 1:
            default:
                $layout = new \Yana\Views\Helpers\Html\MenuLayouts\SimpleLayout();
                break;
        }
        return new \Yana\Views\Helpers\Html\MenuHelper($layout);
    }

    /**
     * @return  \Yana\Views\Helpers\Html\MenuLayouts\IsLayout
     */
    protected function _getLayout()
    {
        return $this->_layout;
    }

    /**
     * Convert keys to href.
     *
     * 0 = no,
     * 1 = yes,
     * 2 = don't print keys at all
     *
     * @return  int
     */
    protected function _getUseKeys()
    {
        return $this->_useKeys;
    }

    /**
     * Convert keys to href.
     *
     * 0 = no,
     * 1 = yes,
     * 2 = don't print keys at all
     *
     * @param   int $useKeys
     * @return  \Yana\Views\Helpers\Html\MenuHelper
     * @see     \Yana\Views\Helpers\Html\MenuLayouts\KeyEnumeration
     */
    public function setUseKeys($useKeys)
    {
        assert('is_int($useKeys); // Invalid argument $useKeys: int expected');

        $this->_useKeys = (int) $useKeys;
        return $this;
    }

    /**
     * Allow HTML in values.
     *
     * false = escape HTML special chars,
     * true = don't escape HTML special chars
     *
     * @return bool
     */
    protected function _getAllowHtml()
    {
        return $this->_allowHtml;
    }

    /**
     * Allow HTML in values.
     *
     * false = escape HTML special chars,
     * true = don't escape HTML special chars
     *
     * @param   bool $allowHtml
     * @return  MenuHelper 
     */
    public function setAllowHtml($allowHtml)
    {
        assert('is_bool($allowHtml); // Invalid argument $allowHtml: bool expected');

        $this->_allowHtml = (bool) $allowHtml;
        return $this;
    }

    /**
     * <<command>> Print an array using a tree menu.
     *
     * Example for $value:
     * <code>
     * $A = array(
     *     '1.html' => 'Link',
     *     'Menu 1' => array(
     *         '2_1.html' => '1) Entry',
     *         '2_2.html' => '2) Entry',
     *         'Menü 2' => array(
     *             '2_3_1.html' => '1) Entry',
     *             '2_3_2.html' => '2) Entry'
     *         ),
     *     ),
     *     'Menu 3' => array(
     *         '3_1.html' => '1) Entry',
     *         '3_2.html' => '2) Entry',
     *         '3_2.html' => '3) Entry'
     *     ),
     * );
     * </code>
     *
     * @param   array  $array  list contents (see example above)
     * @return  string
     */
    public function __invoke(array $array)
    {
        return $this->_getLayout()->__invoke($array, $this->_getUseKeys(), $this->_getAllowHtml());
    }

}

?>