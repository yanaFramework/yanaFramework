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

namespace Yana\Plugins\Configs;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class MethodCollectionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Plugins\Configs\MethodCollection
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Plugins\Configs\MethodCollection();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }

    /**
     * @test
     */
    public function testOffsetSet()
    {
        $o = new \Yana\Plugins\Configs\MethodConfiguration();
        $o->setMethodName('methodName');
        $this->object['test'] = $o;
        $this->assertTrue($this->object['test'] instanceof \Yana\Plugins\Configs\IsMethodConfiguration, 'Instance was not added.');
        $this->assertEquals($this->object['test']->getMethodName(), $o->getMethodName());
    }

    /**
     * @test
     */
    public function testOffsetExists()
    {
        $this->assertFalse($this->object->offsetExists('test'));
        $o = new \Yana\Plugins\Configs\MethodConfiguration();
        $o->setMethodName('methodName');
        $this->object['test'] = $o;
        $this->assertTrue($this->object->offsetExists('test'));
    }

    /**
     * @test
     */
    public function testGetReportEmpty()
    {
        $this->assertTrue($this->object->getReport() instanceof \Yana\Report\IsReport);
    }

    /**
     * @test
     */
    public function testGetReport()
    {
        $o1 = new \Yana\Plugins\Configs\MethodConfiguration();
        $o1->setMethodName('methodName1');
        $this->object['1'] = $o1;
        $o2 = new \Yana\Plugins\Configs\MethodConfiguration();
        $o2->setMethodName('methodName2');
        $this->object['2'] = $o2;
        $report = $this->object->getReport();
        $this->assertSame(2, count($report->report));
        $this->assertSame('methodname1', $report->report[0]->getTitle());
        $this->assertSame('methodname2', $report->report[1]->getTitle());
    }

    /**
     * @test
     */
    public function testOffsetUnset()
    {
        $o = new \Yana\Plugins\Configs\MethodConfiguration();
        $o->setMethodName('methodName');
        $this->object['test'] = $o;
        $this->assertTrue($this->object['test'] instanceof \Yana\Plugins\Configs\IsMethodConfiguration, 'Instance was not added.');
        unset($this->object['test']);
        $this->assertTrue($this->object['test'] === null, 'Instance was not unset.');
    }

    /**
     * @test
     */
    public function testOffsetSetAutodetect()
    {
        $o = new \Yana\Plugins\Configs\MethodConfiguration();
        $o->setMethodName('methodName');
        $this->object[] = $o;
        $this->assertTrue($this->object['methodname'] instanceof \Yana\Plugins\Configs\IsMethodConfiguration, 'Instance was not added.');
        $this->assertEquals($this->object['methodname']->getMethodName(), $o->getMethodName());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testOffsetSetInvalidArgumentException()
    {
        $this->object[] = "invalid value";
    }

}
