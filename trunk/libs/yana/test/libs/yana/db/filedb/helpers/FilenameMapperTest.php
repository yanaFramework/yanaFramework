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

namespace Yana\Db\FileDb\Helpers;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../../include.php';

/**
 * @package  test
 */
class FilenameMapperTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\FileDb\Helpers\FilenameMapper
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Db\FileDb\Helpers\FilenameMapper();
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
    public function testGetBaseDirectory()
    {
        $this->assertContains(realpath(\Yana\Db\Ddl\DDL::getDirectory()), realpath(\Yana\Db\FileDb\Helpers\FilenameMapper::getBaseDirectory()));
    }

    /**
     * @test
     */
    public function testSetBaseDirectory()
    {
        $oldDirectory = \Yana\Db\FileDb\Helpers\FilenameMapper::getBaseDirectory();
        $this->assertNull(\Yana\Db\FileDb\Helpers\FilenameMapper::setBaseDirectory(__DIR__));
        $this->assertSame(__DIR__, \Yana\Db\FileDb\Helpers\FilenameMapper::getBaseDirectory());
        \Yana\Db\FileDb\Helpers\FilenameMapper::setBaseDirectory($oldDirectory);
    }

    /**
     * @test
     */
    public function test__invoke()
    {
        $database = "db";
        $extension = "ext";
        $tableName = "table";
        $expected = realpath(\Yana\Db\FileDb\Helpers\FilenameMapper::getBaseDirectory()) . \DIRECTORY_SEPARATOR . $database
            . \DIRECTORY_SEPARATOR . $tableName . "." . $extension;
        $this->assertSame($expected, $this->object->__invoke($database, $extension, $tableName));
    }

}
