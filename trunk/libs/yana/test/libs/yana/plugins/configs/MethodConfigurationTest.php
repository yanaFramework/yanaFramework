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
 * @package test
 * @ignore
 */
class MyTestPlugin extends \Yana\Core\Object implements \Yana\IsPlugin
{

    public function __construct()
    {
        // intentionally left blank
    }

    public function catchAll($event, array $ARGS)
    {
        return 67890;
    }

    public function test()
    {
        return 12345;
    }
}

/**
 * @package  test
 */
class MethodConfigurationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var PluginConfigurationMethod
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Plugins\Configs\MethodConfiguration();
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
    public function testType()
    {
        $this->assertEquals(\Yana\Plugins\TypeEnumeration::DEFAULT_SETTING, $this->object->getType());
        $this->object->setType('invalid');
        $this->assertEquals(\Yana\Plugins\TypeEnumeration::DEFAULT_SETTING, $this->object->getType());
        $this->object->setType(\Yana\Plugins\TypeEnumeration::LIBRARY);
        $this->assertEquals(\Yana\Plugins\TypeEnumeration::LIBRARY, $this->object->getType());
    }

    /**
     * @test
     */
    public function testPath()
    {
        $this->assertEquals('', $this->object->getPath());
        $this->assertEquals(array(), $this->object->getPaths());
        $this->object->addPath('path1');
        $this->object->addPath('path2');
        $this->assertEquals('path1', $this->object->getPath());
        $this->assertEquals(array('path1', 'path2'), $this->object->getPaths());
    }

    /**
     * @test
     */
    public function testAddSubscription()
    {
        $this->object
                ->addPath('Own Path @üÄ')
                ->setScripts(array('4.js', '5.js', 'a' => '6.js', 'b' => '7.js'))
                ->setLanguages(array('a', 'b', 'a' => 'c', 'b' => 'd'))
                ->setStyles(array('4.css', '5.css', 'a' => '6.css', 'b' => '7.css'));
        $method = new \Yana\Plugins\Configs\MethodConfiguration();
        $method
                ->addPath('Added Path @üÄ')
                ->addPath('This must not be found')
                ->setScripts(array('1.js', '2.js', 'a' => '3.js', 'c' => '8.js'))
                ->setLanguages(array('d', 'e', 'a' => 'f', 'c' => 'g'))
                ->setStyles(array('1.css', '2.css', 'a' => '3.css', 'c' => '8.css'));
        $this->assertSame('Own Path @üÄ', $this->object->addSubscription($method)->getPath());
        $this->assertSame(array('Own Path @üÄ', 'Added Path @üÄ'), $this->object->getPaths());
        $this->assertSame(array('4.js', '5.js', 'a' => '3.js', 'b' => '7.js', '1.js', '2.js', 'c' => '8.js'), $this->object->getScripts());
        $this->assertSame(array('a', 'b', 'a' => 'f', 'b' => 'd', 'd', 'e', 'c' => 'g'), $this->object->getLanguages());
        $this->assertSame(array('4.css', '5.css', 'a' => '3.css', 'b' => '7.css', '1.css', '2.css', 'c' => '8.css'), $this->object->getStyles());
    }

    /**
     * @test
     */
    public function testScripts()
    {
        $this->assertEquals(array(), $this->object->getScripts());
        $this->object->setScripts(array('script1.js', 'script2.js'));
        $this->assertEquals(array('script1.js', 'script2.js'), $this->object->getScripts());
    }

    /**
     * @test
     */
    public function testStyles()
    {
        $this->assertEquals(array(), $this->object->getStyles());
        $this->object->setStyles(array('style1.css', 'style2.css'));
        $this->assertEquals(array('style1.css', 'style2.css'), $this->object->getStyles());
    }

    /**
     * @test
     */
    public function testLanguages()
    {
        $this->assertEquals(array(), $this->object->getLanguages());
        $this->object->setLanguages(array('language1', 'language2'));
        $this->assertEquals(array('language1', 'language2'), $this->object->getLanguages());
    }

    /**
     * @test
     */
    public function testGetParams()
    {
        $this->assertEquals(array(), $this->object->getParams());
    }

    /**
     * @test
     */
    public function testSetParams()
    {
        $params = array('p1' => 'int', 'p2' => 'string');
        $this->object->setParams($params);
        $this->assertEquals($params, $this->object->getParams());
    }

    /**
     * @test
     */
    public function testReturn()
    {
        $this->assertEquals('', $this->object->getReturn());
        $this->object->setReturn('String');
        $this->assertEquals('String', $this->object->getReturn());
    }

    /**
     * @test
     */
    public function testGroup()
    {
        $this->assertEquals('', $this->object->getGroup());
        $this->object->setGroup('group');
        $this->assertEquals('group', $this->object->getGroup());
    }

    /**
     * @test
     */
    public function testGetMenu()
    {
        $this->assertEquals(null, $this->object->getMenu());
    }

    /**
     * @test
     */
    public function testSetMenu()
    {
        $menu = new \Yana\Plugins\Menus\Entry();
        $this->assertSame($menu, $this->object->setMenu($menu)->getMenu());
    }

    /**
     * @test
     */
    public function testGetOnSuccess()
    {
        $this->assertNull($this->object->getOnSuccess());
    }

    /**
     * @test
     */
    public function testSetOnSuccess()
    {
        $expected = new \Yana\Plugins\Configs\EventRoute();
        $expected->setMessage('message');
        $expected->setTarget('target');
        $this->object->setOnSuccess($expected);
        $this->assertEquals($expected, $this->object->getOnSuccess());
        $this->assertEquals(\Yana\Plugins\Configs\ReturnCodeEnumeration::SUCCESS, $expected->getCode());
    }

    /**
     * @test
     */
    public function testGetOnError()
    {
        $this->assertNull($this->object->getOnError());
    }

    /**
     * @test
     */
    public function testSetOnError()
    {
        $expected = new \Yana\Plugins\Configs\EventRoute();
        $expected->setMessage('message');
        $expected->setTarget('target');
        $this->object->setOnError($expected);
        $this->assertEquals($expected, $this->object->getOnError());
        $this->assertEquals(\Yana\Plugins\Configs\ReturnCodeEnumeration::ERROR, $expected->getCode());
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
    public function testSetTitle()
    {
        $this->assertSame('Title @Üäß', $this->object->setTitle('Title @Üäß')->getTitle());
    }

    /**
     * @test
     */
    public function testGetSafeMode()
    {
        $this->assertNull($this->object->getSafeMode());
    }

    /**
     * @test
     */
    public function testSetSafeMode()
    {
        $this->object->setSafeMode(true);
        $this->assertTrue($this->object->getSafeMode());
        $this->object->setSafeMode(false);
        $this->assertFalse($this->object->getSafeMode());
        $this->object->setSafeMode('yes');
        $this->assertTrue($this->object->getSafeMode());
        $this->object->setSafeMode('no');
        $this->assertFalse($this->object->getSafeMode());
        $this->object->setSafeMode(null);
        $this->assertNull($this->object->getSafeMode());
    }

    /**
     * @test
     */
    public function testGetTemplate()
    {
        $this->assertEquals('', $this->object->getTemplate());
    }

    /**
     * @test
     */
    public function testSetTemplate()
    {
        $this->object->setTemplate('test');
        $this->assertEquals('test', $this->object->getTemplate());
    }

    /**
     * @test
     */
    public function testGetUserLevels()
    {
        $this->assertEquals(array(), $this->object->getUserLevels());
    }

    /**
     * @test
     */
    public function testSetUserLevels()
    {
        $levelA = new \Yana\Plugins\Configs\UserPermissionRule();
        $levelB = new \Yana\Plugins\Configs\UserPermissionRule();
        $levelA->setGroup('A')->setLevel(75);
        $levelB->setRole('B')->setLevel(50);
        $users = array($levelA, $levelB);
        $this->object->setUserLevels($users);
        $this->assertEquals($users, $this->object->getUserLevels());
    }

    /**
     * @test
     */
    public function testGetOverwrite()
    {
        $this->assertFalse($this->object->getOverwrite());
    }

    /**
     * @test
     */
    public function testSetOverwrite()
    {
        $this->object->setOverwrite(true);
        $this->assertTrue($this->object->getOverwrite());
    }

    /**
     * @test
     */
    public function testGetSubscribe()
    {
        $this->assertFalse($this->object->getSubscribe());
    }

    /**
     * @test
     */
    public function testSetSubscribe()
    {
        $this->object->setSubscribe(true);
        $this->assertTrue($this->object->getSubscribe());
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
    public function testSetClassName()
    {
        $this->object->setClassName(__CLASS__);
        $this->assertEquals(__CLASS__, $this->object->getClassName());
    }

    /**
     * @test
     */
    public function testGetMethodName()
    {
        $this->assertEquals('', $this->object->getMethodName());
    }

    /**
     * @test
     */
    public function testSetMethodName()
    {
        $this->object->setMethodName(__METHOD__);
        $this->assertEquals(__METHOD__, $this->object->getMethodName());
    }

    /**
     * @return  array
     */
    public function provider()
    {
        return array(
            array('a', 'array', null, null),
            array('a', 'array', null, array('test')),
            array('i', 'int', null, null),
            array('i', 'int', null, 1),
            array('f', 'float', null, null),
            array('f', 'float', null, 1.2),
            array('b', 'bool', null, null),
            array('b', 'bool', null, true),
            array('s', 'string', null, null),
            array('s', 'string', null, "test"),
            array('n', 'string', "", null)
        );
    }

    /**
     * @test
     * @dataProvider  provider
     * @param  string  $name
     * @param  string  $type
     * @param  mixed   $default
     * @param  mixed   $value
     */
    public function testSetEventArguments($name, $type, $default, $value)
    {
        $this->object->addParam($name, $type, $default);
        try {
            $this->assertSame(array($name => \is_null($value) ? $default : $value), $this->object->setEventArguments(array($name => $value)));
        } catch (\Yana\Core\Exceptions\Forms\InvalidValueException $e) {
            if (!\is_null($value) || !\is_null($default)) {
                throw $e;
            }
        }
    }

    /**
     * @test
     */
    public function testAddParam()
    {
        $this->assertSame(array("test" => "type"), $this->object->addParam("test", "type", "default")->getParams());
        $this->assertSame(array("default"), $this->object->getDefaults());
    }

    /**
     * @test
     */
    public function testGetDefaults()
    {
        $this->assertEquals(array(), $this->object->getDefaults());
    }

    /**
     * @test
     */
    public function testSetDefaults()
    {
        $defaults = array(2 => 1, 3 => '2', 4=> 3);
        $this->object->setDefaults($defaults);
        $this->assertEquals($defaults, $this->object->getDefaults());
    }

    /**
     * @test
     */
    public function testSetHasGenericParams()
    {
        $this->assertFalse($this->object->hasGenericParams());
        $this->object->setHasGenericParams(true);
        $this->assertTrue($this->object->hasGenericParams());
    }

    /**
     * @test
     */
    public function testSendEvent()
    {
        $plugin = new \Yana\Plugins\Configs\MyTestPlugin();
        $this->assertSame($plugin->catchAll('', array()), $this->object->sendEvent($plugin));
        $this->assertSame($plugin->test(), $this->object->setMethodName('test')->sendEvent($plugin));
    }

    /**
     * @test
     */
    public function testHasMethod()
    {
        $instance = new \Yana\Plugins\Configs\MyTestPlugin();
        $this->object->setMethodName('no such method');
        $this->assertFalse($this->object->hasMethod($instance));
        $this->object->setMethodName('test');
        $this->assertTrue($this->object->hasMethod($instance));
        $this->object->setMethodName('catchAll');
        $this->assertTrue($this->object->hasMethod($instance));
    }

}

?>