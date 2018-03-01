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
class NullLoader extends \Yana\Core\Autoloaders\Loader
{

    /**
     * Does nothing.
     *
     * @param  string  $fileName
     */
    protected function _includeFile($fileName)
    {
        // intentionally left blank
    }

}

/**
 * @package test
 * @ignore
 */
class LoaderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Core\Autoloaders\NullLoader
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Core\Autoloaders\NullLoader();
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
    public function testLoadClassFile()
    {
        $this->assertFalse($this->object->loadClassFile(__CLASS__)); // Obviously, since there is no class mapper defined yet
        $this->object->getMaps()->offsetSet(null, $mapper1 = new \Yana\Core\Autoloaders\GenericMapper());
        $mapper1->setNameSpace('WontMatch\\')->setBaseDirectory(CWD);
        $this->object->getMaps()->offsetSet(null, $mapper2 = new \Yana\Core\Autoloaders\GenericMapper());
        $mapper2->setNameSpace('Yana\\')->setBaseDirectory(CWD . 'libs/');
        $this->assertTrue($this->object->loadClassFile(__CLASS__));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\ClassNotFoundException
     */
    public function testLoadClassFileClassNotFoundException()
    {
        $this->object->setThrowExceptionWhenClassIsNotFound(true);
        $this->object->loadClassFile(__CLASS__);
    }

}
