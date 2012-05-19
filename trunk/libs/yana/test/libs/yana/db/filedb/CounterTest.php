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

namespace Yana\Db\FileDb;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * Counter test-case
 *
 * @package  test
 */
class CounterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var    Counter
     * @access protected
     */
    protected $_object;

    /**
     * @var    Counter
     * @access protected
     */
    protected $_objectNoIP;

    /**
     * @var    string
     * @access protected
     */
    protected $_counterId = "";

    /**
     * @var    string
     * @access protected
     */
    protected $_counterNoIPId = "";

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
        $this->_counterId = __CLASS__ . '\\IP';
        $this->_counterNoIPId = __CLASS__ . '\\NOIP';
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        \Yana\Db\Ddl\DDL::setDirectory(CWD . '/resources/db/');
        try {
            \Yana\Db\FileDb\Counter::create($this->_counterId, 1, null, null, null, true);
            \Yana\Db\FileDb\Counter::create($this->_counterNoIPId, 1, null, null, null, true, false);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->_object = new \Yana\Db\FileDb\Counter($this->_counterId);
        $this->_objectNoIP = new \Yana\Db\FileDb\Counter($this->_counterNoIPId);

        // check if counter exist - expected true
        $counter = \Yana\Db\FileDb\Counter::exists($this->_counterNoIPId);
        $this->assertTrue($counter, 'assert failed, the counter should be exist');
        // expected false for a non existing counter name
        $counter = \Yana\Db\FileDb\Counter::exists('nonIP');
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
        unset($this->_object);
        unset($this->_objectNoIP);
        \Yana\Db\FileDb\Counter::drop($this->_counterId);
        \Yana\Db\FileDb\Counter::drop($this->_counterNoIPId);
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
        $this->_object->useIp();
        $hasIp = $this->_object->hasIp();
        $this->assertTrue($hasIp, "Counter::useIP() should default to true.");

        $this->_object->useIp(false);
        $hasIp = $this->_object->hasIp();
        $this->assertFalse($hasIp, "Counter::hasIP() should return the value previously set by Counter::useIP().");
    }

    /**
     * get info
     *
     * @test
     */
    public function testInfo()
    {
        $this->_object->setInfo("info");
        $info = $this->_object->getInfo();
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
        $expectedValueUsingIp = $this->_object->getCurrentValue() + ($this->_object->getIncrement() * 2);
        $expectedValueNoIp = $this->_objectNoIP->getCurrentValue() + ($this->_objectNoIP->getIncrement() * 3);

        // should incremet both
        $_SERVER['REMOTE_ADDR'] = $ipList[0];
        $this->_object->getNextValue();
        $this->_objectNoIP->getNextValue();

        // should incremet both
        $_SERVER['REMOTE_ADDR'] = $ipList[1];
        $this->_object->getNextValue();
        $this->_objectNoIP->getNextValue();

        // should not incremet Counter using IP checking
        $nextValueUsingIp = $this->_object->getNextValue();
        $nextValueNoIp = $this->_objectNoIP->getNextValue();

        $this->assertEquals($expectedValueUsingIp, $nextValueUsingIp, "Counter with IP does not increment properly");
        $this->assertEquals($expectedValueNoIp, $nextValueNoIp, "Counter without IP does not increment properly");

        $ips = $this->_object->getIps();
        $this->assertEquals($ips, $ipList, "IPs are not stored correctly");
    }

    /**
     * test instance
     *
     * @test
     */
    public function testInstance()
    {
        $counterInstance = \Yana\Db\FileDb\Counter::getInstance($this->_counterId);
        $compareResult = $this->_object->equals($counterInstance);
        $this->assertTrue($compareResult, 'assert failed, objects need too be equal - true expected');
    }

}

?>