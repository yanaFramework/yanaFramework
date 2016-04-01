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
 * <<facade>> Makes working with HTTP objects in this namespace easier.
 *
 * @package     yana
 * @subpackage  http
 */
class Facade extends \Yana\Core\Object implements \Yana\Http\Requests\IsRequest
{

    /**
     * @var  \Yana\Http\Requests\IsRequest
     */
    private $_request = null;

    /**
     * @var  \Yana\Http\Uploads\IsUploadWrapper
     */
    private $_files = null;

    /**
     * @var  \Yana\Http\Uris\IsUrlBuilder
     */
    private $_uri = null;

    /**
     * Inject objects.
     *
     * @param  \Yana\Http\Requests\IsRequest       $request  holds request vars and request method
     * @param  \Yana\Http\Uploads\IsUploadWrapper  $files    wraps $_FILES array
     * @param  \Yana\Http\Uris\IsUrlBuilder        $uri      builds canonical URLs
     */
    public function __construct(
        \Yana\Http\Requests\IsRequest $request = null, \Yana\Http\Uploads\IsUploadWrapper $files = null, \Yana\Http\Uris\IsUrlBuilder $uri = null
    ) {
        if (is_null($request)) {
            $request = \Yana\Http\Requests\Builder::buildFromSuperGlobals();
        }
        $this->_request = $request;
        if (is_null($files)) {
            $files = \Yana\Http\Uploads\Builder::buildFromSuperGlobals();
        }
        $this->_files = $files;
        if (is_null($uri)) {
            $uri = \Yana\Http\Uris\CanonicalUrlBuilder::buildFromSuperGlobals();
        }
        $this->_uri = $uri;
    }

    /**
     * Returns request object.
     *
     * @return  \Yana\Http\Requests\IsRequest
     */
    protected function _getRequest()
    {
        return $this->_request;
    }

    /**
     * Returns URL-builder object.
     *
     * @return  \Yana\Http\Uris\IsUrlBuilder
     */
    protected function _getUri()
    {
        return $this->_uri;
    }

    /**
     * Returns $_FILES wrapper.
     *
     * @return  \Yana\Http\Uploads\IsUploadWrapper
     */
    public function files()
    {
        return $this->_files;
    }

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
    public function all()
    {
        return $this->_getRequest()->all();
    }

    /**
     * Get ARGV values.
     *
     * @return  \Yana\Http\Requests\ValueWrapper
     */
    public function args()
    {
        return $this->_getRequest()->args();
    }

    /**
     * Get GET values.
     *
     * @return  \Yana\Http\Requests\ValueWrapper
     */
    public function get()
    {
        return $this->_getRequest()->get();
    }

    /**
     * Get POST values.
     *
     * @return  \Yana\Http\Requests\ValueWrapper
     */
    public function post()
    {
        return $this->_getRequest()->post();
    }

    /**
     * Get COOKIE values.
     *
     * @return  \Yana\Http\Requests\ValueWrapper
     */
    public function cookie()
    {
        return $this->_getRequest()->cookie();
    }

    /**
     * Get REQUEST values.
     *
     * @return  \Yana\Http\Requests\ValueWrapper
     */
    public function request()
    {
        return $this->_getRequest()->request();
    }

    /**
     * Get request method.
     *
     * @return  \Yana\Http\Requests\IsMethod
     */
    public function method()
    {
        return $this->_getRequest()->method();
    }

    /**
     * Returns bool(true) if the request was sent using AJAX.
     *
     * This relies on the "is_ajax_request" parameter being present, which is automaticall added,
     * if an AJAX request is sent using the API provided with the framework.
     *
     * @return  bool
     */
    public function isAjaxRequest()
    {
        return $this->_getRequest()->isAjaxRequest();
    }

    /**
     * Build a canonical URL.
     *
     * This function is more reliable than using $_SERVER['REQUEST_URI'].
     * Note however that it may still come up empty.
     * Be sure to handle empty results accordingly.
     *
     * @return  string
     */
    public function uri()
    {
        return $this->_getUri()->__invoke();
    }

}

?>