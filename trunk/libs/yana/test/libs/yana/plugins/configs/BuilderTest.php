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
declare(strict_types=1);

namespace Yana\Plugins\Configs;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 * @ignore
 */
class MyBuilder extends \Yana\Plugins\Configs\Builder
{
    public function buildClass(): \Yana\Plugins\Configs\IsClassConfiguration
    {
        return parent::buildClass();
    }

    public function buildMethod()
    {
        return parent::buildMethod();
    }

}

/**
 * @package  test
 */
class BuilderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Plugins\Configs\MyBuilder
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Plugins\Configs\MyBuilder();
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
    public function testSetReflection()
    {
        $this->assertSame($this->object, $this->object->setReflection(new \Yana\Plugins\Annotations\ReflectionClass(__CLASS__)));
    }

    /**
     * @test
     */
    public function testSetAnnotationParser()
    {
        $this->assertSame($this->object, $this->object->setAnnotationParser(new \Yana\Plugins\Annotations\Parser()));
    }

    /**
     * @test
     */
    public function testBuildClass()
    {
        $this->assertEquals(new \Yana\Plugins\Configs\ClassConfiguration(), $this->object->buildClass());
    }

    /**
     * @test
     */
    public function testBuildMethod()
    {
        $this->assertNull($this->object->buildMethod());
    }

    /**
     * @test
     */
    public function testGetPluginConfigurationClass()
    {
        $filename = CWD . '/resources/testplugin.php';
        include_once $filename;
        $this->object->setReflection(new \Yana\Plugins\Annotations\ReflectionClass('TestPlugin'));

        $expected = new \Yana\Plugins\Configs\ClassConfiguration();
        $expected
                ->setClassName('TestPlugin')
                ->setDirectory('resources')
                ->setActive(2)
                ->setAuthors(array('Author 1', 'Author 2'))
                ->setTitles(array('A' => 'Title A', 'B' => 'Title B'))
                ->setTexts(array('A' => 'Translation A.', 'B' => 'Translation B.'))
                ->setDefaultTitle('Title')
                ->setDefaultText('Description.')
                ->setLastModified(\filemtime($filename))
                ->setType('primary')
                ->setGroup('my group')
                ->setLicense('Some License')
                ->setParent('my Parent')
                ->setDependencies(array('Dependency 1', 'Dependency 2'))
                ->setUrl('Some URL')
                ->setVersion('12.3 Beta');
        $menu1 = new \Yana\Plugins\Menus\Entry();
        $menu1
                ->setTitle('Menu Title')
                ->setGroup('groupname')
                ->setIcon('icon1.png');
        $expected->addMenu($menu1);
        $menu2 = new \Yana\Plugins\Menus\Entry();
        $menu2
                ->setTitle('{lang id="menu.title"}')
                ->setGroup('groupname.sub')
                ->setIcon('icon2.png');
        $expected->addMenu($menu2);

        $method = new \Yana\Plugins\Configs\MethodConfiguration();
        $method
                ->setClassName('TestPlugin')
                ->setMethodName('testA')
                ->addParam(new \Yana\Plugins\Configs\MethodParameter('a', 'string'))
                ->addParam((new \Yana\Plugins\Configs\MethodParameter('b', 'int'))->setDefault(123))
                ->setGroup($expected->getGroup()) // inherited
                ->setSafeMode(true)
                ->setTemplate($expected->getDirectory() . '/testplugin.php')
                ->setType('read')
                ->addPath($expected->getDirectory()) // inherited
                ->setReturn('array')
                ->setOverwrite(true)
                ->setSubscribe(true)
                ->setScripts(array($expected->getDirectory() . '/Script1.js', $expected->getDirectory() . '/Script2.js'))
                ->setStyles(array($expected->getDirectory() . '/Style1.css', $expected->getDirectory() . '/Style2.css'));
        $user = new \Yana\Plugins\Configs\UserPermissionRule();
        $user
                ->setGroup('my_Group')
                ->setRole('my_Role')
                ->setLevel(12);
        $method->addUserLevel($user);
        $menu3 = new \Yana\Plugins\Menus\Entry();
        $menu3
                ->setTitle('Menu Title 2')
                ->setGroup('groupname')
                ->setIcon('icon3.png');
        $method->setMenu($menu3);
        $onError = new \Yana\Plugins\Configs\EventRoute();
        $onError
                ->setMessage('Error')
                ->setTarget('error_action');
        $method->setOnError($onError);
        $onSuccess = new \Yana\Plugins\Configs\EventRoute();
        $onSuccess
                ->setMessage('Success')
                ->setTarget('success_action');
        $method->setOnSuccess($onSuccess);
        $expected->addMethod($method);

        $this->assertEquals($expected, $this->object->getPluginConfigurationClass());
    }

}
