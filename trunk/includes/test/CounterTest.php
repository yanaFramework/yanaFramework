<?php
/**
 * PHPUnit test-case: Counter
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
 * Counter test-case
 *
 * @package  test
 */
class CounterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    Counter
     * @access protected
     */
    protected $object;

    /**
     * @var    Counter
     * @access protected
     */
    protected $objectNoIP;

    /**
     * @var    string
     * @access protected
     */
    protected $counterId = "";

    /**
     * @var    string
     * @access protected
     */
    protected $counterNoIPId = "";

    /**
     * constructor
     *
     * Sets counter names.
     * Note: Counter class encourages the use of namespaces, seperated by backslashes, as introduced
     * in PHP 5.3.
     *
     * @access public
     * @ignore
     */
    public function __construct()
    {
        $this->counterId = __CLASS__ . '\\IP';
        $this->counterNoIPId = __CLASS__ . '\\NOIP';
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        chdir(CWD . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);
        FileDbConnection::setBaseDirectory(CWD . '/resources/db/');
        try {
            Counter::create($this->counterId, 1, null, null, null, true);
            Counter::create($this->counterNoIPId, 1, null, null, null, true, false);
        } catch (Exception $e) {
            $this->fail($e->getMessage());
            chdir(CWD);
        }
        
        $this->object = new Counter($this->counterId);
        $this->objectNoIP = new Counter($this->counterNoIPId);

        // check if counter exist - expected true
        $counter = Counter::exists($this->counterNoIPId);
        $this->assertTrue($counter, 'assert failed, the counter should be exist');
        // expected false for a non existing counter name
        $counter = Counter::exists('nonIP');
        $this->assertFalse($counter, 'assert failed, the counter does not exist');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
        unset($this->object);
        unset($this->objectNoIP);
        Counter::drop($this->counterId);
        Counter::drop($this->counterNoIPId);
        chdir(CWD);
    }

    /**
     * check IP
     *
     * @test
     */
    public function testUseIp()
    {
        // devfault value
        $this->object->useIp();
        $hasIp = $this->object->hasIp();
        $this->assertTrue($hasIp, "Counter::useIP() should default to true.");

        $this->object->useIp(false);
        $hasIp = $this->object->hasIp();
        $this->assertFalse($hasIp, "Counter::hasIP() should return the value previously set by Counter::useIP().");
    }

    /**
     * get info
     *
     * @test
     */
    public function testInfo()
    {
        $this->object->setInfo("info");
        $info = $this->object->getInfo();
        $this->assertEquals("info", $info, "getInfo() should return the value previously set via setInfo()");
    }

    /**
     * get ips
     *
     * @test
     */
    public function testGetIps()
    {
        $ipList = array("1.2.3.4", "1.2.3.5");
        $expectedValueUsingIp = $this->object->getCurrentValue() + ($this->object->getIncrement() * 2);
        $expectedValueNoIp = $this->objectNoIP->getCurrentValue() + ($this->objectNoIP->getIncrement() * 3);

        // should incremet both
        $_SERVER['REMOTE_ADDR'] = $ipList[0];
        $this->object->getNextValue();
        $this->objectNoIP->getNextValue();

        // should incremet both
        $_SERVER['REMOTE_ADDR'] = $ipList[1];
        $this->object->getNextValue();
        $this->objectNoIP->getNextValue();

        // should not incremet Counter using IP checking
        $nextValueUsingIp = $this->object->getNextValue();
        $nextValueNoIp = $this->objectNoIP->getNextValue();

        $this->assertEquals($expectedValueUsingIp, $nextValueUsingIp, "Counter with IP does not increment properly");
        $this->assertEquals($expectedValueNoIp, $nextValueNoIp, "Counter without IP does not increment properly");

        $ips = $this->object->getIps();
        $this->assertEquals($ips, $ipList, "IPs are not stored correctly");
    }

    /**
     * test instance 
     *
     * @test
     */
    public function testInstance()
    {
        $counterInstance = Counter::getInstance($this->counterId);
        $compareResult = $this->object->equals($counterInstance);
        $this->assertTrue($compareResult, 'assert failed, objects need too be equal - true expected');
    }


    /**
     * Counter NotFoundException
     *
     * @expectedException NotFoundException
     * @test
     */
    function testCounterNotFoundException()
    {
        $newInstance = Counter::getInstance('new_counter');
    }
}
?>