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
 * <<trait>> Has cookie wrapper instance.
 *
 * @package     yana
 * @subpackage  core
 */
trait HasCookie
{

    /**
     * @var  \Yana\Core\Sessions\IsCookieWrapper
     */
    private $_cookie = null;

    /**
     * Retrieve cookie wrapper.
     *
     * @return  \Yana\Core\Sessions\IsCookieWrapper
     */
    public function getCookie(): \Yana\Core\Sessions\IsCookieWrapper
    {
        if (!isset($this->_cookie)) {
            $this->_cookie = new \Yana\Core\Sessions\CookieWrapper();
            $this->_cookie
                ->setLifetime(3600)
                ->setPath(dirname(filter_input(\INPUT_SERVER, 'SCRIPT_NAME', \FILTER_SANITIZE_STRING)))
                ->setIsSecure((bool) filter_input(\INPUT_SERVER, 'HTTPS', \FILTER_VALIDATE_BOOLEAN))
                ->setIsHttpOnly(false)
                ->setSameSite(\Yana\Core\Sessions\CookieWrapper::SAMESITE_STRICT);
        }
        return $this->_cookie;
    }

    /**
     * Inject cookie wrapper.
     *
     * @param   \Yana\Core\Sessions\IsCookieWrapper  $cookie  dependency
     * @return  $this
     */
    public function setCookie(\Yana\Core\Sessions\IsCookieWrapper $cookie)
    {
        $this->_cookie = $cookie;
        return $this;
    }

}

?>