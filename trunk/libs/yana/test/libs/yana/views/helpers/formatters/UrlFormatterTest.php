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
class MyUrlFormatterDependencyContainer implements \Yana\Core\Dependencies\IsUrlFormatterContainer
{
    public function getApplicationUrlParameters(): string
    {
        return "";
    }

}
/**
 * @package  test
 */
class UrlFormatterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Views\Helpers\Formatters\UrlFormatter
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Views\Helpers\Formatters\UrlFormatter();
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
        \Yana\Views\Helpers\Formatters\UrlFormatter::setDependencyContainer(new \Yana\Core\Dependencies\UrlFormatterContainer('test'));
        $_SERVER['PHP_SELF'] = "";
        $this->assertSame('http://test?&amp;a=1&amp;b=2', $this->object->__invoke('a=1&b=2'));
        \Yana\Views\Helpers\Formatters\UrlFormatter::setDependencyContainer(new \Yana\Core\Dependencies\UrlFormatterContainer(''));
    }

    /**
     * @test
     */
    public function testSetDependencyContainer()
    {
        $container = new \Yana\Views\Helpers\Formatters\MyUrlFormatterDependencyContainer();
        \Yana\Views\Helpers\Formatters\UrlFormatter::setDependencyContainer($container);
        $this->assertSame($container, \Yana\Views\Helpers\Formatters\UrlFormatter::getDependencyContainer());
    }

}
