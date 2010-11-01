<?php
/**
 * PHPUnit test-case: Flood-File
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

/**
 * @ignore
 */
require_once dirname(__FILE__) . '/include.php';

/**
 * Test class for FloodFile
 *
 * @package  test
 */
class FloodFileTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var     string
     * @access  protected
     */
    protected $file = "resources/floodfiletest.txt";

    /**
     * @var     FloodFile
     * @access  protected
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->object = new FloodFile( CWD . $this->file );
        $this->object->create();
        $this->object->reset();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
        $this->object->delete();
    }

    /**
     * some tests on the basic functions
     *
     * @param string $ipAdr
     * @param int $count
     */
    protected function basicSetTest($ipAdr = "127.127.127.127", $count = 8) {

        for($x=0; $x<$count; $x++)
        {
            // insert is an alias to set()
            $this->object->insert($ipAdr);
        }
        $this->object->read();

        $getContent = explode("\n", $this->object->getContent());

        $this->assertEquals(count($getContent), 3, "unexpected FloodFile length");
        $this->assertEquals((int) $getContent[2], $count, "FloodFile counter did not increment correctly");
        $this->assertEquals($getContent[0], $ipAdr, "FloodFile IP-Adress unexpected");

    }

    /**
     * set()
     *
     * @test
     */
    public function testSet()
    {
        // can the file be written or read, does the counter works correctly
        $this->basicSetTest("127.127.127.127", 8);
        // after changing the ip, does the counter restart
        $this->basicSetTest("127.127.127.128", 3);
    }

    /**
     * insert()
     * this is just an alias to testSet
     *
     * @test
     */
    public function testInsert()
    {
    }

    /**
     * @test
     */
    public function testIsBlocked()
    {
        // generally not blocked, if max = 0
        $this->object->setMax(0);
        $erg = $this->object->isBlocked();
        $this->assertFalse($erg, 'should not be blocked if max is zero');

        // should not be blocked, since, the FloodFile ist still plain
        $this->object->setMax(10);
        $erg = $this->object->isBlocked();
        $this->assertFalse($erg, 'should not be blocked if FloodFile is not created');


        $ipOld = Null;
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $ipOld = $_SERVER['REMOTE_ADDR'];
        }

        $ipNew = "127.127.127.129";
        $_SERVER['REMOTE_ADDR'] = $ipNew;
        $this->basicSetTest($ipNew, 8);
        // below Maximum, should not be blocked
        $erg = $this->object->isBlocked();
        $this->assertFalse($erg, 'should not be blocked if max is not reached');


        $ipNew = "127.127.127.131";
        $_SERVER['REMOTE_ADDR'] = $ipNew;
        $this->basicSetTest($ipNew, 13);
        // above Maximum, should be blocked
        $erg = $this->object->isBlocked();
        $this->assertTrue($erg, 'should be blocked if max is is reached');

        if (isset($ipOld)) {
            $_SERVER['REMOTE_ADDR'] = $ipOld;
        }
     
    }

    /**
     * setMax
     *
     * @test
     */
    public function testSetMax()
    {
        $this->object->setMax(0);
        $max = $this->object->getMax();
        $this->assertEquals($max, 0, "Result of getMax() should equal previously set value");

        $this->object->setMax(11);
        $max = $this->object->getMax();
        $this->assertEquals($max, 11, "Result of getMax() should equal previously set value");
    }

    /**
     * testSetMaxInvalidArgument
     *
     * @expectedException PHPUnit_Framework_Error
     * @test
     */
    function testSetMaxInvalidArgument()
    {
        $this->object->setMax('0');
    }

    /**
     * getMax
     * is testet with setMax
     *
     * @test
     */
    public function testGetMax()
    {
    }
}
?>
