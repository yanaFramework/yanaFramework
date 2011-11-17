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

namespace Yana;

/**
 * @ignore
 */
require_once __Dir__ . '/../../include.php';


/**
 * Test class for XmlArray
 *
 * @package  test
 */
class XmlArrayTest extends \PHPUnit_Framework_TestCase
{

    /** @var  string */ protected $xmlfile = 'resources/test.db.xml';

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
        // intentionally left blank
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
     * ToArray
     *
     * @test
     */
    public function testToArray()
    {
        $xml = simplexml_load_file(CWD . $this->xmlfile, '\Yana\XmlArray');
        $array = $xml->toArray(true);
        $this->assertType('array', $array, 'assert failed, value is not from type array');
        $array = $xml->toArray(false);
        $this->assertType('array', $array, 'assert failed, value is not from type array');
    }
}
?>