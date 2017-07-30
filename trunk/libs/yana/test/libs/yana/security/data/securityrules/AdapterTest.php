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

namespace Yana\Security\Data\SecurityRules;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../../include.php';

/**
 * Test-case
 *
 * @package  test
 */
class AdapterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\NullConnection
     */
    protected $connection;

    /**
     * @var Adapter
     */
    protected $object;

    /**
     * Constructor
     *
     * @ignore
     */
    public function __construct()
    {
        \Yana\Db\Ddl\DDL::setDirectory(CWD. 'resources/');
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        try {

            \Yana\Db\FileDb\Driver::setBaseDirectory(CWD. 'resources/db/');
            \Yana\Db\Ddl\DDL::setDirectory(CWD. 'resources/');
            $schema = \Yana\Files\XDDL::getDatabase('user');
            $this->connection = new \Yana\Db\FileDb\Connection($schema);
            restore_error_handler();

        } catch (\Exception $e) {
            $this->markTestSkipped("Unable to connect to database");
        }
        $this->object = new \Yana\Security\Data\SecurityRules\Adapter($this->connection);
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
    public function testFindEntities()
    {
        $entities = $this->object->findEntities('administrator', 'default');
        $this->assertCount(3, $entities);
        $entity0 = new \Yana\Security\Data\SecurityRules\Rule('ADMIN', 'DEFAULT', true, 'DEFAULT');
        $entity0->setUserName('ADMINISTRATOR');
        $this->assertEquals($entity0->setId(0), $entities[0]);
        $entity1 = new \Yana\Security\Data\SecurityRules\Rule('', 'PRINT', true, 'DEFAULT');
        $entity1->setUserName('ADMINISTRATOR');
        $this->assertEquals($entity1->setId(2), $entities[1]);
        $entity2 = new \Yana\Security\Data\SecurityRules\Rule('ADMIN', 'ADMIN', true, 'DEFAULT');
        $entity2->setUserName('ADMINISTRATOR');
        $this->assertEquals($entity2->setId(10), $entities[2]);
    }

    /**
     * @test
     */
    public function testFindEntitiesWithoutProfile()
    {
        $entities = $this->object->findEntities('administrator');
        $this->assertCount(4, $entities);
        $entity0 = new \Yana\Security\Data\SecurityRules\Rule('ADMIN', 'DEFAULT', true, 'DEFAULT');
        $entity0->setUserName('ADMINISTRATOR');
        $this->assertEquals($entity0->setId(0), $entities[0]);
        $entity1 = new \Yana\Security\Data\SecurityRules\Rule('MOD', 'DEFAULT', true, 'foo');
        $entity1->setUserName('ADMINISTRATOR');
        $this->assertEquals($entity1->setId(1), $entities[1]);
        $entity2 = new \Yana\Security\Data\SecurityRules\Rule('', 'PRINT', true, 'DEFAULT');
        $entity2->setUserName('ADMINISTRATOR');
        $this->assertEquals($entity2->setId(2), $entities[2]);
        $entity3 = new \Yana\Security\Data\SecurityRules\Rule('ADMIN', 'ADMIN', true, 'DEFAULT');
        $entity3->setUserName('ADMINISTRATOR');
        $this->assertEquals($entity3->setId(10), $entities[3]);
    }

}
