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

namespace Yana\Plugins\Annotations;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class ReflectionMethodTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Plugins\Annotations\ReflectionMethod
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Plugins\Annotations\ReflectionMethod(__CLASS__, 'documentation');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }

    /**
     * This is a title.
     *
     * This is a description.
     *
     * @ignore
     */
    protected function documentation()
    {

    }

    /**
     * @test
     */
    public function testGetTitle()
    {
        $this->assertSame('This is a title.', $this->object->getTitle());
    }

    /**
     * @test
     */
    public function testGetText()
    {
        $this->assertSame('This is a description.', $this->object->getText());
    }

    /**
     * @test
     */
    public function testGetClassName()
    {
        $this->assertSame(__CLASS__, $this->object->getClassName());
    }

    /**
     * @test
     */
    public function testGetDocComment()
    {
        $regExp = "/\/\*\*\s+\* This is a title.\s+\*\s+\* This is a description.\s+\*\s+\* @ignore\s+\*\//s";
        $this->assertStringStartsWith("/**", $this->object->getDocComment());
        $this->assertRegExp($regExp, $this->object->getDocComment());
        $this->assertStringEndsWith("*/", $this->object->getDocComment());
    }

}
