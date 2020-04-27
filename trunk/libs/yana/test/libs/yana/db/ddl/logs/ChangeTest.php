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
class MyChange extends \Yana\Db\Ddl\Logs\Change
{
    public static function getHandlers(): array
    {
        return static::$handlers;
    }
}


/**
 * @package  test
 */
class ChangeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Ddl\Logs\Change
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Db\Ddl\Logs\Change();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        \Yana\Db\Ddl\Logs\MyChange::dropHandlers();
    }

    /**
     * @test
     */
    public function testGetDBMS()
    {
        $this->assertNull($this->object->getDbms());
    }

    /**
     * @test
     */
    public function testSetDBMS()
    {
        $this->assertSame(\strtolower(__FUNCTION__), $this->object->setDBMS(__FUNCTION__)->getDBMS());
        $this->assertNull($this->object->setDBMS("")->getDBMS());
    }

    /**
     * @test
     */
    public function testGetType()
    {
        $this->assertNull($this->object->getType());
    }

    /**
     * @test
     */
    public function testSetType()
    {
        $this->assertSame(__FUNCTION__, $this->object->setType(__FUNCTION__)->getType());
        $this->assertNull($this->object->setType("")->getType());
    }

    /**
     * @test
     */
    public function testGetParameters()
    {
        $this->assertSame(array(), $this->object->getParameters());
    }

    /**
     * @test
     */
    public function testAddParameter()
    {
        $this->assertSame($this->object, $this->object->addParameter("Value1"));
        $this->assertSame($this->object, $this->object->addParameter("Value2", "Name2"));
        $this->assertSame(array(0 => "Value1", "Name2" => "Value2"), $this->object->getParameters());
    }

    /**
     * @test
     */
    public function testDropParameters()
    {
        $this->assertSame($this->object, $this->object->addParameter("Value1"));
        $this->assertSame($this->object, $this->object->addParameter("Value2", "Name2"));
        $this->assertNull($this->object->dropParameters());
        $this->assertSame(array(), $this->object->getParameters());
    }

    /**
     * @test
     */
    public function testDropHandler()
    {
        $f = function() { return true; };
        \Yana\Db\Ddl\Logs\Change::setHandler($f);
        $this->assertNull(\Yana\Db\Ddl\Logs\Change::dropHandler());
        $this->assertSame(array(), \Yana\Db\Ddl\Logs\MyChange::getHandlers());
        $this->assertNull(\Yana\Db\Ddl\Logs\Change::setHandler($f, __FUNCTION__));
        $this->assertNull(\Yana\Db\Ddl\Logs\Change::dropHandler(__FUNCTION__));
        $this->assertSame(array(), \Yana\Db\Ddl\Logs\MyChange::getHandlers());
    }

    /**
     * @test
     */
    public function testSetHandler()
    {
        $f = function() { return true; };
        $this->assertNull(\Yana\Db\Ddl\Logs\Change::setHandler($f));
        $this->assertNull(\Yana\Db\Ddl\Logs\Change::setHandler($f, __FUNCTION__));
        $this->assertSame(array('default' => $f, __FUNCTION__ => $f), \Yana\Db\Ddl\Logs\MyChange::getHandlers());
    }

    /**
     * @test
     */
    public function testDropHandlers()
    {
        $f = function() { return true; };
        \Yana\Db\Ddl\Logs\Change::setHandler($f);
        \Yana\Db\Ddl\Logs\Change::setHandler($f, __FUNCTION__);
        $this->assertNull(\Yana\Db\Ddl\Logs\Change::dropHandlers());
        $this->assertSame(array(), \Yana\Db\Ddl\Logs\MyChange::getHandlers());
    }

    /**
     * @test
     */
    public function testCommitUpdate()
    {
        $this->assertFalse($this->object->commitUpdate());
        $f = function() { return true; };
        $this->assertNull(\Yana\Db\Ddl\Logs\Change::setHandler($f));
        $this->assertTrue($this->object->commitUpdate());
    }

    /**
     * @test
     */
    public function testUnserializeFromXDDL()
    {
        $xddl = '
        <change version="1.2" ignoreError="no" dbms="generic" type="default">
            <description>test</description>
            <logparam>1</logparam>
            <logparam name="test">2</logparam>
        </change>';
        $node = \simplexml_load_string($xddl);
        $this->object = \Yana\Db\Ddl\Logs\Change::unserializeFromXDDL($node);
        $this->assertSame("1.2", $this->object->getVersion());
        $this->assertFalse($this->object->ignoreError());
        $this->assertSame("generic", $this->object->getDBMS());
        $this->assertSame("default", $this->object->getType());
        $this->assertSame("test", $this->object->getDescription());
        $this->assertSame(array(0 => "1", "test" => "2"), $this->object->getParameters());
    }

}
