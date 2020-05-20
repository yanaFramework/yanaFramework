<?php
/**
 * PHPUnit test-case.
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

namespace Yana\Db\FileDb\Helpers;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../../include.php';

/**
 * @package  test
 */
class ResultLimitHelperTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var array
     */
    protected $result = array();

    /**
     * @var \Yana\Db\FileDb\Helpers\ResultLimitHelper
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->result = array(
            array('1'),
            array('2'),
            array('3')
        );
        $this->object = new \Yana\Db\FileDb\Helpers\ResultLimitHelper($this->result);
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
    public function test__invoke()
    {
        $this->assertSame($this->result, $this->object->__invoke(0, 0));
    }

    /**
     * @test
     */
    public function test__invoke1()
    {
        $this->assertSame(array(array('1')), $this->object->__invoke(0, 1));
    }

    /**
     * @test
     */
    public function test__invoke2()
    {
        $this->assertSame(array(array('1')), $this->object->__invoke(-1, 1));
    }

    /**
     * @test
     */
    public function test__invoke3()
    {
        $this->assertSame(array(array('2')), $this->object->__invoke(1, 1));
    }

    /**
     * @test
     */
    public function test__invoke4()
    {
        $this->assertSame(array(array('2'), array('3')), $this->object->__invoke(1, 2));
    }

    /**
     * @test
     */
    public function test__invoke5()
    {
        $this->assertSame(array(array('2'), array('3')), $this->object->__invoke(1, 3));
    }

    /**
     * @test
     */
    public function test__invoke6()
    {
        $this->assertSame(array(), $this->object->__invoke(4, 0));
    }

}
