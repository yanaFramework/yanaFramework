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
class AbstractLogTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Ddl\ChangeLog
     */
    protected $parent;

    /**
     * @var \Yana\Db\Ddl\Logs\AbstractLog
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->parent = new \Yana\Db\Ddl\ChangeLog();
        $this->object = new \Yana\Db\Ddl\Logs\Change($this->parent);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        \Yana\Db\Ddl\Logs\AbstractLog::dropHandler();
    }

    /**
     * @test
     */
    public function testGetParent()
    {
        $this->assertSame($this->parent, $this->object->getParent());
    }

    /**
     * @test
     */
    public function testGetVersion()
    {
        $this->assertNull($this->object->getVersion());
    }

    /**
     * @test
     */
    public function testSetVersion()
    {
        $this->assertSame(__FUNCTION__, $this->object->setVersion(__FUNCTION__)->getVersion());
        $this->assertNull($this->object->setVersion("")->getVersion());
    }

    /**
     * @test
     */
    public function testGetDescription()
    {
        $this->assertNull($this->object->getDescription());
    }

    /**
     * @test
     */
    public function testSetDescription()
    {
        $this->assertSame(__FUNCTION__, $this->object->setDescription(__FUNCTION__)->getDescription());
        $this->assertNull($this->object->setDescription("")->getDescription());
    }

    /**
     * @test
     */
    public function testIgnoreError()
    {
        $this->assertFalse($this->object->ignoreError());
    }

    /**
     * @test
     */
    public function testSetIgnoreError()
    {
        $this->assertSame(true, $this->object->setIgnoreError(true)->ignoreError());
        $this->assertSame(false, $this->object->setIgnoreError(false)->ignoreError());
    }

    /**
     * @test
     */
    public function testSetHandler()
    {
        $this->assertNull(\Yana\Db\Ddl\Logs\AbstractLog::setHandler(__METHOD__));
        $this->assertNull(\Yana\Db\Ddl\Logs\AbstractLog::setHandler(array(__CLASS__, __FUNCTION__)));
        $this->assertNull(\Yana\Db\Ddl\Logs\AbstractLog::setHandler(function() { return true; }));
    }

    /**
     * @test
     */
    public function testDropHandler()
    {
        $this->assertNull(\Yana\Db\Ddl\Logs\AbstractLog::dropHandler());
    }

    /**
     * @test
     */
    public function testGetType()
    {
        $create = new \Yana\Db\Ddl\Logs\Create("test");
        $this->assertSame("create", $create->getType());
    }

}
