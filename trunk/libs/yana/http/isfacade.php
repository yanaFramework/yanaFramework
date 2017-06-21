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

namespace Yana\Http;

/**
 * <<interface>> Manages safe access to HTTP request values.
 *
 * @package     yana
 * @subpackage  http
 */
interface IsFacade extends \Yana\Http\Requests\IsRequest
{

    /**
     * Returns $_FILES wrapper.
     *
     * @return  \Yana\Http\Uploads\IsUploadWrapper
     */
    public function files();

    /**
     * Build a canonical URL.
     *
     * This function is more reliable than using $_SERVER['REQUEST_URI'].
     * Note however that it may still come up empty.
     * Be sure to handle empty results accordingly.
     *
     * @return  string
     */
    public function uri();

    /**
     * Returns value of the "action" parameter.
     *
     * Checks both GET and POST parameters and returns an empty string if there is none.
     *
     * @return  string
     */
    public function getActionArgument();

    /**
     * Returns value of the "action" parameter.
     *
     * Checks both GET and POST parameters and returns an empty string if there is none.
     *
     * @return  string
     */
    public function getProfileArgument();
}

?>