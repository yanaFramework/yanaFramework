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

namespace Yana\Views\Icons;

/**
 * <<interface>> Contains information about an icon file.
 *
 * @package     yana
 * @subpackage  views
 */
interface IsFile extends \Yana\Data\Adapters\IsEntity
{

    /**
     * Returns path to file.
     *
     * @return  string
     */
    public function getPath();

    /**
     * Set the path to file.
     *
     * @param   string  $path  file path or URL
     * @return  self
     */
    public function setPath($path);

    /**
     * Returns regex to find matching icon reference in text.
     *
     * @return  string
     */
    public function getRegularExpression();

    /**
     * Returns regex to find matching icon reference in text.
     *
     * @param   string  $regex  Perl-compatible regular expression
     * @return  self
     */
    public function setRegularExpression($regex);

}

?>