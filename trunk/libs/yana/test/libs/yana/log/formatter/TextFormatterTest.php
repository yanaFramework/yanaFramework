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
class TextFormatterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Log\Formatter\TextFormatter
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Log\Formatter\TextFormatter();
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
        $this->assertSame("Yana Error: " . __METHOD__ . " in file 'test.php' on line 10.", $formattedString);
        $multipleOccurencesText = $this->object->format(\Yana\Log\TypeEnumeration::ERROR, __METHOD__, 'test.php', 10);
        $this->assertSame("\t... the previous error was reported multiple times.", $multipleOccurencesText);
        $finalText = $this->object->format(\Yana\Log\TypeEnumeration::ERROR, __METHOD__, 'test.php', 10);
        $this->assertSame("", $finalText);
    }

    /**
     * @test
     */
    public function testFormatAssertion()
    {
        $formattedString = $this->object->format(\Yana\Log\TypeEnumeration::ASSERT, __METHOD__, 'test.php', 10);
        $this->assertSame("Assertion failed: " . __METHOD__ . " in file 'test.php' on line 10.", $formattedString);
    }

    /**
     * @test
     */
    public function testFormatUnknown()
    {
        $formattedString = $this->object->format(-10, __METHOD__, 'test.php', 10);
        $this->assertSame("Unknown Error: " . __METHOD__ . " in file 'test.php' on line 10.", $formattedString);
    }

}
