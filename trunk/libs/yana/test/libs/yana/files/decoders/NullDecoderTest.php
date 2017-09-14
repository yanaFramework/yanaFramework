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

namespace Yana\Files\Decoders;

/**
 * @ignore
 */
require_once __Dir__ . '/../../../../include.php';

/**
 * @package  test
 */
class NullDecoderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Files\Decoders\NullDecoder
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Files\Decoders\NullDecoder();
    }

    /**
     * @test
     */
    public function testGetFile()
    {
        $this->assertSame(1, $this->object->getFile(array(\serialize(1))));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testGetFileInvalidArgumentException()
    {
        $this->object->getFile("no such file");
    }

    /**
     * @test
     */
    public function testEncode()
    {
        $this->assertSame(serialize(array('name' => 'Value')), $this->object->encode('Value', 'Name', \CASE_LOWER));
    }

    /**
     * @test
     */
    public function testDecode()
    {
        $expected = array('name' => 'Value');
        $input = json_encode($expected);
        $this->assertEquals($expected, $this->object->decode(\serialize(array('Name' => 'Value')), \CASE_LOWER));
    }

}
