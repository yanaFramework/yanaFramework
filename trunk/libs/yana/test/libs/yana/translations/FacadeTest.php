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

namespace Yana\Translations;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../include.php';

/**
 * @ignore
 */
class MyFacade extends \Yana\Translations\Facade
{

    public function __construct()
    {
    }

    protected function _setSystemLocale(string $locale)
    {
    }

}

/**
 * @ignore
 */
class MyFacadeMockManager extends \Yana\Translations\MyFacade
{

    protected function _getManager(): \Yana\Translations\IsTranslationManager
    {
        return new \Yana\Translations\NullFacade();
    }

}

/**
 * @package test
 */
class FacadeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Translations\Facade
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Translations\MyFacade();
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
    public function test__get()
    {
        $this->assertEquals('test', $this->object->__get('test'));
    }

    /**
     * @test
     */
    public function testGetLanguage()
    {
        $this->assertEquals('', $this->object->getLanguage());
        $this->assertEquals('en', $this->object->setLocale('en', 'UK')->getLanguage());
    }

    /**
     * @test
     */
    public function testGetCountry()
    {
        $this->assertEquals('', $this->object->getCountry());
        $this->assertEquals('UK', $this->object->setLocale('en', 'UK')->getCountry());
    }

    /**
     * @test
     */
    public function testGetLocale()
    {
        $this->assertEquals('', $this->object->getLocale());
    }

    /**
     * @test
     */
    public function testReadFile()
    {
        $this->object = new \Yana\Translations\MyFacadeMockManager();
        $this->assertSame($this->object, $this->object->readFile('test'));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Translations\InvalidFileNameException
     */
    public function testReadFileInvalidFileNameException()
    {
        $this->object->readFile('');
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Translations\LanguageFileNotFoundException
     */
    public function testReadFileLanguageFileNotFoundException()
    {
        $this->object->readFile('test');
    }

    /**
     * @test
     */
    public function testGetVar()
    {
        $this->assertEquals('test', $this->object->getVar('test'));
    }

    /**
     * @test
     */
    public function testGetVars()
    {
        $this->assertEquals(array(), $this->object->getVars());
    }

    /**
     * @test
     */
    public function testIsVar()
    {
        $this->assertFalse($this->object->isVar('test'));
    }

    /**
     * @test
     */
    public function testGetLanguages()
    {
        $this->assertEquals(array(), $this->object->getLanguages());
    }

    /**
     * @test
     */
    public function testAddDirectory()
    {
        $this->assertEquals($this->object, $this->object->addDirectory(new \Yana\Files\Dir(__DIR__)));
    }

    /**
     * @test
     */
    public function testSetLocale()
    {
        $this->assertEquals('en-UK', $this->object->setLocale('en', 'UK')->getLocale());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function testGetInfoNotFoundException()
    {
        $this->object->getMetaData('empty');
    }

    /**
     * @test
     */
    public function testSerialize()
    {
        $this->assertInternalType('string', serialize($this->object));
    }

    /**
     * @test
     */
    public function testUnserialize()
    {
        $this->assertEquals($this->object, unserialize(serialize($this->object)));
    }

    /**
     * @test
     */
    public function testReplaceToken()
    {
        $test = 'test' . \YANA_LEFT_DELIMITER . 'lang id="test"' . \YANA_RIGHT_DELIMITER . 'test';
        $this->assertEquals('testtesttest', $this->object->replaceToken($test));
    }

    /**
     * @test
     */
    public function testAttachLogger()
    {
        $logger = new \Yana\Log\NullLogger();
        $collection = $this->object->attachLogger($logger)->getLogger();
        $this->assertEquals(1, count($collection));
    }

    /**
     * @test
     */
    public function testGetLogger()
    {
        $this->assertInstanceOf('\Yana\Log\IsLogHandler', $this->object->getLogger());
    }

}
