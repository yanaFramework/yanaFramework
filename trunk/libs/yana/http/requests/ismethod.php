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
 * <<interface>> Value-object to handle request methods.
 *
 * @package     yana
 * @subpackage  http
 */
interface IsMethod
{

    /**
     * @return bool
     */
    public function isPost();

    /**
     * @return bool
     */
    public function isGet();

    /**
     * Returns bool(true) if the request method is PUT.
     *
     * @return  bool
     */
    public function isPut();

    /**
     * Returns bool(true) if the request method is DELETE.
     *
     * @return  bool
     */
    public function isDelete();

    /**
     * Returns bool(true) if the request method is likely to have been made from CLI.
     *
     * @return  bool
     */
    public function isCommandLine();
}

?>