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

namespace Yana\Db;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../include.php';

/**
 * @ignore
 * @package  test
 */
class MySchemaFactory extends \Yana\Db\SchemaFactory
{
    /**
     * @return  \Yana\Data\Adapters\IsDataAdapter
     */
    public function getCache()
    {
        return $this->_getCache();
    }
}

/**
 * @package  test
 */
class SchemaFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\MySchemaFactory
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Db\MySchemaFactory(new \Yana\Data\Adapters\ArrayAdapter());
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
    public function testCreateSchema()
    {
        $schema = $this->object->createSchema('check');
        $this->assertSame('check', $this->object->createSchema('check')->getName());
        $this->assertSame($schema, $this->object->createSchema('check'));
    }

    /**
     * @test
     */
    public function testSetCache()
    {
        $cache = new \Yana\Data\Adapters\ArrayAdapter();
        $this->assertSame($cache, $this->object->setCache($cache)->getCache());
    }

}
