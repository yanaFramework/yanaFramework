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

namespace Yana\Db\Ddl\Logs;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../../include.php';

/**
 * @ignore
 * @package  test
 */
class MyCreate extends \Yana\Db\Ddl\Logs\Create
{
    public static function getHandlers(): array
    {
        return static::$handler;
    }
    public static function dropHandlers()
    {
        static::$handlers = array();
    }
}

/**
 * @package  test
 */
class CreateTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Ddl\Logs\Create
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Db\Ddl\Logs\Create("Test");
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        \Yana\Db\Ddl\Logs\Create::dropHandler();
    }

    /**
     * @test
     */
    public function testGetType()
    {
        $this->assertEquals('create', $this->object->getType());
    }

    /**
     * @test
     */
    public function testGetSubject()
    {
        $this->assertNull($this->object->getSubject());
    }

    /**
     * @test
     */
    public function testSetSubject()
    {
        $this->assertSame("table", $this->object->setSubject("table")->getSubject());
        $this->assertNull($this->object->setSubject()->getSubject());
    }

    /**
     * @test
     */
    public function testGetName()
    {
        $this->assertSame("test", $this->object->getName());
    }

    /**
     * @test
     */
    public function testSetName()
    {
        $this->assertSame(\strtolower(__FUNCTION__), $this->object->setName(__FUNCTION__)->getName());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testSetNameInvalidArgumentException()
    {
        $this->object->setName("");
    }

    /**
     * @test
     */
    public function testCommitUpdate()
    {
        \Yana\Db\Ddl\Logs\Create::dropHandler();
        $this->assertFalse($this->object->commitUpdate());
    }

    /**
     * @test
     */
    public function testCommitUpdateReturnFalse()
    {
        $f = function() { return false; };
        $this->assertNull(\Yana\Db\Ddl\Logs\Create::setHandler($f));
        $this->assertFalse($this->object->commitUpdate());
    }

    /**
     * @test
     */
    public function testCommitUpdateReturnTrue()
    {
        $f = function() { return true; };
        $this->assertNull(\Yana\Db\Ddl\Logs\Create::setHandler($f));
        $this->assertTrue($this->object->commitUpdate());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testUnserializeFromXDDLInvalidArgumentException()
    {
        $xddl = '
        <create version="1.2" ignoreError="no" subject="trigger">
            <description>test</description>
        </create>';
        $node = \simplexml_load_string($xddl);
        \Yana\Db\Ddl\Logs\Create::unserializeFromXDDL($node);
    }

    /**
     * @test
     */
    public function testUnserializeFromXDDL()
    {
        $xddl = '
        <create version="1.2" ignoreError="no" name="test_create" subject="trigger">
            <description>test</description>
        </create>';
        $node = \simplexml_load_string($xddl);
        $this->object = \Yana\Db\Ddl\Logs\Create::unserializeFromXDDL($node);
        $this->assertSame("1.2", $this->object->getVersion());
        $this->assertFalse($this->object->ignoreError());
        $this->assertSame("test_create", $this->object->getName());
        $this->assertSame("trigger", $this->object->getSubject());
        $this->assertSame("test", $this->object->getDescription());
    }

}
