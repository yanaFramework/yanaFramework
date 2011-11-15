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

namespace Yana\Views\Helpers\Formatters;

/**
 * <<formatter>> This class encapsulates an extension for HTML creation.
 *
 * @package     yana
 * @subpackage  views
 */
class IconFormatter extends \Yana\Views\Helpers\AbstractIconHelper implements \Yana\Views\Helpers\IsFormatter
{

    /**
     * Create HTML from a unix timestamp.
     *
     * @param   string  $string  HTML text
     * @return  string
     */
    public function __invoke($string)
    {
        assert('is_string($string); // Invalid argument $string: string expected');

        /* if not necessary -> skip the whole section for better performance */
        if (mb_strpos($string, ':') !== false) {
            /* Emot-Codes */
            foreach (self::$_icons as $fileName => $regEx)
            {
                while (preg_match("/:" . $regEx . ":(\s|\[wbr\]|\[br\]|<br \/>)*:" . $regEx . ":/i", $string))
                {
                    $pattern = "/:" . $regEx . ":(\s|\[wbr\]|\[br\]|<br \/>)*:" . $regEx . ":/i";
                    $string = preg_replace($pattern, ':' . $regEx . ':', $string);
                }
                $pattern = "/:" . addcslashes($regEx, "+()[]{}.?*/\\$^") . ":/";
                $replacement = '<img alt="" border="0" hspace="2" src="' . self::$_dir . $fileName . '.gif"/>';
                $string = preg_replace($pattern, $replacement, $string);
            }
        }

        return $string;
    }

}

?>