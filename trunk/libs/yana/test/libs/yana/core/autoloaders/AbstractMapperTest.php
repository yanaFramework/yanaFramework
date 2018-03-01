<?php
/**
 * YANA library
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

namespace Yana\Core\Autoloaders;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package test
 * @ignore
 */
class MyMapper extends \Yana\Core\Autoloaders\AbstractMapper
{

    /**
     * Does nothing.
     *
     * @param   string  $className  including namespace
     * @return  string
     */
    public function mapClassNameToFilePath($className)
    {
        // intentionally left blank
    }

    /**
     * Removes namespace-prefix from class-name.
     *
     * @param   string  $className  including prefix
     * @return  string
     */
    public function removeNameSpacePrefix($className)
    {
        return $this->_removeNameSpacePrefix($className);
    }
}


/**
 * @package  test
 */
class AbstractMapperTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Core\Autoloaders\MyMapper
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Core\Autoloaders\MyMapper();
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
        $this->assertSame("", $this->object->getBaseDirectory());
    }

    /**
     * @test
     */
    public function testGetFileExtension()
    {
        $this->assertSame(".php", $this->object->getFileExtension());
    }

    /**
     * @test
     */
    public function testGetFilePrefix()
    {
        $this->assertSame("", $this->object->getFilePrefix());
    }

    /**
     * @test
     */
    public function testGetNameSpace()
    {
        $this->assertSame("", $this->object->getNameSpace());
    }

    /**
     * @test
     */
    public function testGetNameSpacePrefix()
    {
        $this->assertSame("", $this->object->getNameSpacePrefix());
    }

    /**
     * @test
     */
    public function testSetBaseDirectory()
    {
        $this->assertSame(__DIR__, $this->object->setBaseDirectory(__DIR__)->getBaseDirectory());
    }

    /**
     * @test
     */
    public function testSetFileExtension()
    {
        $this->assertSame('Ext', $this->object->setFileExtension('Ext')->getFileExtension());
    }

    /**
     * @test
     */
    public function testSetFilePrefix()
    {
        $this->assertSame('Pre', $this->object->setFilePrefix('Pre')->getFilePrefix());
    }

    /**
     * @test
     */
    public function testSetNameSpace()
    {
        $this->assertSame('NameSpace', $this->object->setNameSpace('NameSpace')->getNameSpace());
    }

    /**
     * @test
     */
    public function testSetNameSpacePrefix()
    {
        $this->assertSame('NameSpacePrefix', $this->object->setNameSpacePrefix('NameSpacePrefix')->getNameSpacePrefix());
    }

    /**
     * @test
     */
    public function testRemoveNameSpacePrefix()
    {
        $this->assertSame('NameSpace\\Class', $this->object->setNameSpacePrefix('Pre\\')->removeNameSpacePrefix('Pre\\NameSpace\\Class'));
    }

}
