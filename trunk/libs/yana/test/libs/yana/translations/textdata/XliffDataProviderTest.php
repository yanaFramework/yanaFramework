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

namespace Yana\Translations\TextData;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class XliffDataProviderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Translations\TextData\XliffDataProvider
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $dir = new \Yana\Files\Dir(CWD . '/resources/languages/');
        $this->object = new \Yana\Translations\TextData\XliffDataProvider($dir);
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
        $locale = new \Yana\Translations\Locale('en');
        $varContainer = $this->object->loadOject('default', $locale);
        $this->assertTrue($varContainer->isVar('title'));
    }

    /**
     * @test
     */
    public function testLoadOjectWithCountryCode()
    {
        $locale = new \Yana\Translations\Locale('en', 'US');
        $varContainer = $this->object->loadOject('default', $locale);
        $this->assertTrue($varContainer->isVar('title'));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Translations\LanguageFileNotFoundException
     */
    public function testLoadOjectLanguageFileNotFoundException()
    {
        $locale = new \Yana\Translations\Locale('en');
        $this->object->loadOject('no-such-file', $locale);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Translations\LanguageFileNotFoundException
     */
    public function testLoadOjectLanguageFileNotFoundException2()
    {
        $locale = new \Yana\Translations\Locale('no');
        $this->object->loadOject('default', $locale);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Translations\InvalidSyntaxException
     */
    public function testLoadOjectLanguageInvalidSyntaxException()
    {
        $locale = new \Yana\Translations\Locale('en');
        $this->object->loadOject('invalid', $locale);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Translations\InvalidFileNameException
     */
    public function testLoadOjectLanguageInvalidFileNameException()
    {
        $locale = new \Yana\Translations\Locale('en');
        $this->object->loadOject('invalid file name', $locale);
    }

}
