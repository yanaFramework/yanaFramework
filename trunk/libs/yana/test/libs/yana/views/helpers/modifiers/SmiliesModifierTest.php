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

namespace Yana\Views\Helpers\Modifiers;

/**
 * @ignore
 */
require_once dirname(__FILE__) . '/../../../../../include.php';

/**
 * @package test
 * @ignore
 */
class MySmiliesModifier extends \Yana\Views\Helpers\Modifiers\SmiliesModifier
{

    protected function _getFormatter()
    {
        return new \Yana\Views\Helpers\Formatters\NullFormatter();
    }

    public function getFormatter()
    {
        return parent::_getFormatter();
    }

}

/**
 * @package test
 */
class SmiliesModifierTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Views\Helpers\Modifiers\MySmiliesModifier
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $configurationFactory = new \Yana\ConfigurationFactory();
        $configuration = $configurationFactory->loadConfiguration(CWD . 'resources/system.config.xml');
        $this->object = new \Yana\Views\Helpers\Modifiers\MySmiliesModifier(new \Yana\Core\Dependencies\Container($configuration));
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
        $this->assertSame(123, $this->object->__invoke(123));
        $this->assertSame('', $this->object->__invoke(''));
        $this->assertSame('Test', $this->object->__invoke('Test'));
    }

    /**
     * @test
     */
    public function testGetFormatter()
    {
        $this->assertTrue($this->object->getFormatter() instanceof \Yana\Views\Helpers\Formatters\IconFormatter);
    }

}
