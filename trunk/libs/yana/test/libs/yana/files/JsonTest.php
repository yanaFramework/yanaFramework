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
class JsonTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Files\Json
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $file = tempnam(sys_get_temp_dir(), md5(__NAMESPACE__));
        $this->object = new \Yana\Files\Json($file);
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
    public function testGetFile()
    {
        $this->object->setVar('test', 1);
        $this->assertEquals(1, $this->object->getVar('test'));
    }

    /**
     * @test
     */
    public function testEncode()
    {
        $value = array('test' => 1);
        $test = json_encode($value);
        $this->object->setVar('test', 1);
        $encoded = $this->object->getContent();
        $this->assertEquals($test, $encoded);
    }

}
