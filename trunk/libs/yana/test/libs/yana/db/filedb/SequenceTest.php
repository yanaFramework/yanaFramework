<?php
/**
 * PHPUnit test-case: Sequence
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

namespace Yana\Db\FileDb;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * Test class for Sequence
 *
 * @package  test
 */
class SequenceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var  \Yana\Db\FileDb\Sequence
     */
    protected $ascendingSequence;

    /**
     * @var  \Yana\Db\FileDb\Sequence
     */
    protected $descendingSequence;

    /**
     * @var  string
     */
    protected $ascendingId = "";

    /**
     * @var  string
     */
    protected $descendingId = "";

    /**
     * constructor
     *
     * @ignore
     */
    public function __construct()
    {
        $this->ascendingId = mb_strtoupper(__CLASS__) . '_ASC';
        $this->descendingId = mb_strtoupper(__CLASS__) . '_DESC';
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        chdir(CWD . '../../..' );
        \Yana\Db\FileDb\Driver::setBaseDirectory(CWD . '/resources/db/');
        try {

            \Yana\Db\FileDb\Sequence::create($this->ascendingId, +1, null, null, +3, false);
            \Yana\Db\FileDb\Sequence::create($this->descendingId, -1, null, -3, null, true);
            $this->ascendingSequence = new Sequence($this->ascendingId);
            $this->descendingSequence = new Sequence($this->descendingId);

        } catch (\Exception $e) {
            $this->markTestSkipped("Unable to connect to database: " . $e->getMessage());
            chdir(CWD);
        }
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        unset($this->ascendingSequence);
        unset($this->descendingSequence);
        \Yana\Db\FileDb\Sequence::drop($this->ascendingId);
        \Yana\Db\FileDb\Sequence::drop($this->descendingId);
        chdir(CWD);
    }

    /**
     * increment getter and setter
     *
     * @test
     * @covers  \Yana\Db\FileDb\Sequence
     */
    public function testIncrement()
    {
        $this->ascendingSequence->setIncrement(1);
        $increment = $this->ascendingSequence->getIncrement();
        $this->assertEquals(1, $increment);
    }

    /**
     * max getter and setter
     *
     * @test
     * @covers  \Yana\Db\FileDb\Sequence
     */
    public function testMax()
    {
        $this->ascendingSequence->setMax(1);
        $max = $this->ascendingSequence->getMax();
        $this->assertEquals(1, $max);
    }

    /**
     * max getter and setter
     *
     * @test
     * @covers  \Yana\Db\FileDb\Sequence
     * @expectedException  \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testMaxInvalidArgumentException()
    {
        $min = $this->ascendingSequence->getMin();
        $this->ascendingSequence->setMax($min - 1);
        $this->fail("Should not be able to set max < min.");
    }

    /**
     * min getter and setter
     *
     * @test
     * @covers  \Yana\Db\FileDb\Sequence
     */
    public function testMin()
    {
        $this->ascendingSequence->setMin(1);
        $min = $this->ascendingSequence->getMin();
        $this->assertEquals(1, $min);
    }

    /**
     * min getter and setter
     *
     * @test
     * @covers  \Yana\Db\FileDb\Sequence
     * @expectedException  \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testMinInvalidArgumentException()
    {
        $max = $this->ascendingSequence->getMax();
        $this->ascendingSequence->setMin($max + 1);
        $this->fail("Should not be able to set min > max.");
    }

    /**
     * cycle getter and setter
     *
     * @test
     * @covers  \Yana\Db\FileDb\Sequence
     */
    public function testCycle()
    {
        $this->ascendingSequence->setCycle(true);
        $cycle = $this->ascendingSequence->isCycle(true);
        $this->assertTrue($cycle);
    }

    /**
     * test value-getter (ascending non-cyclic sequence)
     *
     * @test
     * @covers  \Yana\Db\FileDb\Sequence
     */
    public function testGetValueForAscendingSequence()
    {
        $currentValue = $this->ascendingSequence->getCurrentValue();
        $start = $this->ascendingSequence->getMin();
        $this->assertEquals($start, $currentValue, "Current value for asc. sequence should equal start value.");

        $nextValue = $this->ascendingSequence->getNextValue();
        $this->assertEquals(++$start, $nextValue, "Next value for asc. sequence starting at 1 should be 2.");

        $currentValue = $this->ascendingSequence->getCurrentValue();
        $this->assertEquals($nextValue, $currentValue, "Current value should not modify the value set with getNextValue().");

        $nextValue = $this->ascendingSequence->getNextValue();
        $this->assertEquals(++$start, $nextValue, "Next value for asc. sequence with current value at 2 should be 3.");

        // non-cyclic sequence should throw an OutOfBoundsException, when reaching max-value
        try {

            $this->ascendingSequence->getNextValue();
            $this->fail("Non-cyclic sequence should throw an OutOfBoundsException, when reaching max-value.");

        } catch (\Yana\Core\Exceptions\OutOfBoundsException $e) {
            // success
        } catch (\Exception $e) {
            $this->fail("Unexpected exception: " . $e->getMessage());
        }

    }

    /**
     * test value-getter (descending sequence)
     *
     * @test
     * @covers  \Yana\Db\FileDb\Sequence
     */
    public function testGetValueForDescendingSequence()
    {
        $currentValue = $this->descendingSequence->getCurrentValue();
        $start = $this->descendingSequence->getMax();
        $this->assertEquals($start, $currentValue, "Current value for desc. sequence should equal start value.");

        $nextValue = $this->descendingSequence->getNextValue();
        $this->assertEquals($start - 1, $nextValue, "Next value for desc. sequence starting at -1 should be -2.");

        $currentValue = $this->descendingSequence->getCurrentValue();
        $this->assertEquals($nextValue, $currentValue, "Current value should not modify the value set with getNextValue().");

        $nextValue = $this->descendingSequence->getNextValue();
        $this->assertEquals($start - 2, $nextValue, "Next value for desc. sequence with current value at -2 should be -3.");

        $nextValue = $this->descendingSequence->getNextValue();
        $this->assertEquals($start, $nextValue, "Cyclic desc. sequence should wrap around and reset to max-value.");

    }

    /**
     * set current value
     *
     * @test
     */
    public function testSetCurrentValue()
    {
        $currentValue = $this->ascendingSequence->getCurrentValue();
        $start = $this->ascendingSequence->getMin();
        $this->assertEquals($start, $currentValue, "Current value for asc. sequence should equal start value.");

        $this->ascendingSequence->setCurrentValue(++$start);
        $currentValue = $this->ascendingSequence->getCurrentValue();
        $this->assertEquals($start, $currentValue, "Current value for asc. sequence should match the value with setCurrentValue().");
    }

    /**
     * set current value
     *
     * @test
     * @expectedException \Yana\Core\Exceptions\OutOfBoundsException
     */
    public function testSetCurrentValueOutOfBoundsException()
    {
        $max = $this->ascendingSequence->getMax();
        $this->ascendingSequence->setCurrentValue($max + 1);
        $this->fail("Should not be able to set value outside range [min, max].");
    }

    /**
     * equals
     *
     * @test
     */
    public function testEquals()
    {
        $equals = $this->ascendingSequence->equals($this->descendingSequence);
        $this->assertFalse($equals, "Two different sequences must not be equal.");

        $equals = $this->ascendingSequence->equals($this->ascendingSequence);
        $this->assertTrue($equals, "Two itentical sequences must be equal.");
    }

}

?>