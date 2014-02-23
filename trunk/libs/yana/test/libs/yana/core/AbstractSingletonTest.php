<?php
/**
 * PHPUnit test-case: Object
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
 * @ignore
 */
class TestSingleton extends \Yana\Core\AbstractSingleton
{

    /**
     * @var  bool
     */
    public $isCalled = false;

    /**
     * @return \Yana\Core\TestSingleton
     */
    protected static function _createNewInstance()
    {
        $instance = parent::_createNewInstance();
        $instance->isCalled = true;
        return $instance;
    }

    /**
     * @return string
     */
    protected static function _getClassName()
    {
        return __CLASS__;
    }

}

/**
 * @ignore
 */
class TestSingletonA extends \Yana\Core\AbstractSingleton
{
    /**
     * @return string
     */
    protected static function _getClassName()
    {
        return __CLASS__;
    }
}

/**
 * @ignore
 */
class TestSingletonB extends \Yana\Core\AbstractSingleton
{

    /**
     * @return string
     */
    protected static function _getClassName()
    {
        return __CLASS__;
    }

}

/**
 * Test class for Object
 *
 * @package  test
 */
class AbstractSingletonTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function testGetInstance()
    {
        $singletonA = \Yana\Core\TestSingletonA::getInstance();
        $singletonB = \Yana\Core\TestSingletonB::getInstance();
        $singletonA2 = \Yana\Core\TestSingletonA::getInstance();

        $this->assertTrue($singletonA instanceof \Yana\Core\TestSingletonA, 'SingletonA has invalid class');
        $this->assertTrue($singletonB instanceof \Yana\Core\TestSingletonB, 'SingletonB has invalid class');
        $this->assertSame($singletonA, $singletonA2);
    }

    public function testWakeUp()
    {
        $singletonA = \Yana\Core\TestSingleton::getInstance();
        $singletonB = unserialize(serialize($singletonA));
        $this->assertTrue($singletonA->isCalled);
        $this->assertSame($singletonB, $singletonA->getInstance());
    }

}

?>