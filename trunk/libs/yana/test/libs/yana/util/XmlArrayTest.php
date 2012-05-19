<?php
/**
 * PHPUnit test-case: XmlArray
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

namespace Yana\Util;

/**
 * @ignore
 */
require_once __Dir__ . '/../../../include.php';


/**
 * Test class for XmlArray
 *
 * @package  test
 */
class XmlArrayTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var  string
     */
    private $_xmlSource = '<root><child1>1</child1></root>';

    /**
     * @var  \Yana\Util\XmlArray
     */
    private $_object = null;

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
        
        $this->_object = new \Yana\Util\XmlArray($this->_xmlSource);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
        // intentionally left blank
    }

    /**
     * @test
     */
    public function testIsset()
    {
        $this->assertTrue(isset($this->_object->child1));
    }

    /**
     * @test
     */
    public function testToArrayAsNumericArray()
    {
        $array = $this->_object->toArray(true);
        $this->assertInternalType('array', $array, 'assert failed, value is not from type array');
        $expected = array("#tag" => "root", array("#tag" => "child1", "#pcdata" => "1"));
        $this->assertEquals($expected, $array);
    }

    /**
     * @test
     */
    public function testToArrayAsAssociativeArray()
    {
        $array = $this->_object->toArray(false);
        $this->assertInternalType('array', $array);
        $expected = array("child1" => "1");
        $this->assertEquals($expected, $array);
    }
}
?>