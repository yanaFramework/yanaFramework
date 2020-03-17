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

namespace Yana\Security\Data\SecurityLevels;


/**
 * @ignore
 */
require_once __DIR__ . '/../../../../../include.php';

/**
 * Test-case
 *
 * @package  test
 */
class LevelTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Security\Data\SecurityLevels\Level
     */
    protected $object1;

    /**
     * @var \Yana\Security\Data\SecurityLevels\Level
     */
    protected $object2;

    /**
     * @var \Yana\Security\Data\SecurityLevels\Level
     */
    protected $object3;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object1 = new \Yana\Security\Data\SecurityLevels\Level(0, true);
        $this->object2 = new \Yana\Security\Data\SecurityLevels\Level(10, false);
        $this->object3 = new \Yana\Security\Data\SecurityLevels\Level(100, false);
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
        $this->assertSame(-1, $this->object1->getId());
        $this->assertSame(-1, $this->object2->getId());
        $this->assertSame(-1, $this->object3->getId());
    }

    /**
     * @test
     */
    public function testSetId()
    {
        $this->assertSame(0, $this->object1->setId(0)->getId());
        $this->assertSame(1, $this->object2->setId(1)->getId());
        $this->assertSame(2, $this->object3->setId(2)->getId());
    }

    /**
     * @test
     */
    public function testGetSecurityLevel()
    {
        $this->assertSame(0, $this->object1->getSecurityLevel());
        $this->assertSame(10, $this->object2->getSecurityLevel());
        $this->assertSame(100, $this->object3->getSecurityLevel());
    }

    /**
     * @test
     */
    public function testIsUserProxyActive()
    {
        $this->assertSame(true, $this->object1->isUserProxyActive());
        $this->assertSame(false, $this->object2->isUserProxyActive());
        $this->assertSame(false, $this->object3->isUserProxyActive());
    }

    /**
     * @test
     */
    public function testGetGrantedByUser()
    {
        $this->assertSame("", $this->object1->getGrantedByUser());
    }

    /**
     * @test
     */
    public function testSetGrantedByUser()
    {
        $this->assertSame("b", $this->object1->setGrantedByUser("a")->setGrantedByUser("b")->getGrantedByUser());
        $this->assertSame("User@Näme!", $this->object2->setGrantedByUser("User@Näme!")->getGrantedByUser());
        $this->assertSame("", $this->object3->setGrantedByUser("")->getGrantedByUser());
    }

    /**
     * @test
     */
    public function testGetUserName()
    {
        $this->assertSame("", $this->object1->getUserName());
    }

    /**
     * @test
     */
    public function testSetUserName()
    {
        $this->assertSame("b", $this->object1->setUserName("a")->setUserName("b")->getUserName());
        $this->assertSame("User@Näme!", $this->object2->setUserName("User@Näme!")->getUserName());
        $this->assertSame("", $this->object3->setUserName("")->getUserName());
    }

    /**
     * @test
     */
    public function testGetProfile()
    {
        $this->assertSame('', $this->object1->getProfile());
        $this->assertSame('', $this->object2->getProfile());
        $this->assertSame('', $this->object3->getProfile());
    }

    /**
     * @test
     */
    public function testSetProfile()
    {
        $this->assertSame('Profile0', $this->object1->setProfile('Profile0')->getProfile());
        $this->assertSame('Profile1', $this->object2->setProfile('Profile1')->getProfile());
        $this->assertSame('', $this->object3->setProfile('')->getProfile());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\User\NotGrantableException
     */
    public function testGrantToNotGrantableException()
    {
        $this->object3->grantTo('user');
    }

    /**
     * @test
     */
    public function testGrantTo()
    {
        $permission = $this->object1->grantTo('user');
        $this->assertSame('user', $permission->getUserName());
        $this->assertSame($this->object1->getUserName(), $permission->getGrantedByUser());
        $this->assertSame($this->object1->getSecurityLevel(), $permission->getSecurityLevel());
        $this->assertSame($this->object1->getProfile(), $permission->getProfile());
    }

}
