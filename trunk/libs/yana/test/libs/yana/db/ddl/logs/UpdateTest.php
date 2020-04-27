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
class UpdateTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Ddl\ChangeLog
     */
    protected $parent;

    /**
     * @var \Yana\Db\Ddl\Logs\Update
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->parent = new \Yana\Db\Ddl\ChangeLog();
        $this->object = new \Yana\Db\Ddl\Logs\Update('Test', $this->parent);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        \Yana\Db\Ddl\Logs\Update::dropHandler();
    }

    /**
     * @test
     */
    public function testGetType()
    {
        $this->assertEquals('update', $this->object->getType());
    }

    /**
     * @test
     */
    public function testGetPropertyName()
    {
        $this->assertNull($this->object->getPropertyName());
    }

    /**
     * @test
     */
    public function testSetPropertyName()
    {
        $this->assertSame(__FUNCTION__, $this->object->setPropertyName(__FUNCTION__)->getPropertyName());
        $this->assertNull($this->object->setPropertyName("")->getPropertyName());
    }

    /**
     * @test
     */
    public function testGetPropertyValue()
    {
        $this->assertNull($this->object->getPropertyValue());
    }

    /**
     * @test
     */
    public function testSetPropertyValue()
    {
        $this->assertSame(__FUNCTION__, $this->object->setPropertyValue(__FUNCTION__)->getPropertyValue());
        $this->assertNull($this->object->setPropertyValue("")->getPropertyValue());
    }

    /**
     * @test
     */
    public function testGetOldPropertyValue()
    {
        $this->assertNull($this->object->getOldPropertyValue());
    }

    /**
     * @test
     */
    public function testSetOldPropertyValue()
    {
        $this->assertSame(__FUNCTION__, $this->object->setOldPropertyValue(__FUNCTION__)->getOldPropertyValue());
        $this->assertNull($this->object->setOldPropertyValue("")->getOldPropertyValue());
    }

    /**
     * @test
     */
    public function testCommitUpdate()
    {
        \Yana\Db\Ddl\Logs\Update::dropHandler();
        $this->assertFalse($this->object->commitUpdate());
    }

    /**
     * @test
     */
    public function testCommitUpdateReturnFalse()
    {
        $f = function() { return false; };
        $this->assertNull(\Yana\Db\Ddl\Logs\Update::setHandler($f));
        $this->assertFalse($this->object->commitUpdate());
    }

    /**
     * @test
     */
    public function testCommitUpdateReturnTrue()
    {
        $f = function() { return true; };
        $this->assertNull(\Yana\Db\Ddl\Logs\Update::setHandler($f));
        $this->assertTrue($this->object->commitUpdate());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testUnserializeFromXDDLInvalidArgumentException()
    {
        $xddl = '
        <update version="1.2" ignoreError="no" subject="view" property="array" value="name" oldvalue="new">
            <description>test</description>
        </update>';
        $node = \simplexml_load_string($xddl);
        \Yana\Db\Ddl\Logs\Update::unserializeFromXDDL($node);
    }

    /**
     * @test
     */
    public function testUnserializeFromXDDL()
    {
        $xddl = '
        <update version="1.2" ignoreError="no" subject="view" name="test_update" property="array" value="name" oldvalue="new">
            <description>test</description>
        </update>';
        $node = \simplexml_load_string($xddl);
        $this->object = \Yana\Db\Ddl\Logs\Update::unserializeFromXDDL($node, $this->parent);
        $this->assertSame("1.2", $this->object->getVersion());
        $this->assertFalse($this->object->ignoreError());
        $this->assertSame("test_update", $this->object->getName());
        $this->assertSame("view", $this->object->getSubject());
        $this->assertSame("array", $this->object->getPropertyName());
        $this->assertSame("name", $this->object->getPropertyValue());
        $this->assertSame("new", $this->object->getOldPropertyValue());
        $this->assertSame("test", $this->object->getDescription());
    }

}
