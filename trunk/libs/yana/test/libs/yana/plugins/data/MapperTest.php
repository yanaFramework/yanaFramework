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

namespace Yana\Plugins\Data;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class MapperTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Plugins\Data\Mapper
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Plugins\Data\Mapper();
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
    public function testToEntityEmpty()
    {
        $row = array(
            \Yana\Plugins\Data\Tables\PluginEnumeration::ID => 0,
        );
        $entity = $this->object->toEntity($row);
        $this->assertSame('0', $entity->getId());
        $this->assertSame(false, $entity->isActive());
    }

    /**
     * @test
     */
    public function testToEntity()
    {
        $row = array(
            \Yana\Plugins\Data\Tables\PluginEnumeration::ID => 'Test',
            \Yana\Plugins\Data\Tables\PluginEnumeration::IS_ACTIVE => 1,
        );
        $entity = $this->object->toEntity($row);
        $this->assertSame('Test', $entity->getId());
        $this->assertSame(true, $entity->isActive());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testToEntityInvalidArgumentException()
    {
        $this->object->toEntity(array());
    }

    /**
     * @test
     */
    public function testToDatabaseRow()
    {
        $entity = new \Yana\Plugins\Data\Entity();
        $entity->setId('Test')
                ->setActive(true);
        $expectedRow = array(
            \Yana\Plugins\Data\Tables\PluginEnumeration::ID => 'Test',
            \Yana\Plugins\Data\Tables\PluginEnumeration::IS_ACTIVE => true,
        );
        $this->assertEquals($expectedRow, $this->object->toDatabaseRow($entity));
    }

}
