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
 * @package  test
 */
class DropTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Ddl\Logs\Drop
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Db\Ddl\Logs\Drop("Test");
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        \Yana\Db\Ddl\Logs\Drop::dropHandler();
    }

    /**
     * @test
     */
    public function testGetType()
    {
        $this->assertEquals('drop', $this->object->getType());
    }

    /**
     * @test
     */
    public function testCommitUpdate()
    {
        \Yana\Db\Ddl\Logs\Drop::dropHandler();
        $this->assertFalse($this->object->commitUpdate());
    }

    /**
     * @test
     */
    public function testCommitUpdateReturnFalse()
    {
        $f = function() { return false; };
        $this->assertNull(\Yana\Db\Ddl\Logs\Drop::setHandler($f));
        $this->assertFalse($this->object->commitUpdate());
    }

    /**
     * @test
     */
    public function testCommitUpdateReturnTrue()
    {
        $f = function() { return true; };
        $this->assertNull(\Yana\Db\Ddl\Logs\Drop::setHandler($f));
        $this->assertTrue($this->object->commitUpdate());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testUnserializeFromXDDLInvalidArgumentException()
    {
        $xddl = '
        <drop version="1.2" ignoreError="no" subject="view">
            <description>test</description>
        </drop>';
        $node = \simplexml_load_string($xddl);
        \Yana\Db\Ddl\Logs\Drop::unserializeFromXDDL($node);
    }

    /**
     * @test
     */
    public function testUnserializeFromXDDL()
    {
        $xddl = '
        <drop version="1.2" ignoreError="no" subject="view" name="test_drop">
            <description>test</description>
        </drop>';
        $node = \simplexml_load_string($xddl);
        $this->object = \Yana\Db\Ddl\Logs\Drop::unserializeFromXDDL($node);
        $this->assertSame("1.2", $this->object->getVersion());
        $this->assertFalse($this->object->ignoreError());
        $this->assertSame("test_drop", $this->object->getName());
        $this->assertSame("view", $this->object->getSubject());
        $this->assertSame("test", $this->object->getDescription());
    }

}
