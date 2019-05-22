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

namespace Yana\Core\Dependencies;

/**
 * <<trait>> Request object dependencies.
 *
 * @package     yana
 * @subpackage  core
 *
 * @ignore
 */
trait HasRequest
{

    /**
     * @var  \Yana\Http\Facade
     */
    private $_request = null;

    /**
     * @var  \Yana\Http\Uris\IsUrlBuilder
     */
    private $_urlBuilder = null;

    /**
     * @var  \Yana\Http\Uploads\IsUploadWrapper
     */
    private $_uploadBuilder = null;

    /**
     * @var  \Yana\Http\Requests\IsRequest
     */
    private $_requestBuilder = null;

    /**
     * Builds and returns request object.
     *
     * By default this will be done by using the respective super-globals like $_GET, $_POST aso.
     *
     * @return  \Yana\Http\Facade
     */
    public function getRequest()
    {
        if (!isset($this->_request)) {
            $this->_request = new \Yana\Http\Facade($this->getRequestBuilder(), $this->getUploadBuilder(), $this->getUrlBuilder());
        }
        return $this->_request;
    }

    /**
     * Builds and returns request helper object.
     *
     * @return  \Yana\Http\Requests\IsRequest
     */
    public function getRequestBuilder()
    {
        if (!isset($this->_requestBuilder)) {
            $this->_requestBuilder = \Yana\Http\Requests\Builder::buildFromSuperGlobals();
        }
        return $this->_requestBuilder;
    }

    /**
     * Builds and returns upload helper object.
     *
     * @return  \Yana\Http\Uploads\IsUploadWrapper
     */
    public function getUploadBuilder()
    {
        if (!isset($this->_uploadBuilder)) {
            $this->_uploadBuilder = \Yana\Http\Uploads\Builder::buildFromSuperGlobals();
        }
        return $this->_uploadBuilder;
    }

    /**
     * Builds and returns URL helper object.
     *
     * @return  \Yana\Http\Uris\IsUrlBuilder
     */
    public function getUrlBuilder()
    {
        if (!isset($this->_urlBuilder)) {
            $this->_urlBuilder = \Yana\Http\Uris\CanonicalUrlBuilder::buildFromSuperGlobals();
        }
        return $this->_urlBuilder;
    }

}

?>