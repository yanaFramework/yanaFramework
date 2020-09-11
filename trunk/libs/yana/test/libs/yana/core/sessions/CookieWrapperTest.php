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
 * @package  test
 * @license  http://www.gnu.org/licenses/gpl.txt
 */

namespace Yana\Core\Sessions;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 * @ignore
 */
class MyCookieWrapper extends \Yana\Core\Sessions\CookieWrapper
{
    public function __construct()
    {
        if (!isset($_COOKIE)) {
            $_COOKIE = array();
        }
    }
}

/**
 * @package  test
 */
class CookieWrapperTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Core\Sessions\CookieWrapper
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Core\Sessions\MyCookieWrapper();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }

    /**
     * @test
     */
    public function testOffsetExists()
    {
        $this->assertFalse($this->object->offsetExists(__FUNCTION__));
        $this->assertSame('123', $this->object->offsetSet(__FUNCTION__, '123'));
        $this->assertTrue($this->object->offsetExists(__FUNCTION__));
    }

    /**
     * @test
     */
    public function testOffsetGet()
    {
        $this->assertNull($this->object->offsetGet(__FUNCTION__));
        $this->assertSame('123', $this->object->offsetSet(__FUNCTION__, '123'));
        $this->assertSame('123', $this->object->offsetGet(__FUNCTION__));
    }

    /**
     * @test
     */
    public function testOffsetSet()
    {
        $this->assertSame('123', $this->object->offsetSet(__FUNCTION__, '123'));
    }

    /**
     * @test
     */
    public function testOffsetSetNull()
    {
        $this->assertSame(__FUNCTION__, $this->object->offsetSet(null, __FUNCTION__));
        $this->assertSame(__FUNCTION__, $this->object->offsetGet(key($_COOKIE)));
    }

    /**
     * @test
     */
    public function testOffsetSetArray()
    {
        $values = array('a' => 1, 'B' => 2, 'c' => 3);
        $this->assertSame($values, $this->object->offsetSet(__FUNCTION__, $values));
        $this->assertSame($values, $this->object->offsetGet(__FUNCTION__));
    }

    /**
     * @test
     */
    public function testOffsetUnset()
    {
        $this->assertFalse($this->object->offsetExists(__FUNCTION__));
        $this->assertSame('123', $this->object->offsetSet(__FUNCTION__, '123'));
        $this->assertTrue($this->object->offsetExists(__FUNCTION__));
        $this->assertNull($this->object->offsetUnset(__FUNCTION__, '123'));
        $this->assertFalse($this->object->offsetExists(__FUNCTION__));
    }

    /**
     * @test
     */
    public function testCount()
    {
        $this->assertSame(0, $this->object->count());
    }

    /**
     * @test
     */
    public function testGetLifetime()
    {
        $this->assertSame(0, $this->object->getLifetime());
    }

    /**
     * @test
     */
    public function testSetLifetime()
    {
        $this->assertSame(0, $this->object->getLifetime());
        $this->assertSame(12345, $this->object->setLifetime(12345)->getLifetime());
        $params = \session_get_cookie_params();
        $this->assertSame(12345, $params['lifetime']);
        $this->assertSame(0, $this->object->setLifetime(0)->getLifetime());
    }

    /**
     * @test
     */
    public function testGetPath()
    {
        $this->assertSame('/', $this->object->getPath());
    }

    /**
     * @test
     */
    public function testGetDomain()
    {
        $this->assertSame('', $this->object->getDomain());
    }

    /**
     * @test
     */
    public function testIsHttpOnly()
    {
        $this->assertFalse($this->object->isHttpOnly());
    }

    /**
     * @test
     */
    public function testIsSecure()
    {
        $this->assertFalse($this->object->isSecure());
    }

    /**
     * @test
     */
    public function testGetSameSite()
    {
        $this->assertSame('lax', $this->object->getSameSite());
    }

    /**
     * @test
     */
    public function testSetPath()
    {
        $this->assertSame(__FUNCTION__, $this->object->setPath(__FUNCTION__)->getPath());
        $params = \session_get_cookie_params();
        $this->assertSame(__FUNCTION__, $params['path']);
        $this->assertSame('', $this->object->setPath('')->getPath());
        $this->assertSame('/', $this->object->setPath('/')->getPath());
    }

    /**
     * @test
     */
    public function testSetDomain()
    {
        $this->assertSame(__FUNCTION__, $this->object->setDomain(__FUNCTION__)->getDomain());
        $params = \session_get_cookie_params();
        $this->assertSame(__FUNCTION__, $params['domain']);
        $this->assertSame('', $this->object->setDomain('')->getDomain());
    }

    /**
     * @test
     */
    public function testSetIsHttpOnly()
    {
        $this->assertTrue($this->object->setIsHttpOnly(true)->isHttpOnly());
        $params = \session_get_cookie_params();
        $this->assertSame(true, $params['httponly']);
        $this->assertFalse($this->object->setIsHttpOnly(false)->isHttpOnly());
    }

    /**
     * @test
     */
    public function testSetIsSecure()
    {
        $this->assertTrue($this->object->setIsSecure(true)->isSecure());
        $params = \session_get_cookie_params();
        $this->assertSame(true, $params['secure']);
        $this->assertFalse($this->object->setIsSecure(false)->isSecure());
    }

    /**
     * @test
     */
    public function testSetSameSite()
    {
        $this->assertSame(\Yana\Core\Sessions\CookieWrapper::SAMESITE_NONE,
            $this->object->setSameSite(\Yana\Core\Sessions\CookieWrapper::SAMESITE_NONE)->getSameSite());
        $this->assertSame(\Yana\Core\Sessions\CookieWrapper::SAMESITE_NONE,
            $this->object->setSameSite(__FUNCTION__)->getSameSite());
        $this->assertSame(\Yana\Core\Sessions\CookieWrapper::SAMESITE_STRICT,
            $this->object->setSameSite(\Yana\Core\Sessions\CookieWrapper::SAMESITE_STRICT)->getSameSite());
        $this->assertSame(\Yana\Core\Sessions\CookieWrapper::SAMESITE_STRICT, $this->object->setSameSite('')->getSameSite());
        $this->assertSame(\Yana\Core\Sessions\CookieWrapper::SAMESITE_LAX,
            $this->object->setSameSite(\Yana\Core\Sessions\CookieWrapper::SAMESITE_LAX)->getSameSite());
    }

}
