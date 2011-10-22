<?php
/**
 * PHPUnit test-case: BlockFile
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
 * Test class for BlockFile
 *
 * @package  test
 *
 */
class BlockFileTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var  BlockFile
     */
    protected $_object;

    /**
     * @var  string
     */
    protected $_source = 'resources/blockFile.txt';

    /**
     * Constructor
     *
     * @ignore
     */
    public function __construct()
    {
        // intentionally left blank
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->_object = new BlockFile(CWD . $this->_source);
        $this->_object->reset();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
       unset ($this->_object);
    }

    /**
     * test
     *
     *
     * @test
     */
    function test()
    {
        $input = 'qwertyTest';
        $set = $this->_object->setContent($input);
        $this->assertTrue($set, 'assert failed, expected true for set content');

        $this->assertTrue($this->_object->write(), 'assert failed, write has failed');

        $this->_object->read();

        $this->assertEquals($input, $this->_object->getContent(), 'assert failed, the given content should be match the expected');
        $this->assertFalse($this->_object->isBlocked(), 'assert failed, the users premissions are too low');

        $input2 = 'ytrewq';
        $this->assertTrue($this->_object->set($input2), 'assert failed, set content has failed');

        $this->assertTrue($this->_object->write(), 'assert failed, write has failed');

        $get = $this->_object->getContent();
        $this->assertEquals($input2."\n", $get, 'assert failed, the given content should be match the expected');
        $this->assertEquals($input2."\n", $this->_object->__toString(), 'assert failed, the given string should be match the expected');
    }

}

?>