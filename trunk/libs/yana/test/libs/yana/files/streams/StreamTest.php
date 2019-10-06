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

namespace Yana\Files\Streams;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class StreamTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Files\Streams\Stream
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Files\Streams\Stream();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        if ($this->object->isRegistered('null')) {
            $this->object->unregisterWrapper('null');
        }
    }

    /**
     * @test
     */
    public function testRegisterWrapper()
    {
        $this->object->registerWrapper('null');
        $this->assertEquals("", \file_get_contents('null://path/file.ext'));
    }

    /**
     * @test
     */
    public function testGetWrappers()
    {
        $list = $this->object->getWrappers();
        $this->assertInternalType('array', $list);
    }

    /**
     * @test
     */
    public function testIsRegistered()
    {
        $this->assertFalse($this->object->isRegistered('null'));
        $this->object->registerWrapper('null');
        $this->assertTrue($this->object->isRegistered('null'));
    }

    /**
     * @test
     */
    public function testRestoreWrapper()
    {
        $this->object->unregisterWrapper('file');
        $this->object->registerWrapper('file', 'null');
        $this->assertTrue($this->object->restoreWrapper('file'));
        $this->assertTrue($this->object->isRegistered('file'));
    }

    /**
     * @test
     */
    public function testUnregisterWrapper()
    {
        $this->object->registerWrapper('null');
        $this->assertTrue($this->object->isRegistered('null'));
        $this->object->unregisterWrapper('null');
        $this->assertFalse($this->object->isRegistered('null'));
    }

}
