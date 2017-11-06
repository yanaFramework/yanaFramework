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
 * @package test
 */
class NullFacadeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Translations\NullFacade
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Translations\NullFacade();
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
    public function testGetLanguage()
    {
        $this->assertSame("", $this->object->getLanguage());
    }

    /**
     * @test
     */
    public function testGetCountry()
    {
        $this->assertSame("", $this->object->getCountry());
    }

    /**
     * @test
     */
    public function testGetLocale()
    {
        $this->assertSame("", $this->object->getLocale());
    }

    /**
     * @test
     */
    public function testReadFile()
    {
        $this->assertSame($this->object, $this->object->readFile("doesn't matter"));
    }

    /**
     * @test
     */
    public function testGetVar()
    {
        $this->assertSame("", $this->object->getVar("doesn't matter"));
    }

    /**
     * @test
     */
    public function testIsVar()
    {
        $this->assertFalse($this->object->isVar("doesn't matter"));
    }

    /**
     * @test
     */
    public function testGetLanguages()
    {
        $this->assertSame(array(), $this->object->getLanguages());
    }

    /**
     * @test
     */
    public function testAddDirectory()
    {
        $this->assertSame($this->object, $this->object->addDirectory("doesn't matter"));
    }

    /**
     * @test
     */
    public function testSetLocale()
    {
        $this->assertSame($this->object, $this->object->setLocale("doesn't matter"));
    }

    /**
     * @test
     */
    public function testGetMetaData()
    {
        $this->assertEquals(new \Yana\Core\MetaData\PackageMetaData(), $this->object->getMetaData("doesn't matter"));
    }

    /**
     * @test
     */
    public function testReplaceToken()
    {
        $this->assertSame("This should not change", $this->object->replaceToken("This should not change"));
    }

    /**
     * @test
     */
    public function testAddMetaDataProvider()
    {
        $this->assertSame($this->object, $this->object->addMetaDataProvider(new \Yana\Core\MetaData\NullDataProvider()));
    }

    /**
     * @test
     */
    public function testAddTextDataProvider()
    {
        $this->assertSame($this->object, $this->object->addTextDataProvider(new \Yana\Translations\TextData\NullDataProvider()));
    }

    /**
     * @test
     */
    public function testGetTranslations()
    {
        $this->assertEquals(new \Yana\Translations\TextData\TextContainer(), $this->object->getTranslations());
    }

    /**
     * @test
     */
    public function testLoadTranslations()
    {
        $this->assertSame($this->object, $this->object->loadTranslations("doesn't matter"));
    }

    /**
     * @test
     */
    public function testAttachLogger()
    {
        $this->assertNull($this->object->attachLogger(new \Yana\Log\NullLogger()));
    }

    /**
     * @test
     */
    public function testGetLogger()
    {
        $this->assertEquals(new \Yana\Log\NullLogger(), $this->object->getLogger());
    }

}
