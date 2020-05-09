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
class MyAbstractManager extends \Yana\Translations\AbstractManager
{
    public function loadTranslations(string $id)
    {
        return $this;
    }

    public function getAcceptedLocales(): \Yana\Translations\LocaleCollection
    {
        return parent::_getAcceptedLocales();
    }

    public function addAcceptedLocale(\Yana\Translations\IsLocale $locale): \Yana\Translations\IsTranslationManager
    {
        return $this;
    }

    public function getMetaDataProviders(): \Yana\Core\MetaData\DataProviderCollection
    { 
       return parent::_getMetaDataProviders();
    }

    public function getTextDataProviders(): \Yana\Translations\TextData\DataProviderCollection
    {
       return parent::_getTextDataProviders();
    }
}

/**
 * @package test
 */
class AbstractManagerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Translations\MyAbstractManager
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Translations\MyAbstractManager();
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
    public function test_getAcceptedLocales()
    {
        $this->assertEquals(new \Yana\Translations\LocaleCollection(), $this->object->getAcceptedLocales());
    }

    /**
     * @test
     */
    public function testAddMetaDataProvider()
    {
        $provider = new \Yana\Core\MetaData\NullDataProvider();
        $collection = new \Yana\Core\MetaData\DataProviderCollection();
        $collection[] = $provider;
        $this->assertEquals($collection, $this->object->addMetaDataProvider($provider)->getMetaDataProviders());
    }

    /**
     * @test
     */
    public function testAddTextDataProvider()
    {
        $provider = new \Yana\Translations\TextData\NullDataProvider();
        $collection = new \Yana\Translations\TextData\DataProviderCollection();
        $collection[] = $provider;
        $this->assertEquals($collection, $this->object->addTextDataProvider($provider)->getTextDataProviders());
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
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function testGetMetaDataNotFoundException()
    {
        $this->object->getMetaData("");
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function testGetMetaDataNotFoundException2()
    {
        $this->object->addMetaDataProvider(new \Yana\Core\MetaData\XmlDataProvider(new \Yana\Files\Dir(__DIR__)));
        $this->object->getMetaData("");
    }

    /**
     * @test
     */
    public function testGetMetaData()
    {
        $this->object->addMetaDataProvider(new \Yana\Translations\MetaData\XmlDataProvider(new \Yana\Files\Dir(CWD . '/resources/languages/')));
        $metaData = $this->object->getMetaData('en');
        $this->assertSame('English (US)', $metaData->getTitle());
    }

    /**
     * @test
     */
    public function testGetLanguagesEmpty()
    {
        $this->assertSame(array(), $this->object->getLanguages());
    }

    /**
     * @test
     */
    public function testGetLanguages()
    {
        $this->object->addMetaDataProvider(new \Yana\Translations\MetaData\XmlDataProvider(new \Yana\Files\Dir(CWD . '/resources/languages/')));
        $languages = $this->object->getLanguages();
        $this->assertSame(array('de' => 'Deutsch (Deutschland)', 'en' => 'English (US)'), $languages);
    }

    /**
     * @test
     */
    public function testGetLanguagesWithDataProvider()
    {
        $this->object->addMetaDataProvider(new \Yana\Core\MetaData\XmlDataProvider(new \Yana\Files\Dir(__DIR__)));
        $this->assertSame(array(), $this->object->getLanguages());
    }

    /**
     * @test
     */
    public function test__get()
    {
        $this->assertSame('noSuchVar', $this->object->noSuchVar);
        $this->assertSame('noSuchVar', $this->object->__get('noSuchVar'));
    }

    /**
     * @test
     */
    public function testIsVar()
    {
        $this->assertFalse($this->object->isVar('no-such-var'));
    }

    /**
     * @test
     */
    public function testGetVar()
    {
        $this->assertSame('no-such-var', $this->object->getVar('no-such-var'));
    }

    /**
     * @test
     */
    public function testGetVars()
    {
        $this->assertSame(array(), $this->object->getVars());
    }

    /**
     * @test
     */
    public function testReplaceToken()
    {
        $this->assertSame('Some string', $this->object->replaceToken('Some string'));
    }

    /**
     * @test
     */
    public function testAttachLogger()
    {
        $logger = new \Yana\Log\NullLogger();
        $collection = new \Yana\Log\LoggerCollection();
        $collection[] = $logger;
        $this->assertEquals($collection, $this->object->attachLogger($logger)->getLogger());
    }

    /**
     * @test
     */
    public function testGetLogger()
    {
        $this->assertEquals(new \Yana\Log\LoggerCollection(), $this->object->getLogger());
    }

}
