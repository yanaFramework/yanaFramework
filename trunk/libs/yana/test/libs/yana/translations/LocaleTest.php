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
class LocaleTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Translations\Locale
     */
    protected $object;

    /**
     * @var \Yana\Translations\Locale
     */
    protected $initialized;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Translations\Locale();
        $this->initialized = new \Yana\Translations\Locale("Aa", "Bb");
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
    public function testSetLanguage()
    {
        $this->assertSame('te', $this->object->setLanguage('Te')->getLanguage());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testSetLanguageInvalidArgumentException()
    {
        $this->object->setLanguage('not a language');
    }

    /**
     * @test
     */
    public function testSetCountry()
    {
        $this->assertSame('TE', $this->object->setCountry('Te')->getCountry());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testSetCountryInvalidArgumentException()
    {
        $this->object->setCountry('not a country');
    }

    /**
     * @test
     */
    public function testToString()
    {
        $this->assertSame('la-CO', $this->object->setCountry('co')->setLanguage('la')->toString());
    }

    /**
     * @test
     */
    public function test__toString()
    {
        $this->assertSame('la-CO', (string) $this->object->setCountry('co')->setLanguage('la'));
    }

    /**
     * @test
     */
    public function testGetLanguage()
    {
        $this->assertSame("", $this->object->getLanguage());
        $this->assertSame("aa", $this->initialized->getLanguage());
    }

    /**
     * @test
     */
    public function testGetCountry()
    {
        $this->assertSame("", $this->object->getCountry());
        $this->assertSame("BB", $this->initialized->getCountry());
    }

    /**
     * @test
     */
    public function testEquals()
    {
        $locale1 = new \Yana\Translations\Locale('aa', 'BB');
        $locale2 = new \Yana\Translations\Locale('aa', 'BB');
        $locale3 = new \Yana\Translations\Locale('bb', 'aa');
        $this->assertTrue($locale1->equals($locale1));
        $this->assertTrue($locale1->equals($locale2));
        $this->assertTrue($locale2->equals($locale1));
        $this->assertFalse($locale3->equals($locale1));
        $this->assertFalse($locale3->equals($locale2));
        $this->assertFalse($locale1->equals($locale3));
        $this->assertFalse($locale2->equals($locale3));
    }

}
