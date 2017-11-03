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

namespace Yana\Log\Formatter;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class HtmlFormatterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Log\Formatter\HtmlFormatter
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Log\Formatter\HtmlFormatter();
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
    public function testFormat()
    {
        $formattedString = $this->object->format(\Yana\Log\TypeEnumeration::ERROR, __METHOD__, 'test.php', 10);
        $regExp = "/^<div style=[^>]+><pre><span style[^>]+>Yana Error<\/span>\s+description:\s+" .
                \preg_quote(__METHOD__, '/') . "\s+file:\s+test.php\s+line:\s+10<\/pre>.*?<\/div>$/s";
        $this->assertRegExp($regExp, $formattedString);
        $multipleOccurencesText = $this->object->format(\Yana\Log\TypeEnumeration::ERROR, __METHOD__, 'test.php', 10);
        $this->assertRegExp("/^<div style=[^>]+><pre>\t... the previous error was reported multiple times.<\/pre><\/div>$/", $multipleOccurencesText);
        $finalText = $this->object->format(\Yana\Log\TypeEnumeration::ERROR, __METHOD__, 'test.php', 10);
        $this->assertSame("", $finalText);
    }

    /**
     * @test
     */
    public function testFormatSameLineDifferentError()
    {
        $this->object->format(\Yana\Log\TypeEnumeration::ERROR, 'message1', 'test.php', 10);
        $formattedString = $this->object->format(\Yana\Log\TypeEnumeration::ERROR, 'message2', 'test.php', 10);
        $regExp = "/^<div style=[^>]+><pre>message2<\/pre>.*?<\/div>$/s";
        $this->assertRegExp($regExp, $formattedString);
    }

    /**
     * @test
     */
    public function testFormatAssertion()
    {
        $formattedString = $this->object->format(\Yana\Log\TypeEnumeration::ASSERT, __METHOD__, 'test.php', 10);
        $regExp = "/^<div style=[^>]+><pre><span style[^>]+>Assertion failed<\/span>\s+description:\s+" .
                \preg_quote(__METHOD__, '/') . "\s+file:\s+test.php\s+line:\s+10<\/pre>.*?<\/div>$/s";
        $this->assertRegExp($regExp, $formattedString);
    }

    /**
     * @test
     */
    public function testFormatUnknown()
    {
        $formattedString = $this->object->format(-10, __METHOD__, 'test.php', 10);
        $regExp = "/^<div style=[^>]+><pre><span style[^>]+>Unknown Error<\/span>\s+description:\s+" .
                \preg_quote(__METHOD__, '/') . "\s+file:\s+test.php\s+line:\s+10<\/pre>.*?<\/div>$/s";
        $this->assertRegExp($regExp, $formattedString);
    }

    /**
     * @test
     */
    public function testFormatBacktrace()
    {
        $formattedString = $this->object->format(\Yana\Log\TypeEnumeration::ERROR, __METHOD__, 'test.php', 10);
        $regExp = "/^<div style=[^>]+>.*<div><pre><span style=[^>]+>Backtrace<\/span>(\n\t[\w\\\\]+(->|::)\w+\([\w, ]*\))*<\/pre><\/div><\/div>$/s";
        $this->assertRegExp($regExp, $formattedString);
    }

    /**
     * @test
     */
    public function testFormatBacktraceTriggerError()
    {
        $backtrace = array(
            array(
                'file' => 'file.php',
                'line' => 123,
                'function' => 'trigger_error'
            )
        );
        $formattedString = $this->object->format(\Yana\Log\TypeEnumeration::ERROR, __METHOD__, 'test.php', 10, $backtrace);
        $this->assertContains("Error was raised in file 'file.php' on line 123", $formattedString);
    }

}
