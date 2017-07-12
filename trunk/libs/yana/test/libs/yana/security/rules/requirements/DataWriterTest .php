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
class DataWriterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Security\Rules\Requirements\DataWriter
     */
    protected $emptyWriter;

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
        $this->emptyWriter = new \Yana\Security\Rules\Requirements\DataWriter(new \Yana\Db\NullConnection());
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
        $eventConfigurations = new \Yana\Plugins\Configs\MethodCollection();
        $this->assertTrue($this->emptyWriter->__invoke($eventConfigurations) instanceof $this->emptyWriter);
    }

    /**
     * @test
     */
    public function testCommitChanges()
    {
        $this->assertTrue($this->emptyWriter->commitChanges() instanceof \Yana\Security\Rules\Requirements\DataWriter);
    }

    /**
     * @test
     */
    public function testFlushRequirements()
    {
        $this->assertTrue($this->emptyWriter->flushRequirements() instanceof \Yana\Security\Rules\Requirements\DataWriter);
    }

    /**
     * @test
     */
    public function testFlushActions()
    {
        $this->assertTrue($this->emptyWriter->flushActions() instanceof \Yana\Security\Rules\Requirements\DataWriter);
    }

    /**
     * @test
     */
    public function testInsertRequirements()
    {
        $this->assertTrue($this->emptyWriter->insertRequirements(array()) instanceof \Yana\Security\Rules\Requirements\DataWriter);
    }

    /**
     * @test
     */
    public function testInsertRoles()
    {
        $this->assertTrue($this->emptyWriter->insertRoles(array()) instanceof \Yana\Security\Rules\Requirements\DataWriter);
    }

    /**
     * @test
     */
    public function testInsertGroups()
    {
        $this->assertTrue($this->emptyWriter->insertGroups(array()) instanceof \Yana\Security\Rules\Requirements\DataWriter);
    }

    /**
     * @test
     */
    public function testInsertActions()
    {
        $this->assertTrue($this->emptyWriter->insertActions(array()) instanceof \Yana\Security\Rules\Requirements\DataWriter);
    }

}

?>