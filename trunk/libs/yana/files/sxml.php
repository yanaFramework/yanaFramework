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
 */
declare(strict_types=1);

namespace Yana\Files;

/**
 * Simple XML Files
 *
 * This is a wrapper-class that may be used to work with *.xml files.
 * However these files must be convertible to an array and vice-versa.
 *
 * Real-world example:
 * <pre>
 * &lt;calendar&gt;
 *   &lt;categories&gt;
 *     &lt;item id="white"&gt;#ffffff&lt;/item&gt;
 *     &lt;item id="vacation"&gt;#00aa00&lt;/item&gt;
 *   &lt;/categories&gt;
 *   &lt;timezone&gt;1&lt;/timezone&gt;
 *   &lt;dst&gt;0&lt;/dst&gt;
 * &lt;/calendar&gt;
 * </pre>
 *
 * <code>
 * $config = new SXML('calendar.xml');
 * $dst = $config->getVar('dst');
 * if ($dst == '1') {
 *     // do something
 * }
 * // loop through categories
 * foreach ($config->getVar('categories.item') as $category)
 * {
 *     print $category['@id'] . '=' . $category['#pcdata'];
 * }
 * // or ...
 * $array = $config->getVars();
 * foreach ($array['categories']['item'] as $category)
 * {
 *     print $category['@id'] . '=' . $category['#pcdata'];
 * }
 * </code>
 *
 * Note: If you prefer to work with objects instead of arrays, give SimpleXML a try instead.
 * See the PHP manual for details.
 *
 * @package     yana
 * @subpackage  files
 * @since       3.1.0
 */
class SXML extends \Yana\Files\AbstractVarContainer
{

    /**
     * Returns an SXML decoder.
     *
     * @return  \Yana\Files\Decoders\IsDecoder
     */
    protected static function _getDecoder(): \Yana\Files\Decoders\IsDecoder
    {
        return new \Yana\Files\Decoders\SXML();
    }

}

?>