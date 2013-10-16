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

namespace Yana\Core;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../include.php';

/**
 * Test implementation
 *
 * @package  test
 */
class MyEnumeration extends \Yana\Core\AbstractEnumeration
{

    const A = '1';

}

/**
 * @package  test
 */
class AbstractEnumerationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \ReflectionClass
     */
    private $_reflection;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_reflection = new \ReflectionClass(__NAMESPACE__ . '\\MyEnumeration');
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
    public function testConstructor()
    {
        $constructor = $this->_reflection->getParentClass()->getMethod('__construct');
        $this->assertTrue($constructor->isPrivate());
    }


    /**
     * @test
     */
    public function testGetValidItems()
    {
        $this->assertEquals($this->_reflection->getConstants(), \Yana\Core\MyEnumeration::getValidItems());
    }

    /**
     * @test
     */
    public function testIsValidItem()
    {
        $constants = $this->_reflection->getConstants();
        foreach ($constants as $constant) {
            $this->assertTrue(\Yana\Core\MyEnumeration::isValidItem($constant));
        }
        $this->assertFalse(\Yana\Core\MyEnumeration::isValidItem('not a valid item'));
    }

}

?>