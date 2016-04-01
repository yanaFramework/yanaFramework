<?php
/**
 * YANA library
 *
 * Primary controller class
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

namespace Yana\Http\Uris;

/**
 * <<interface>> Build canonical request URIs.
 *
 * @package     yana
 * @subpackage  http
 */
interface IsUrlBuilder extends \Yana\Http\Uris\IsContainer
{

    /**
     * Get canonical request URI.
     *
     * Returns a canonical request URI that is identical, no matter if the data was sent via GET,
     * or POST. You may use this function to calculate IDs or identify the page.
     *
     * The request URI is the URI which was given in order to access this page; like '/index.php'.
     *
     * @return  string
     */
    public function __invoke();

}

?>