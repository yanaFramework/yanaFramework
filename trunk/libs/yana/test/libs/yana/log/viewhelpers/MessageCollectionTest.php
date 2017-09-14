<?php
/**
 * PHPUnit test-case.
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

namespace Yana\Log\ViewHelpers;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class MessageCollectionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Log\ViewHelpers\MessageCollection
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Log\ViewHelpers\MessageCollection();
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
    public function testOffsetSet()
    {
        $this->assertEquals(0, count($this->object));
        $object = new \Yana\Log\ViewHelpers\Message();
        $this->assertEquals($object, $this->object[1] = $object);
        $this->assertEquals(1, count($this->object));
        $this->assertEquals($object, $this->object[1]);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testOffsetSetInvalidArgumentException()
    {
        $this->object[1] = "";
    }

    /**
     * @test
     */
    public function testGetLevel()
    {
        $this->assertEquals(\Yana\Log\ViewHelpers\MessageLevelEnumeration::ALERT, $this->object->getLevel());
    }

    /**
     * @test
     */
    public function testUpdateLevel()
    {
        $level = $this->object->updateLevel(\Yana\Log\TypeEnumeration::INFO)->getLevel();
        $this->assertEquals(\Yana\Log\ViewHelpers\MessageLevelEnumeration::ALERT, $level);
        $level = $this->object->updateLevel(\Yana\Log\TypeEnumeration::WARNING)->getLevel();
        $this->assertEquals(\Yana\Log\ViewHelpers\MessageLevelEnumeration::WARNING, $level);
        $level = $this->object->updateLevel(\Yana\Log\TypeEnumeration::INFO)->getLevel();
        $this->assertEquals(\Yana\Log\ViewHelpers\MessageLevelEnumeration::WARNING, $level);
        $level = $this->object->updateLevel(\Yana\Log\TypeEnumeration::SUCCESS)->getLevel();
        $this->assertEquals(\Yana\Log\ViewHelpers\MessageLevelEnumeration::MESSAGE, $level);
        $level = $this->object->updateLevel(\Yana\Log\TypeEnumeration::ERROR)->getLevel();
        $this->assertEquals(\Yana\Log\ViewHelpers\MessageLevelEnumeration::MESSAGE, $level);
    }

}
