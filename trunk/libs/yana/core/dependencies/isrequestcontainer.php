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
declare(strict_types=1);

namespace Yana\Core\Dependencies;

/**
 * <<interface>> Dependency container for the form facade.
 *
 * @package     yana
 * @subpackage  core
 */
interface IsRequestContainer
{

    /**
     * Builds and returns request object.
     *
     * By default this will be done by using the respective super-globals like $_GET, $_POST aso.
     *
     * @return  \Yana\Http\IsFacade
     */
    public function getRequest();

    /**
     * Builds and returns request helper object.
     *
     * @return  \Yana\Http\Requests\IsRequest
     */
    public function getRequestBuilder();

    /**
     * Builds and returns upload helper object.
     *
     * @return  \Yana\Http\Uploads\IsUploadWrapper
     */
    public function getUploadBuilder();

    /**
     * Builds and returns URL helper object.
     *
     * @return  \Yana\Http\Uris\IsUrlBuilder
     */
    public function getUrlBuilder();

}

?>