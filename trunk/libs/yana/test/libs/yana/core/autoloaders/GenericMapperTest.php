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

namespace Yana\Core\Autoloaders;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class GenericMapperTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Core\Autoloaders\GenericMapper
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Core\Autoloaders\GenericMapper();
        $this->object->setBaseDirectory('Dir')->setFilePrefix('Pre')->setFileExtension('Ext');
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
    public function testMapClassNameToFilePath()
    {
        $this->assertSame('DirPreExt', $this->object->mapClassNameToFilePath(''));
        $this->assertSame('DirPreClassExt', $this->object->mapClassNameToFilePath('Class'));
        $this->assertSame('DirPreClass/NameExt', $this->object->mapClassNameToFilePath('Class_Name'));
        $this->assertSame('DirPreNameSpace/ClassExt', $this->object->mapClassNameToFilePath('NameSpace\\Class'));
        $this->assertSame('DirPreName_Space/ClassExt', $this->object->mapClassNameToFilePath('Name_Space\\Class'));
        $this->assertSame('DirPreName_Space/Class/NameExt', $this->object->mapClassNameToFilePath('Name_Space\\Class_Name'));
        $this->object->setNameSpacePrefix('Base\\');
        $this->assertSame('DirPreNameSpace/ClassExt', $this->object->mapClassNameToFilePath('Base\\NameSpace\\Class'));
    }

}
