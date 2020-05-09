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
class SequenceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Ddl\Database
     */
    protected $parent;

    /**
     * @var \Yana\Db\Ddl\Sequence
     */
    protected $object;

    /**
     * sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->parent = new \Yana\Db\Ddl\Database();
        $this->object = new \Yana\Db\Ddl\Sequence('sequence', $this->parent);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        unset($this->object);
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
        $this->assertNull($this->object->setDescription('')->getDescription());
    }

    /**
     * @test
     */
    public function testIsCycle()
    {
       $this->assertFalse($this->object->isCycle());
    }

    /**
     * @test
     */
    public function testSetCycle()
    {
       $this->assertTrue($this->object->setCycle(true)->isCycle());
       $this->assertFalse($this->object->setCycle(false)->isCycle());
       $this->assertTrue($this->object->setCycle(true)->isCycle());
    }

    /**
     * @test
     */
    public function testGetStart()
    {
        $this->assertNull($this->object->getStart());
    }

    /**
     * @test
     */
    public function testStart()
    {
        $this->assertSame(1, $this->object->setStart(1)->getStart());
        $this->assertNull($this->object->setStart(0)->getStart());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testSetStartInvalidArgument()
    {
        $this->object->setMin(2);
        $this->object->setStart(1);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testSetStartInvalidArgument2()
    {
        $this->object->setMax(1);
        $this->object->setStart(2);
    }

    /**
     * @test
     */
    public function testGetIncrement()
    {
        $this->assertEquals(1, $this->object->getIncrement());
    }

    /**
     * @test
     */
    public function testSetIncrement()
    {
        $this->assertEquals(2, $this->object->setIncrement(2)->getIncrement());

    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testSetIncrementInvalidArgumentException()
    {
        $this->object->setIncrement(0);
    }

    /**
     * @test
     */
    public function testGetMin()
    {
        $this->assertNull($this->object->getMin());
    }

    /**
     * @test
     */
    public function testSetMin()
    {
        $this->assertSame(1, $this->object->setMin(1)->getMin());
        $this->assertSame(0, $this->object->setMin(0)->getMin());
        $this->assertNull($this->object->setMin()->getMin());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testSetMinInvalidArgumentException()
    {
        $this->object->setMax(1)->setMin(2);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testSetMinInvalidArgumentException2()
    {
        $this->object->setStart(1)->setMin(2);
    }

    /**
     * @test
     */
    public function testGetMax()
    {
        $this->assertNull($this->object->getMax());
    }

    /**
     * @test
     */
    public function testSetMax()
    {
        $this->assertSame(1, $this->object->setMax(1)->getMax());
        $this->assertNull($this->object->setMax()->getMax());
    }

    /**
     * @test
     */
    public function testSetMax2()
    {
        $this->assertSame(2, $this->object->setStart(2)->setMax(2)->getMax());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testSetMaxInvalidArgumentException()
{
        $this->object->setStart(2)->setMax(1);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testUnserializeFromXDDLInvalidArgumentException()
    {
        $data = "<sequence/>";
        \Yana\Db\Ddl\Sequence::unserializeFromXDDL(new \SimpleXMLElement($data));
    }

    /**
     * @test
     */
    public function testUnserializeFromXDDL()
    {
        $data = '<sequence name="Test"/>';
        $this->object = \Yana\Db\Ddl\Sequence::unserializeFromXDDL(new \SimpleXMLElement($data));
        $this->assertSame('test', $this->object->getName());
        $this->assertSame(1, $this->object->getIncrement());
    }

    /**
     * @test
     */
    public function testUnserializeFromXDDLWithArguments()
    {
        $data = '<sequence name="Test" min="1" start="2" max="3" increment="4" cycle="yes"/>';
        $this->object = \Yana\Db\Ddl\Sequence::unserializeFromXDDL(new \SimpleXMLElement($data));
        $this->assertSame('test', $this->object->getName());
        $this->assertSame(1, $this->object->getMin());
        $this->assertSame(2, $this->object->getStart());
        $this->assertSame(3, $this->object->getMax());
        $this->assertSame(4, $this->object->getIncrement());
        $this->assertTrue($this->object->isCycle());
    }

}

?>