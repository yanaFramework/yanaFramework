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
class CookieWrapper extends \Yana\Core\StdObject implements \Yana\Core\Sessions\IsCookieWrapper
{

    private $_lifetime = 0;
    private $_path = "/";
    private $_domain = "";
    private $_isHttpOnly = false;
    private $_isSecure = false;
    private $_sameSite = "lax";

    const SAMESITE_NONE = "none";
    const SAMESITE_LAX = "lax";
    const SAMESITE_STRICT = "strict";

    /**
     * <<constructor>> Initialize session vars.
     *
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        if (!isset($_COOKIE)) {
            $_COOKIE = array();
        }
        if (\is_numeric(\ini_get('session.cookie_lifetime'))) {
            $this->_lifetime = (int) \ini_get('session.cookie_lifetime');
        }
        if (\is_string(\ini_get('session.cookie_path'))) {
            $this->_path = (string) \ini_get('session.cookie_path');
        }
        if (\is_string(\ini_get('session.cookie_domain'))) {
            $this->_domain = (string) \ini_get('session.cookie_domain');
        }
        if (\is_bool(\filter_var(\ini_get('session.cookie_secure'), \FILTER_VALIDATE_BOOLEAN))) {
            $this->_isSecure = \filter_var(\ini_get('session.cookie_secure'), \FILTER_VALIDATE_BOOLEAN);
        }
        if (\is_bool(\filter_var(\ini_get('session.cookie_httponly'), \FILTER_VALIDATE_BOOLEAN))) {
            $this->_isHttpOnly = \filter_var(\ini_get('session.cookie_httponly'), \FILTER_VALIDATE_BOOLEAN);
        }
        $this->setSameSite(\strtolower((string) \ini_get('session.cookie_samesite')));
    }

    /**
     * Returns bool(true) if the cookie has a value at the given offset.
     *
     * Returns bool(false) otherwise.
     *
     * @param   scalar  $offset  some array index
     * @return  bool
     */
    public function offsetExists($offset)
    {
        assert(is_scalar($offset), 'Invalid argument $offset: scalar expected');
        return isset($_COOKIE[$offset]);
    }

    /**
     * Returns the cookie-value at the given offset.
     *
     * Returns NULL if no value exists to that offset.
     *
     * @param   scalar  $offset  some array index
     * @return  mixed
     */
    public function offsetGet($offset)
    {
        assert(is_scalar($offset), 'Invalid argument $offset: scalar expected');
        $value = null;
        if ($this->offsetExists($offset)) {
            $value = $_COOKIE[$offset];
        }
        return $value;
    }

    /**
     * Replaces the cookie-var at the given offset and returns it.
     *
     * @param   scalar  $offset  some array index
     * @param   mixed   $value   new session-var value
     * @return  mixed
     */
    public function offsetSet($offset, $value)
    {
        if (!\is_null($offset)) {
            assert(is_scalar($offset), 'Invalid argument $offset: scalar expected');
            $_COOKIE[$offset] = $value;
            $this->_setCookie($offset, $value);
        } else {
            $_COOKIE[] = $value;
            $this->_setCookie(key($_COOKIE), $value);
        }
        return $value;
    }

    /**
     * Calls session_set_cookie_params() with PHP-version dependent parameter list.
     */
    private function _setCookieParameters()
    {
        // @codeCoverageIgnoreStart
        if (PHP_VERSION_ID < 70300) { // PHP prior to 7.3 does not support the "samesite" setting.
            \session_set_cookie_params(
                $this->getLifetime(),
                $this->getPath(),
                $this->getDomain(),
                $this->isSecure(),
                $this->isHttpOnly()
            );
        } elseif (\session_status() === \PHP_SESSION_NONE) {
            // PHP 7.3 and up support "samesite", but only if params are given as an array.
            \session_set_cookie_params(
                array(
                    'lifetime' => $this->getLifetime(),
                    'path' => $this->getPath(),
                    'domain' => $this->getDomain(),
                    'secure' => $this->isSecure(),
                    'httponly' => $this->isHttpOnly(),
                    'samesite' => $this->getSameSite()
                )
            );
        }
        // @codeCoverageIgnoreEnd        
    }

    /**
     * Send a cookie to the browser.
     *
     * @param  scalar        $key       index in $_COOKIE array
     * @param  scalar|array  $value     value to assign, NULL for delete
     * @param  bool          $isDelete  wether to assign or delete the entry
     */
    private function _setCookie($key, $value, bool $isDelete = false)
    {
        if (\is_array($value)) {
            foreach ($value as $i => $v)
            {
                $this->_setCookie("{$key}[{$i}]", $v, $isDelete);
            }
        } elseif (!\headers_sent()) {
            // @codeCoverageIgnoreStart
            $expires = ($isDelete ? time() -42000 :
                    ($this->getLifetime() > 0 ? time() + $this->getLifetime() : 0)
                );
            // The rest of the cookie parameters are applied automatically
            if (PHP_VERSION_ID < 70300) {
                \setcookie((string) $key, (string) $value, $expires,
                    $this->getPath() . // prior to PHP 7.3 same site policy could be added via a hack
                        ($this->getSameSite() ? '; samesite=' . $this->getSameSite() : ""),
                    $this->getDomain(),
                    $this->isSecure(),
                    $this->isHttpOnly()
                );
            } else {
                $options = array(
                    'expires' => $expires,
                    'path' => $this->getPath(),
                    'domain' => $this->getDomain(),
                    'secure' => $this->isSecure(),
                    'httponly' => $this->isHttpOnly(),
                    'samesite' => $this->getSameSite()
                );
                \setcookie((string) $key, (string) $value, $options);
            }
        }
    }

    /**
     * Unset an item in the cookie-array.
     *
     * @param  scalar  $offset  some array index
     */
    public function offsetUnset($offset)
    {
        assert(is_scalar($offset), 'Invalid argument $offset: scalar expected');

        $this->_setCookie($offset, $this->offsetGet($offset), true);
        unset($_COOKIE[$offset]);
    }

    /**
     * Returns the number of items in the cookie-array.
     *
     * @return  int
     */
    public function count(): int
    {
        return count($_COOKIE);
    }

    /**
     * Set cookie lifetime.
     *
     * Set to int(0) to create a cookie that is destroyed by the end of the session.
     *
     * @param   int  $lifetime  Lifetime of the cookie, defined in seconds. 0 = session cookie
     * @return  $this
     */
    public function setLifetime(int $lifetime)
    {
        $this->_lifetime = $lifetime;
        $this->_setCookieParameters();
        return $this;
    }

    /**
     * Get cookie lifetime.
     *
     * Lifetime of the cookie, defined in seconds. 0 = session cookie.
     *
     * @return  int
     */
    public function getLifetime(): int
    {
        return $this->_lifetime;
    }

    /**
     * Returns the server path the cookie is limited to.
     *
     * Path of the application it corresponds to, default is "/".
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->_path;
    }

    /**
     * Get the domain the cookie is limited to.
     *
     * Domain hat the cookie is valid for.
     * Default is "" (if this isn't your domain, something is seriously wrong).
     *
     * @return string
     */
    public function getDomain(): string
    {
        return $this->_domain;
    }

    /**
     * Returns whether to hide cookie from scripts.
     *
     * If TRUE, the cookie will not be sent using JavaScript, default is FALSE.
     * (if this is TRUE, you have not been contacted using a script, or you wouldn't have gotten the cookie).
     *
     * @return bool
     */
    public function isHttpOnly(): bool
    {
        return $this->_isHttpOnly;
    }

    /**
     * Returns whether to send cookie over secure connections only.
     *
     * If TRUE, then the cookie contents are only sent via SSL, default is FALSE.
     * (if this is TRUE then the request was made using SSL or you wouldn't be seeing the cookie).
     * 
     * @return bool
     */
    public function isSecure(): bool
    {
        return $this->_isSecure;
    }

    /**
     * Returns same site policy.
     *
     * @return string
     */
    public function getSameSite(): string
    {
        return $this->_sameSite;
    }

    /**
     * Limit the cookie to a path on the domain.
     *
     * @param   string  $path  must be a valid server path
     * @return  $this
     */
    public function setPath(string $path)
    {
        $this->_path = $path;
        $this->_setCookieParameters();
        return $this;
    }

    /**
     * Limit the cookie to a domain name.
     *
     * @param   string  $domain  must start with "." if sub-domain are to be included
     * @return  $this
     */
    public function setDomain(string $domain)
    {
        $this->_domain = $domain;
        $this->_setCookieParameters();
        return $this;
    }

    /**
     * Set wether the cookie should be hidden from scripts.
     *
     * @param   bool  $isHttpOnly  set to TRUE to prevent JavaScript from seeing the cookie
     * @return  $this
     */
    public function setIsHttpOnly(bool $isHttpOnly)
    {
        $this->_isHttpOnly = $isHttpOnly;
        $this->_setCookieParameters();
        return $this;
    }

    /**
     * Set wether the cookie should be shared only via secure connections.
     *
     * @param   bool  $isSecure  set to TRUE for cookies to be valid via HTTPS only
     * @return  $this
     */
    public function setIsSecure(bool $isSecure)
    {
        $this->_isSecure = $isSecure;
        $this->_setCookieParameters();
        return $this;
    }

    /**
     * Set same-site cookie policy.
     * 
     * @param   string  $sameSite  can be "none", "lax" or "strict"
     * @return  $this
     */
    public function setSameSite(string $sameSite)
    {
        switch ($sameSite)
        {
            case self::SAMESITE_NONE:
            case self::SAMESITE_LAX:
            case self::SAMESITE_STRICT:
                $this->_sameSite = $sameSite;
        }
        $this->_setCookieParameters();
        return $this;
    }

}

?>