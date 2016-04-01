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

namespace Yana\Http\Requests;

/**
 * <<interface>> Manages safe access to HTTP request values.
 *
 * @package     yana
 * @subpackage  http
 */
interface IsRequest
{

    /**
     * Get all values.
     *
     * This is as a combination of "get" and "post" vars, where "post" vars take precedence over "get" vars.
     * It doesn't include "cookie" vars.
     *
     * For command-line calls this be set to the "argv" array, presented as key-value pairs.
     *
     * @return  \Yana\Http\Requests\ValueWrapper
     */
    public function all();

    /**
     * Get ARGV values.
     *
     * @return  \Yana\Http\Requests\ValueWrapper
     */
    public function args();

    /**
     * Get GET values.
     *
     * @return  \Yana\Http\Requests\ValueWrapper
     */
    public function get();

    /**
     * Get POST values.
     *
     * @return  \Yana\Http\Requests\ValueWrapper
     */
    public function post();

    /**
     * Get COOKIE values.
     *
     * @return  \Yana\Http\Requests\ValueWrapper
     */
    public function cookie();

    /**
     * Get REQUEST values.
     *
     * @return  \Yana\Http\Requests\ValueWrapper
     */
    public function request();

    /**
     * Get request method.
     *
     * @return  \Yana\Http\Requests\IsMethod
     */
    public function method();

    /**
     * Returns bool(true) if the request was sent using AJAX.
     *
     * This relies on the "is_ajax_request" parameter being present, which is automaticall added,
     * if an AJAX request is sent using the API provided with the framework.
     *
     * @return  bool
     */
    public function isAjaxRequest();
}

?>