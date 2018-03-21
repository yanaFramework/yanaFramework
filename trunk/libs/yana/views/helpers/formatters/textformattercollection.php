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
 * <<formatter>> This class combines the icon- and text-formatter for convenience.
 *
 * @package     yana
 * @subpackage  views
 */
class TextFormatterCollection extends FormatterCollection
{

    /**
     * Add default entries.
     *
     * Presets the IconFormatter and TextFormatter as members of the collection.
     */
    public function __construct()
    {
        $this[] = new \Yana\Views\Helpers\Formatters\IconFormatter();
        $this[] = new \Yana\Views\Helpers\Formatters\TextFormatter();
    }

}

?>