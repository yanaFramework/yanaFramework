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

namespace Yana\Views\Helpers\Formatters;

/**
 * @ignore
 */
require_once dirname(__FILE__) . '/../../../../../include.php';

/**
 * @package  test
 * @ignore
 */
class MyFooFormatter extends \Yana\Core\StdObject implements \Yana\Views\Helpers\IsFormatter
{
    /**
     * @param   string  $string  ignored
     * @return  string
     */
    public function __invoke($string)
    {
        return "foo";
    }
}

/**
 * @package  test
 */
class FormatterCollectionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Views\Helpers\Formatters\FormatterCollection
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Views\Helpers\Formatters\FormatterCollection();
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
        $this->assertSame("Test", $this->object->__invoke("Test"));
        $this->object->offsetSet(null, new \Yana\Views\Helpers\Formatters\NullFormatter());
        $this->assertSame("Test", $this->object->__invoke("Test"));
        $this->object->offsetSet(null, new \Yana\Views\Helpers\Formatters\MyFooFormatter());
        $this->assertSame("foo", $this->object->__invoke("Test"));
    }

    /**
     * @test
     */
    public function testOffsetSet()
    {
        $formatter = new \Yana\Views\Helpers\Formatters\NullFormatter();
        $this->assertSame($formatter, $this->object->offsetSet(null, $formatter));
        $this->assertCount(1, $this->object);
        $this->assertSame($formatter, $this->object->offsetSet(null, $formatter));
        $this->assertCount(2, $this->object);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testOffsetSetInvalidArgumentException()
    {
        $this->object->offsetSet(null, new \Yana\Core\StdObject());
    }

}
