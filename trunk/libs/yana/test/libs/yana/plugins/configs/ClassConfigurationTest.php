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

namespace Yana\Plugins\Configs;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class ClassConfigurationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Plugins\Configs\ClassConfiguration
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Plugins\Configs\ClassConfiguration();
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
    public function testSetClassName()
    {
        $this->object->setClassName('ClassName');
        $this->assertEquals('ClassName', $this->object->getClassName());
    }

    /**
     * @test
     */
    public function testSetDirectory()
    {
        $this->object->setDirectory(dirname(__FILE__));
        $this->assertEquals(dirname(__FILE__), $this->object->getDirectory());
    }

    /**
     * @test
     */
    public function testSetTitles()
    {
        $titles = array(
            'a' => 'Ä',
            'a-b' => 'Ae'
        );
        $this->object->setDefaultTitle('test Ä')
            ->setTitles($titles);
        $this->assertEquals('test Ä', $this->object->getTitle());
        $this->assertEquals('Ä', $this->object->getTitle('a'));
        $this->assertEquals('Ae', $this->object->getTitle('a', 'b'));
        $this->assertEquals('Ä', $this->object->getTitle('a', 'c'));
        $this->assertEquals('test Ä', $this->object->getTitle('b'));
    }

    /**
     * @test
     */
    public function testSetDefaultTitle()
    {
        $this->object->setDefaultTitle('test Ä');
        $this->assertEquals('test Ä', $this->object->getTitle());
    }

    /**
     * @test
     */
    public function testSetTexts()
    {
        $texts = array(
            'a' => 'Ä',
            'a-b' => 'Ae'
        );
        $this->object->setDefaultText('test Ä')
            ->setTexts($texts);
        $this->assertEquals('test Ä', $this->object->getText());
        $this->assertEquals('Ä', $this->object->getText('a'));
        $this->assertEquals('Ae', $this->object->getText('a', 'b'));
        $this->assertEquals('Ä', $this->object->getText('a', 'c'));
        $this->assertEquals('test Ä', $this->object->getText('b'));
    }

    /**
     * @test
     */
    public function testSetDefaultText()
    {
        $this->object->setDefaultText('test Ä');
        $this->assertEquals('test Ä', $this->object->getText());
    }

    /**
     * @test
     */
    public function testSetType()
    {
        $this->object->setType('Library');
        $this->assertEquals('library', $this->object->getType());
    }

    /**
     * @test
     */
    public function testSetAuthors()
    {
        $this->object->setAuthors(array('Ä', 'b'));
        $this->assertEquals(array('Ä', 'b'), $this->object->getAuthors());
        $this->assertEquals('Ä, b', $this->object->getAuthor());
    }

    /**
     * @test
     */
    public function testSetPriority()
    {
        $max = \Yana\Plugins\PriorityEnumeration::HIGHEST;
        $min = \Yana\Plugins\PriorityEnumeration::LOWEST;
        $default = \Yana\Plugins\PriorityEnumeration::NORMAL;
        $this->assertEquals($default, $this->object->getPriority());
        $this->assertEquals($min, $this->object->setPriority($min - 1)->getPriority());
        $this->assertEquals($max, $this->object->setPriority($max + 1)->getPriority());
        $this->assertEquals($default, $this->object->setPriority($default)->getPriority());
        $this->assertEquals(\Yana\Plugins\PriorityEnumeration::HIGH, $this->object->setPriority('high')->getPriority());
    }

    /**
     * @test
     */
    public function testSetGroup()
    {
        $this->assertEquals('Test Ä', $this->object->setGroup('Test Ä')->getGroup());
    }

    /**
     * @test
     */
    public function testSetParent()
    {
        $this->assertEquals('ClassName', $this->object->setParent('ClassName')->getParent());
    }

    /**
     * @test
     */
    public function testSetDependencies()
    {
        $value = array('test');
        $this->assertSame($value, $this->object->setDependencies($value)->getDependencies());
    }

    /**
     * @test
     */
    public function testSetLicense()
    {
        $value = 'License @äÜ';
        $this->assertSame($value, $this->object->setLicense($value)->getLicense());
    }

    /**
     * @test
     */
    public function testSetUrl()
    {
        $value = 'Url @äÜ';
        $this->assertSame($value, $this->object->setUrl($value)->getUrl());
    }

    /**
     * @test
     */
    public function testSetVersion()
    {
        $value = 'Version @äÜ';
        $this->assertSame($value, $this->object->setVersion($value)->getVersion());
    }

    /**
     * @test
     */
    public function testSetLastModified()
    {
        $value = 1234567890;
        $this->assertSame($value, $this->object->setLastModified($value)->getLastModified());
        $value2 = -1234567890;
        $this->assertSame($value2, $this->object->setLastModified($value2)->getLastModified());
        $value3 = 0;
        $this->assertSame($value3, $this->object->setLastModified($value3)->getLastModified());
    }

    /**
     * @test
     */
    public function testSetMenus()
    {
        $value = array(new \Yana\Plugins\Menus\Entry());
        $this->assertSame($value, $this->object->setMenus($value)->getMenuNames());
    }

    /**
     * @test
     */
    public function testSetActive()
    {
        $value = -1;
        $this->assertSame($value, $this->object->setActive($value)->getActive());
        $value2 = \Yana\Plugins\ActivityEnumeration::ACTIVE;
        $this->assertSame($value2, $this->object->setActive($value2)->getActive());
        $value3 = \Yana\Plugins\ActivityEnumeration::DEFAULT_ACTIVE;
        $this->assertSame($value3, $this->object->setActive($value3)->getActive());
        $value4 = \Yana\Plugins\ActivityEnumeration::INACTIVE;
        $this->assertSame($value4, $this->object->setActive($value4)->getActive());
    }

    /**
     * @test
     */
    public function testGetLastModified()
    {
        $this->assertNull($this->object->getLastModified());
    }

    /**
     * @test
     */
    public function testGetTitle()
    {
        $this->assertEquals('', $this->object->getTitle());
    }

    /**
     * @test
     */
    public function testGetText()
    {
        $this->assertEquals('', $this->object->getText());
    }

    /**
     * @test
     */
    public function testGetType()
    {
        $this->assertEquals('default', $this->object->getType());
    }

    /**
     * @test
     */
    public function testGetAuthor()
    {
        $this->assertEquals('', $this->object->getAuthor());
    }

    /**
     * @test
     */
    public function testGetAuthors()
    {
        $this->assertEquals(array(), $this->object->getAuthors());
    }

    /**
     * @test
     */
    public function testGetPriority()
    {
        $this->assertEquals(\Yana\Plugins\PriorityEnumeration::NORMAL, $this->object->getPriority());
        $priority = \Yana\Plugins\PriorityEnumeration::NORMAL;
        $this->assertEquals($priority + \Yana\Plugins\PriorityEnumeration::HIGHEST, $this->object->setType(\Yana\Plugins\TypeEnumeration::LIBRARY)->getPriority());
        $this->assertEquals($priority + \Yana\Plugins\PriorityEnumeration::HIGHEST * 2, $this->object->setType(\Yana\Plugins\TypeEnumeration::SECURITY)->getPriority());
    }

    /**
     * @test
     */
    public function testGetGroup()
    {
        $this->assertEquals('', $this->object->getGroup());
    }

    /**
     * @test
     */
    public function testGetParent()
    {
        $this->assertEquals('', $this->object->getParent());
    }

    /**
     * @test
     */
    public function testGetDependencies()
    {
        $this->assertEquals(array(), $this->object->getDependencies());
    }

    /**
     * @test
     */
    public function testGetVersion()
    {
        $this->assertEquals('', $this->object->getVersion());
    }

    /**
     * @test
     */
    public function testGetUrl()
    {
        $this->assertEquals('', $this->object->getUrl());
    }

    /**
     * @test
     */
    public function testGetLicense()
    {
        $this->assertEquals('', $this->object->getLicense());
    }

    /**
     * @test
     */
    public function testGetMenuNames()
    {
        $this->assertEquals(array(), $this->object->getMenuNames());
    }

    /**
     * @test
     */
    public function testGetMenuEntries()
    {
        $this->assertEquals(array(), $this->object->getMenuEntries());
    }

    /**
     * @test
     */
    public function testGetDirectory()
    {
        $this->assertEquals('', $this->object->getDirectory());
    }

    /**
     * @test
     */
    public function testGetActive()
    {
        $this->assertEquals(\Yana\Plugins\ActivityEnumeration::INACTIVE, $this->object->getActive());
    }

    /**
     * @test
     */
    public function testGetPreviewImage()
    {
        $this->assertEquals('/preview.png', $this->object->getPreviewImage());
        $this->object->setDirectory('Ä/b');
        $this->assertEquals('Ä/b/preview.png', $this->object->getPreviewImage());
    }

    /**
     * @test
     */
    public function testGetIcon()
    {
        $this->assertEquals('/icon.png', $this->object->getIcon());
        $this->object->setDirectory('Ä/b');
        $this->assertEquals('Ä/b/icon.png', $this->object->getIcon());
    }

    /**
     * @test
     */
    public function testGetClassName()
    {
        $this->assertEquals('', $this->object->getClassName());
    }

    /**
     * @test
     */
    public function testGetMethod()
    {
        $this->assertNull($this->object->getMethod('No such method.'));
    }

    /**
     * @test
     */
    public function testGetMethods()
    {
        $value1 = new \Yana\Plugins\Configs\MethodConfiguration();
        $value1->setMethodName('Test 1');
        $value2 = new \Yana\Plugins\Configs\MethodConfiguration();
        $value2->setMethodName('Test 2');
        $expected = new \Yana\Plugins\Configs\MethodCollection();
        $expected[] = $value1;
        $expected[] = $value2;
        $this->assertEquals($expected, $this->object->addMethod($value1)->addMethod($value2)->getMethods());
    }

    /**
     * @test
     */
    public function testAddMethod()
    {
        $value = new \Yana\Plugins\Configs\MethodConfiguration();
        $value->setMethodName('Test');
        $this->assertSame($value, $this->object->addMethod($value)->getMethod('Test'));
    }

    /**
     * @test
     */
    public function testIsActiveByDefault()
    {
        $this->assertFalse($this->object->isActiveByDefault());
        $this->assertFalse($this->object->setActive(\Yana\Plugins\ActivityEnumeration::INACTIVE)->isActiveByDefault());
        $this->assertFalse($this->object->setActive(-1)->isActiveByDefault());
        $this->assertFalse($this->object->setActive(\Yana\Plugins\ActivityEnumeration::ACTIVE)->isActiveByDefault());
        $this->assertTrue($this->object->setActive(\Yana\Plugins\ActivityEnumeration::DEFAULT_ACTIVE)->isActiveByDefault());
    }

    /**
     * @test
     */
    public function testGetNamespace()
    {
        $this->assertSame('', $this->object->getNamespace());
    }

    /**
     * @test
     */
    public function testSetNamespace()
    {
        $this->assertSame(__NAMESPACE__, $this->object->setNamespace(__NAMESPACE__)->getNamespace());
    }

}

?>