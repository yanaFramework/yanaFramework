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

namespace Yana\Security\Sessions;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * Test-case
 *
 * @package  test
 */
class WrapperTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Security\Sessions\Wrapper
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Security\Sessions\Wrapper();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->object->unsetAll();
    }

    /**
     * @test
     */
    public function testGetCurrentUserName()
    {
        $this->assertEquals("", $this->object->getCurrentUserName());
    }

    /**
     * @test
     */
    public function testSetCurrentUserName()
    {
        $user = new \Yana\Security\Data\Users\Entity("Test äß");
        $this->assertEquals($user->getId(), $this->object->setCurrentUserName($user)->getCurrentUserName());
    }

    /**
     * @test
     */
    public function testGetApplicationUserId()
    {
        $this->assertEquals("", $this->object->getApplicationUserId());
    }

    /**
     * @test
     */
    public function testSetApplicationUserId()
    {
        $this->assertEquals("Test äß", $this->object->setApplicationUserId("Test äß")->getApplicationUserId());
    }

    /**
     * @test
     */
    public function testGetSessionUserId()
    {
        $this->assertEquals("", $this->object->getSessionUserId());
    }

    /**
     * @test
     */
    public function testSetSessionUserId()
    {
        $this->assertEquals("Test äß", $this->object->setSessionUserId("Test äß")->getSessionUserId());
    }

    /**
     * @test
     */
    public function testGetCurrentLanguage()
    {
        $this->assertEquals("", $this->object->getCurrentLanguage());
    }

    /**
     * @test
     */
    public function testSetCurrentLanguage()
    {
        $this->assertEquals("Test äß", $this->object->setCurrentLanguage("Test äß")->getCurrentLanguage());
    }

}
