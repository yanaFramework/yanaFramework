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
declare(strict_types=1);

namespace Yana\Translations;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../include.php';

/**
 * @package test
 * @ignore
 */
class MyManager extends \Yana\Translations\Manager
{
    public function getAcceptedLocales(): \Yana\Translations\LocaleCollection
    {
        return $this->_getAcceptedLocales();
    }
}
/**
 * @package test
 */
class ManagerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Translations\MyManager
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Translations\MyManager();
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
    public function testAddAcceptedLocale()
    {
        $locale = new \Yana\Translations\Locale();
        $collection = new \Yana\Translations\LocaleCollection();
        $collection[0] = $locale;
        $this->assertEquals($collection, $this->object->addAcceptedLocale($locale)->getAcceptedLocales());
        unset($collection[0]);
        $collection[1] = $locale;
        $this->assertEquals($collection, $this->object->addAcceptedLocale($locale)->getAcceptedLocales());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function testGetMetaDataNotFoundException()
    {
        $this->object->getMetaData('no-such-locale');
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Translations\InvalidFileNameException
     */
    public function testLoadTranslationsInvalidFileNameException()
    {
        $this->object->loadTranslations('invalid file name');
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Translations\LanguageFileNotFoundException
     */
    public function testLoadTranslationsLanguageFileNotFoundException()
    {
        $this->object->loadTranslations('default');
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Translations\LanguageFileNotFoundException
     */
    public function testLoadTranslationsLanguageFileNotFoundException2()
    {
        $dir = new \Yana\Files\Dir(CWD . '/resources/languages/');
        $this->object->addMetaDataProvider(new \Yana\Translations\MetaData\XmlDataProvider($dir));
        $this->object->addTextDataProvider(new \Yana\Translations\TextData\XliffDataProvider($dir));
        $this->object->addAcceptedLocale(new \Yana\Translations\Locale('en'));
        $this->object->loadTranslations('invalid');
    }

    /**
     * @test
     */
    public function testLoadTranslations()
    {
        $dir = new \Yana\Files\Dir(CWD . '/resources/languages/');
        $this->object->addMetaDataProvider(new \Yana\Translations\MetaData\XmlDataProvider($dir));
        $this->object->addTextDataProvider(new \Yana\Translations\TextData\XliffDataProvider($dir));
        $this->object->addAcceptedLocale(new \Yana\Translations\Locale('de'));
        $this->object->addAcceptedLocale(new \Yana\Translations\Locale('en'));
        $this->assertSame('title', $this->object->getVar('title'));
        $this->assertSame($this->object, $this->object->loadTranslations('default'));
        $this->assertSame('application', $this->object->getVar('title'));
    }

    /**
     * @test
     */
    public function testLoadTranslationsOther()
    {
        $dir = new \Yana\Files\Dir(CWD . '/resources/languages/');
        $this->object->addMetaDataProvider(new \Yana\Translations\MetaData\XmlDataProvider($dir));
        $this->object->addTextDataProvider(new \Yana\Translations\TextData\XliffDataProvider($dir));
        $this->object->addAcceptedLocale(new \Yana\Translations\Locale('de'));
        $this->object->addAcceptedLocale(new \Yana\Translations\Locale('en'));
        $this->assertSame('test', $this->object->getVar('test'));
        $this->assertSame($this->object, $this->object->loadTranslations('other'));
        $this->assertSame('other test', $this->object->getVar('test'));
    }

    /**
     * @test
     */
    public function testLoadTranslationsFallback()
    {
        $dir = new \Yana\Files\Dir(CWD . '/resources/languages/');
        $this->object->addMetaDataProvider(new \Yana\Translations\MetaData\XmlDataProvider($dir));
        $this->object->addTextDataProvider(new \Yana\Translations\TextData\XliffDataProvider($dir));
        $this->object->addAcceptedLocale(new \Yana\Translations\Locale('de'));
        $this->object->addAcceptedLocale(new \Yana\Translations\Locale('en'));
        $this->assertSame('fallback', $this->object->getVar('fallback'));
        $this->assertSame($this->object, $this->object->loadTranslations('default'));
        $this->assertSame('language fallback', $this->object->getVar('fallback'));
    }

    /**
     * @test
     */
    public function testLoadTranslationsFallback2()
    {
        $dir = new \Yana\Files\Dir(CWD . '/resources/languages/');
        $this->object->addMetaDataProvider(new \Yana\Translations\MetaData\XmlDataProvider($dir));
        $this->object->addTextDataProvider(new \Yana\Translations\TextData\XliffDataProvider($dir));
        $this->object->addAcceptedLocale(new \Yana\Translations\Locale('en'));
        $this->object->addAcceptedLocale(new \Yana\Translations\Locale('de'));
        $this->assertSame('fallback', $this->object->getVar('fallback'));
        $this->assertSame($this->object, $this->object->loadTranslations('default'));
        $this->assertSame('language fallback', $this->object->getVar('fallback'));
    }

}
