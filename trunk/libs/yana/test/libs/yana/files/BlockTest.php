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

namespace Yana\Files;

/**
 * @ignore
 */
require_once __Dir__ . '/../../../include.php';

/**
 * @package  test
 */
class BlockTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var  \Yana\Files\Block
     */
    protected $_object;

    /**
     * @var  string
     */
    protected $_source = '';

    /**
     * Constructor
     *
     * @ignore
     */
    public function __construct()
    {
        $this->_source = tempnam(sys_get_temp_dir(), __CLASS__);
        file_put_contents($this->_source, '::1');
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_object = new \Yana\Files\Block($this->_source);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
       unset ($this->_object);
    }

    /**
     * @test
     */
    public function testSetContent()
    {
        $input = 'qwertyTest';

        $this->assertTrue($this->_object->setContent($input)->write(), 'assert failed, write has failed');

        $this->_object->read();

        $this->assertEquals($input, $this->_object->getContent(), 'assert failed, the given content should be match the expected');
    }

    /**
     * @test
     */
    public function testIsBlocked()
    {
        $this->assertTrue($this->_object->isBlocked('::1'));
    }

    /**
     * Test IPv4
     *
     * @test 
     */
    public function testWithIpv4()
    {
        $this->_object->setContent(array('127.*.*.*', '::1'));
        $this->assertTrue($this->_object->isBlocked('127.0.0.1'));
    }

    /**
     * Test IPv6
     *
     * @test 
     */
    public function testWithIpv6()
    {
        $this->_object->setContent(array('127.*.*.*', '::1'));
        $this->assertTrue($this->_object->isBlocked('::1'));
    }

}
