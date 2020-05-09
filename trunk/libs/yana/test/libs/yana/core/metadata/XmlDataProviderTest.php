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
declare(strict_types=1);

namespace Yana\Core\MetaData;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class XmlDataProviderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Core\MetaData\XmlDataProvider
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $directory = CWD . '/resources/skin/';
        $this->object = new \Yana\Core\MetaData\XmlDataProvider(new \Yana\Files\Dir($directory));
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
    public function testLoadOject()
    {
        $object = $this->object->loadOject('default.skin');
        $this->assertTrue($object instanceof \Yana\Core\MetaData\IsPackageMetaData);
        $this->assertTrue($object instanceof \Yana\Core\MetaData\PackageMetaData);
        $this->assertSame('Default', $object->getTitle());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function testLoadOjectNotFoundException()
    {
        $this->object->loadOject('no such object');
    }

    /**
     * @test
     */
    public function testGetListOfValidIds()
    {
        $list = $this->object->getListOfValidIds();
        $expected = array("default.skin", "test.skin");
        $this->assertEquals($expected, $list);
    }

}
