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
class TextContainerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Translations\TextData\TextContainer
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Translations\TextData\TextContainer();
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
    public function testReplaceTokenEmpty()
    {
        $this->assertSame("This should not change", $this->object->replaceToken("This should not change"));
    }

    /**
     * @test
     */
    public function testReplaceToken()
    {
        $this->object->setVar('Token', 'test');
        $string = "a " . \YANA_LEFT_DELIMITER . 'lang id="token"' . \YANA_RIGHT_DELIMITER . " b";
        $this->assertSame("a test b", $this->object->replaceToken($string));
    }

    /**
     * @test
     */
    public function testIsLoaded()
    {
        $this->assertFalse($this->object->isLoaded('no-such-id'));
    }

    /**
     * @test
     */
    public function testSetLoaded()
    {
        $this->assertTrue($this->object->setLoaded('MyId')->isLoaded('mYiD'));
    }

    /**
     * @test
     */
    public function testAddVars()
    {
        $this->object->setVars(array('Test1' => __FUNCTION__))->addVars(array('Test2' => __FUNCTION__));
        $this->assertSame(array('test2' => __FUNCTION__, 'test1' => __FUNCTION__), $this->object->getVars());
    }

    /**
     * @test
     */
    public function testAddGroups()
    {
        $this->object->addGroups(array('Group' => array('group.0' => 'a', 'gRoup.1' => 'b')));
        $this->assertSame(array('group' => array('group.0' => 'a', 'group.1' => 'b')), $this->object->getGroups());
    }

    /**
     * @test
     */
    public function testIsGroup()
    {
        $this->assertFalse($this->object->isGroup('no-such-id'));
    }

    /**
     * @test
     */
    public function testGetGroups()
    {
        $this->assertSame(array(), $this->object->getGroups());
    }

    /**
     * @test
     */
    public function testGetGroupMembers()
    {
        $this->assertSame(array(), $this->object->getGroupMembers('no-such-group'));
    }

    /**
     * @test
     */
    public function testIsVar()
    {
        $this->assertFalse($this->object->isVar('no-such-id'));
    }

    /**
     * @test
     */
    public function testSetVarsByReference()
    {
        $this->assertSame(__FUNCTION__, $this->object->setVars(array('Test' => __FUNCTION__))->getVar('tesT'));
    }

    /**
     * @test
     */
    public function testGetVar()
    {
        $this->assertSame(__FUNCTION__, $this->object->setVar('Test', __FUNCTION__)->getVar('tesT'));
    }

    /**
     * @test
     */
    public function testGetVarGroup()
    {
        $this->object->addGroups(array('Group' => array('group.0' => 'a', 'gRoup.1' => 'b')))->setVars(array('group.0' => '1', 'gRoup.1' => '2'));
        $this->assertSame(array('a' => '1', 'b' => '2'), $this->object->getVar('grouP'));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Translations\NotFoundException
     */
    public function testGetVarNotFoundException()
    {
        $this->object->getVar('no-such-id');
    }

}
