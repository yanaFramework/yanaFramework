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

namespace Yana\Db\Ddl;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * DDL test-case
 *
 * @package  test
 */
class GrantTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Grant
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Grant;
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
    public function testGetRole()
    {
        $this->assertNull($this->object->getRole());
    }

    /**
     * @test
     */
    public function testSetRole()
    {
        $this->assertEquals('test', $this->object->setRole('test')->getRole());
        $this->assertNull($this->object->setRole('')->getRole());
    }

    /**
     * @test
     */
    public function testGetUser()
    {
        $this->assertNull($this->object->getUser());
    }

    /**
     * @test
     */
    public function testSetUser()
    {
        $this->assertEquals('test', $this->object->setUser('test')->getUser());
        $this->assertNull($this->object->setUser('')->getUser());
    }

    /**
     * @test
     */
    public function testGetLevel()
    {
        $this->assertNull($this->object->getLevel());
    }

    /**
     * @test
     */
    public function testSetLevel()
    {
        $this->assertEquals(0, $this->object->setLevel(0)->getLevel());
        $this->assertNull($this->object->setLevel(null)->getLevel());
        $this->assertEquals(100, $this->object->setLevel(100)->getLevel());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testSetLevelInvalidArgumentExceptionLowValue()
    {
        $this->object->setLevel(-1)->getLevel();
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testSetLevelInvalidArgumentExceptionHighValue()
    {
        $this->object->setLevel(101)->getLevel();
    }

    /**
     * @test
     */
    public function testIsSelectable()
    {
        $this->assertTrue($this->object->isSelectable());
    }

    /**
     * @test
     */
    public function testSetSelect()
    {
        $this->assertFalse($this->object->setSelect(false)->isSelectable());
        $this->assertTrue($this->object->setSelect(true)->isSelectable());
    }

    /**
     * @test
     */
    public function testIsInsertable()
    {
        $this->assertTrue($this->object->isInsertable());
    }

    /**
     * @test
     */
    public function testSetInsert()
    {
        $this->assertFalse($this->object->setInsert(false)->isInsertable());
        $this->assertTrue($this->object->setInsert(true)->isInsertable());
    }

    /**
     * @test
     */
    public function testIsUpdatable()
    {
        $this->assertTrue($this->object->isUpdatable());
    }

    /**
     * @test
     */
    public function testSetUpdate()
    {
        $this->assertFalse($this->object->setUpdate(false)->isUpdatable());
        $this->assertTrue($this->object->setUpdate(true)->isUpdatable());
    }

    /**
     * @test
     */
    public function testIsDeletable()
    {
        $this->assertTrue($this->object->isDeletable());
    }

    /**
     * @test
     */
    public function testSetDelete()
    {
        $this->assertFalse($this->object->setDelete(false)->isDeletable());
        $this->assertTrue($this->object->setDelete(true)->isDeletable());
    }

    /**
     * @test
     */
    public function testIsGrantable()
    {
        $this->assertTrue($this->object->isGrantable());
    }

    /**
     * @test
     */
    public function testSetGrantOption()
    {
        $this->assertFalse($this->object->setGrantOption(false)->isGrantable());
        $this->assertTrue($this->object->setGrantOption(true)->isGrantable());
    }

    /**
     * @test
     */
    public function testCheckPermission()
    {
        $this->object->setDelete(false);
        $this->object->setGrantOption(false);
        $this->assertTrue($this->object->checkPermission());
        $this->assertTrue($this->object->checkPermission(true, true, true));
        $this->assertFalse($this->object->checkPermission(true, true, true, true));
    }

    /**
     * @test
     */
    public function testCheckPermissions()
    {
        $this->object->setDelete(false);
        $this->object->setGrantOption(false);
        $grants = array($this->object);
        $this->assertTrue($this->object->checkPermissions($grants));
        $this->assertTrue($this->object->checkPermissions($grants, true, true, true));
        $this->assertFalse($this->object->checkPermissions($grants, true, true, true, true));
    }

}

?>
