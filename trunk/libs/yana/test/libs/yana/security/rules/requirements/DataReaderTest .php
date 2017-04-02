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

namespace Yana\Security\Rules\Requirements;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../../include.php';

/**
 * Test-case
 *
 * @package  test
 */
class DataReaderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Security\Rules\Requirements\DataReader
     */
    protected $filledReader;

    /**
     * @var \Yana\Security\Rules\Requirements\DataReader
     */
    protected $emptyReader;

    /**
     * Constructor
     *
     * @ignore
     */
    public function __construct()
    {
        parent::__construct();
        \Yana\Db\Ddl\DDL::setDirectory(CWD . '/../../../config/db/');
        \Yana\Db\FileDb\Driver::setBaseDirectory(CWD . '/resources/db/');
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->emptyReader = new \Yana\Security\Rules\Requirements\DataReader(new \Yana\Db\NullConnection());

        $schema = \Yana\Files\XDDL::getDatabase('user');
        $database = new \Yana\Db\FileDb\Connection($schema);
        $this->filledReader = new \Yana\Security\Rules\Requirements\DataReader($database);
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
    public function testLoadRequirementsByAssociatedAction()
    {
        $collection = $this->filledReader->loadRequirementsByAssociatedAction("ABOUT");
        $this->assertTrue($collection instanceof \Yana\Security\Rules\Requirements\Collection, 'Instance of Collection expected');
        $this->assertCount(2, $collection);
        $this->assertEquals('GUEST', $collection[0]->getGroup());
        $this->assertEquals('DEFAULT', $collection[0]->getRole());
        $this->assertEquals(100, $collection[0]->getLevel());
    }

    /**
     * @test
     * @expectedException \Yana\Security\Rules\Requirements\NotFoundException
     */
    public function testLoadRequirementsByAssociatedActionNotFoundException()
    {
        $this->emptyReader->loadRequirementsByAssociatedAction("");
    }

    /**
     * @test
     */
    public function testLoadRequirementById()
    {
        $requirement = $this->filledReader->loadRequirementById(2091);
        $this->assertTrue($requirement instanceof \Yana\Security\Rules\Requirements\Requirement, 'Instance of Requirement expected');
        $this->assertEquals('GUEST', $requirement->getGroup());
        $this->assertEquals('DEFAULT', $requirement->getRole());
        $this->assertEquals(100, $requirement->getLevel());
    }

    /**
     * @test
     * @expectedException \Yana\Security\Rules\Requirements\NotFoundException
     */
    public function testLoadRequirementByIdNotFoundException()
    {
        $this->emptyReader->loadRequirementById(1);
    }

    /**
     * @test
     */
    public function testLoadListOfGroups()
    {
        $this->assertInternalType('array', $this->emptyReader->loadListOfGroups());
        $this->assertEmpty($this->emptyReader->loadListOfGroups());
    }

    /**
     * @test
     */
    public function testLoadListOfRoles()
    {
        $this->assertInternalType('array', $this->emptyReader->loadListOfRoles());
        $this->assertEmpty($this->emptyReader->loadListOfRoles());
    }

}

?>