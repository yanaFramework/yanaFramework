<?php
/**
 * PHPUnit test-case
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
declare(strict_types=1);

namespace Yana\Security\Data\Users;


/**
 * @ignore
 */
require_once __DIR__ . '/../../../../../include.php';

/**
 * Test-case
 *
 * @package  test
 */
class EntityTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Security\Data\Users\Entity
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Security\Data\Users\Entity('Testä!');
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
    public function testGetId()
    {
        $this->assertEquals('Testä!', $this->object->getId());
    }

    /**
     * @test
     */
    public function testSetId()
    {
        $this->assertEquals('Áòß!', $this->object->setId('Áòß!')->getId());
    }

    /**
     * @test
     */
    public function testSetFailureCount()
    {
        $this->assertEquals(3, $this->object->setFailureCount(3)->getFailureCount());
    }

    /**
     * @test
     */
    public function testSetFailureTime()
    {
        $this->assertEquals(50, $this->object->setFailureTime(50)->getFailureTime());
        $this->assertEquals(0, $this->object->setFailureTime(0)->getFailureTime());
        $this->assertEquals(-5, $this->object->setFailureTime(-5)->getFailureTime());
    }

    /**
     * @test
     */
    public function testSetLoginCount()
    {
        $this->assertEquals(3, $this->object->setLoginCount(3)->getLoginCount());
    }

    /**
     * @test
     */
    public function testSetLoginTime()
    {
        $this->assertEquals(50, $this->object->setLoginTime(50)->getLoginTime());
        $this->assertEquals(0, $this->object->setLoginTime(0)->getLoginTime());
        $this->assertEquals(-5, $this->object->setLoginTime(-5)->getLoginTime());
    }

    /**
     * @test
     */
    public function testSetPasswordRecoveryId()
    {
        $this->assertEquals('Áòß!', $this->object->setPasswordRecoveryId('Áòß!')->getPasswordRecoveryId());
    }

    /**
     * @test
     */
    public function testSetPasswordRecoveryTime()
    {
        $this->assertEquals(50, $this->object->setPasswordRecoveryTime(50)->getPasswordRecoveryTime());
        $this->assertEquals(0, $this->object->setPasswordRecoveryTime(0)->getPasswordRecoveryTime());
        $this->assertEquals(-5, $this->object->setPasswordRecoveryTime(-5)->getPasswordRecoveryTime());
    }

    /**
     * @test
     */
    public function testSetPasswordChangedTime()
    {
        $this->assertEquals(50, $this->object->setPasswordChangedTime(50)->getPasswordChangedTime());
        $this->assertEquals(0, $this->object->setPasswordChangedTime(0)->getPasswordChangedTime());
        $this->assertEquals(-5, $this->object->setPasswordChangedTime(-5)->getPasswordChangedTime());
    }

    /**
     * @test
     */
    public function testSetRecentPasswords()
    {
        $this->assertEquals(array('Áòß!', 'Áòß!2'), $this->object->setRecentPasswords(array('Áòß!', 'Áòß!2'))->getRecentPasswords());
        $this->assertEquals(array(), $this->object->setRecentPasswords(array())->getRecentPasswords());
    }

    /**
     * @test
     */
    public function testSetTimeCreated()
    {
        $this->assertEquals(50, $this->object->setTimeCreated(50)->getTimeCreated());
        $this->assertEquals(0, $this->object->setTimeCreated(0)->getTimeCreated());
        $this->assertEquals(-5, $this->object->setTimeCreated(-5)->getTimeCreated());
    }

    /**
     * @test
     */
    public function testSetSessionCheckSum()
    {
        $this->assertEquals('Áòß!', $this->object->setSessionCheckSum('Áòß!')->getSessionCheckSum());
        $this->assertEquals("", $this->object->setSessionCheckSum("")->getSessionCheckSum());
    }

    /**
     * @test
     */
    public function testGetSessionCheckSum()
    {
        $this->assertNull($this->object->getSessionCheckSum());
    }

    /**
     * @test
     */
    public function testGetPassword()
    {
        $this->assertNull($this->object->getPassword());
    }

    /**
     * @test
     */
    public function testSetPassword()
    {
        $this->assertEquals('Áòß!', $this->object->setPassword('Áòß!')->getPassword());
    }

    /**
     * @test
     */
    public function testSetLanguage()
    {
        $this->assertEquals('Áòß!', $this->object->setLanguage('Áòß!')->getLanguage());
    }

    /**
     * @test
     */
    public function testGetLanguage()
    {
        $this->assertNull($this->object->getLanguage());
    }

    /**
     * @test
     */
    public function testGetFailureCount()
    {
        $this->assertSame(0, $this->object->getFailureCount());
    }

    /**
     * @test
     */
    public function testGetFailureTime()
    {
        $this->assertSame(0, $this->object->getFailureTime());
    }

    /**
     * @test
     */
    public function testGetLoginCount()
    {
        $this->assertSame(0, $this->object->getLoginCount());
    }

    /**
     * @test
     */
    public function testGetLoginTime()
    {
        $this->assertNull($this->object->getLoginTime());
    }

    /**
     * @test
     */
    public function testSetMail()
    {
        $this->assertEquals('Áòß!', $this->object->setMail('Áòß!')->getMail());
    }

    /**
     * @test
     */
    public function testGetMail()
    {
        $this->assertNull($this->object->getMail());
    }

    /**
     * @test
     */
    public function testSetExpert()
    {
        $this->assertTrue($this->object->setExpert(true)->isExpert());
        $this->assertFalse($this->object->setExpert(false)->isExpert());
        $this->assertTrue($this->object->setExpert(true)->isExpert());
    }

    /**
     * @test
     */
    public function testIsExpert()
    {
        $this->assertFalse($this->object->isExpert());
    }

    /**
     * @test
     */
    public function testSetActive()
    {
        $this->assertTrue($this->object->setActive(true)->isActive());
        $this->assertFalse($this->object->setActive(false)->isActive());
        $this->assertTrue($this->object->setActive(true)->isActive());
    }

    /**
     * @test
     */
    public function testIsActive()
    {
        $this->assertFalse($this->object->isActive());
    }

    /**
     * @test
     */
    public function testGetTimeCreated()
    {
        $this->assertNull($this->object->getTimeCreated());
    }

    /**
     * @test
     */
    public function testGetPasswordChangedTime()
    {
        $this->assertSame(0, $this->object->getPasswordChangedTime());
    }

    /**
     * @test
     */
    public function testGetRecentPasswords()
    {
        $this->assertEquals(array(), $this->object->getRecentPasswords());
    }

    /**
     * @test
     */
    public function testGetPasswordRecoveryId()
    {
        $this->assertNull($this->object->getPasswordRecoveryId());
    }

    /**
     * @test
     */
    public function testGetPasswordRecoveryTime()
    {
        $this->assertSame(0, $this->object->getPasswordRecoveryTime());
    }

}
