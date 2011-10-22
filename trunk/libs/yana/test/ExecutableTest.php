<?php
/**
 * PHPUnit test-case: Executable
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

/**
 * @ignore
 */
require_once dirname(__FILE__) . '/include.php';

/**
 * Test class for Executable
 *
 * @package  test
 */
class ExecutableTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    Executable
     * @access protected
     */
    protected $existingExecutable;

    /**
     * @var    Executable
     * @access protected
     */
    protected $nonExistingExecutable;

    /**
     * @var  string
     */
    protected $existingPath = 'resources/foo.php';

    /**
     * @var  string
     */
    protected $nonExistingPath = 'resources/bar.php';

    /**
     * @ignore
     */
    public function __construct()
    {
        assert('is_file(CWD . $this->existingPath);     // expecting $existingPath to be a valid file-path');
        assert('!is_file(CWD . $this->nonExistingPath); // expecting $existingPath to be NOT a valid file-path');
        assert('!class_exists("Foo");                   // expecting class "Foo" to be not loaded yet');
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->existingExecutable = new Executable(CWD . $this->existingPath);
        $this->nonExistingExecutable = new Executable(CWD . $this->nonExistingPath);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
        // intentionally left blank
    }

    /**
     * read php file
     *
     * @test
     */
    public function testRead()
    {
        // load existing PHP class
        $read = $this->existingExecutable->read();
        $isLoaded = class_exists("Foo");
        $this->assertTrue($read, 'read failed, file not found');
        $this->assertTrue($isLoaded, 'expected class "Foo" defined in executable file was not loaded');

        // test file not found
        $read = $this->nonExistingExecutable->read();
        $this->assertFalse($read, 'read failed, should return false on non-existing file');
    }

    /**
     * get content
     *
     * @test
     */
    public function testGetContent()
    {
        $exists = $this->existingExecutable->getContent();
        $this->assertType('string', $exists, 'expected function getContent() to return a string.');
        $regExp = preg_quote($this->existingExecutable->getPath(), '/');
        $this->assertRegExp('/' . $regExp . '/', $exists, 'expected function getContent() to report filename.');

        $notExists = $this->nonExistingExecutable->getContent();
        $this->assertType('string', $notExists, 'expected function getContent() to return a string.');
        $this->assertRegExp('/not exist/', $notExists, 'expected function getContent() to report that file does not exist.');
    }

    /**
     * to string
     *
     * @test
     */
    public function testToString()
    {
        $toString = (string) $this->existingExecutable;
        $this->assertType('string', $toString, 'expected function __toString() to return value of type string');        
    }

    /**
     * is empty
     *
     * @test
     */
    public function testIsEmpty()
    {
        // file exists
        $isEmpty = $this->nonExistingExecutable->isEmpty();
        $this->assertTrue($isEmpty, 'expected isEmpty() to return true on non-existing file');

        // file does NOT exist
        $isEmpty = $this->existingExecutable->isEmpty();
        $this->assertFalse($isEmpty, 'expected isEmpty() to return false on existing file');
    }
}
?>