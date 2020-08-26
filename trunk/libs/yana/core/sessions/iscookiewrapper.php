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

namespace Yana\Core\Sessions;

/**
 * <<wrapper>> Cookie wrapper.
 *
 * This class is an OO-wrapper around php's functions.
 *
 * @package     yana
 * @subpackage  core
 * @link http://php.net/manual/en/function.session-set-cookie-params.php
 */
interface IsCookieWrapper extends \Yana\Core\IsCountableArray
{

    /**
     * Set cookie lifetime.
     *
     * Set to int(0) to create a cookie that is destroyed by the end of the session.
     *
     * @param   int  $lifetime  Lifetime of the cookie, defined in seconds. 0 = session cookie
     * @return  $this
     */
    public function setLifetime(int $lifetime);

    /**
     * Get cookie lifetime.
     *
     * Lifetime of the cookie, defined in seconds. 0 = session cookie.
     *
     * @return  int
     */
    public function getLifetime(): int;

    /**
     * Returns the server path the cookie is limited to.
     *
     * Path of the application it corresponds to, default is "/".
     *
     * @return string
     */
    public function getPath(): string;

    /**
     * Get the domain the cookie is limited to.
     *
     * Domain hat the cookie is valid for.
     * Default is "" (if this isn't your domain, something is seriously wrong).
     *
     * @return string
     */
    public function getDomain(): string;

    /**
     * Returns whether to hide cookie from scripts.
     *
     * If TRUE, the cookie will not be sent using JavaScript, default is FALSE.
     * (if this is TRUE, you have not been contacted using a script, or you wouldn't have gotten the cookie).
     *
     * @return bool
     */
    public function isHttpOnly(): bool;

    /**
     * Returns whether to send cookie over secure connections only.
     *
     * If TRUE, then the cookie contents are only sent via SSL, default is FALSE.
     * (if this is TRUE then the request was made using SSL or you wouldn't be seeing the cookie).
     * 
     * @return bool
     */
    public function isSecure(): bool;

    /**
     * Returns same site policy.
     *
     * @return string
     */
    public function getSameSite(): string;

    /**
     * Limit the cookie to a path on the domain.
     *
     * @param   string  $path  must be a valid server path
     * @return  $this
     */
    public function setPath(string $path);

    /**
     * Limit the cookie to a domain name.
     *
     * @param   string  $domain  must start with "." if sub-domain are to be included
     * @return  $this
     */
    public function setDomain(string $domain);

    /**
     * Set wether the cookie should be hidden from scripts.
     *
     * @param   bool  $isHttpOnly  set to TRUE to prevent JavaScript from seeing the cookie
     * @return  $this
     */
    public function setIsHttpOnly(bool $isHttpOnly);

    /**
     * Set wether the cookie should be shared only via secure connections.
     *
     * @param   bool  $isSecure  set to TRUE for cookies to be valid via HTTPS only
     * @return  $this
     */
    public function setIsSecure(bool $isSecure);

    /**
     * Set same-site cookie policy.
     * 
     * @param   string  $sameSite  can be "none", "lax" or "strict"
     * @return  $this
     */
    public function setSameSite(string $sameSite);
}

?>