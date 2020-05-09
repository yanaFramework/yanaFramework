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
     * @var \Yana\Db\Ddl\Sequence
     */
    protected $object;

    /**
     * sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Db\Ddl\Sequence('sequence');
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
    public function testDescription()
    {
        $this->object->setDescription('description');
        $result = $this->object->getDescription();
        $this->assertEquals('description', $result, 'expected value is "description"  - the values should be equal');

        $this->object->setDescription('');
        $result = $this->object->getDescription();
        $this->assertNull($result, 'the description is expected null');
    }

    /**
     * Cycle
     *
     * @test
     */
    public function testCycle()
    {
       // DDL Sequence
       $this->object->setCycle(true);
       $result = $this->object->isCycle();
       $this->assertTrue($result, 'assert failed, \Yana\Db\Ddl\Sequence : expected true - setCycle was set with true');

       $this->object->setCycle(false);
       $result = $this->object->isCycle();
       $this->assertFalse($result, 'assert failed, \Yana\Db\Ddl\Sequence : expected false - setCycle was set with false');
    }

    /**
     * Start
     *
     * @test
     */
    public function testStart()
    {
        $this->object->setStart(1);
        $get = $this->object->getStart();
        $this->assertEquals(1, $get, 'assert failed, \Yana\Db\Ddl\Sequence : expected "1" as number');

        $this->object->setStart(0);
        $get = $this->object->getStart();
        $this->assertNull($get, 'assert failed, \Yana\Db\Ddl\Sequence : expected null, start is not set');
    }

    /**
     * Start
     *
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     * @test
     */
    public function testSetStartInvalidArgument1()
    {
        $this->object->setMin(6);
        $this->object->setStart(5);
    }

    /**
     * Increment
     *
     * @test
     */
    public function testIncrement()
    {

        $get = $this->object->getIncrement();
        $this->assertEquals(1, $get, 'if not defined otherwise, Sequenz should iterate with 1-Steps');

        $this->object->setIncrement(2);
        $get = $this->object->getIncrement();
        $this->assertEquals(2, $get, 'assert failed, \Yana\Db\Ddl\Sequence : the values should be equal');

        try {
            $this->object->setIncrement(0);
            $this->fail("Increment value may not be set to '0'.");
        } catch (\Yana\Core\Exceptions\InvalidArgumentException $e) {
            // success
        }
    }

    /**
     * Increment
     *
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     * @test
     */
    function testSetIncrementInvalidArgument()
    {
         $this->object->setIncrement(0);
    }

    /**
     * Min
     *
     * @test
     */
    public function testMin()
    {
        $this->object->setMin();
        $get = $this->object->getMin();
        $this->assertEquals(null, $get, 'setMin() without arguments should reset the property.');

        $this->object->setMin(1);
        $get = $this->object->getMin();
        $this->assertEquals(1, $get, 'getMin() should return the same value as previously set by setMin().');

        $this->object->setStart(2);
        $this->object->setMin(2); // should succeed
        $get = $this->object->getMin();
        $this->assertEquals(2, $get, 'setMin() to lower boundary must succeed.');
        try {
            $this->object->setMin(3);
            $this->fail("Should not be able to set minimum higher than start value.");
        } catch (\Yana\Core\Exceptions\InvalidArgumentException $e) {
            // success
        }
    }

    /**
     * Max
     *
     * @test
     */
    public function testMax()
    {
        $this->object->setMax();
        $get = $this->object->getMax();
        $this->assertEquals(null, $get, 'setMax() without arguments should reset the property.');

        $this->object->setMax(3);
        $get = $this->object->getMax();
        $this->assertEquals(3, $get, 'getMax() should return the same value as previously set by setMax().');

        $this->object->setStart(2);
        $this->object->setMax(2); // should succeed
        $get = $this->object->getMax();
        $this->assertEquals(2, $get, 'setMax() to lower boundary must succeed.');
        try {
            $this->object->setMax(1);
            $this->fail("Should not be able to set maximum lower than start value.");
        } catch (\Yana\Core\Exceptions\InvalidArgumentException $e) {
            // success
        }
    }

}

?>