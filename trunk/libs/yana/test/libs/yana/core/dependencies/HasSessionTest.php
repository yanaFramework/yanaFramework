<?php
/**
 * YANA library
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

namespace Yana\Core\Dependencies;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @ignore
 * @package  test
 */
class MyHasSession
{
    use \Yana\Core\Dependencies\HasSession;
}

/**
 * Test-case
 *
 * @package  test
 */
class HasSessionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Core\Dependencies\MyHasSession
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        restore_error_handler();
        $this->object = new \Yana\Core\Dependencies\MyHasSession();
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
    public function testGetSession()
    {
        $this->assertTrue($this->object->getSession() instanceof \Yana\Security\Sessions\IsWrapper);
        $this->assertTrue($this->object->getSession() instanceof \Yana\Security\Sessions\Wrapper);
    }

    /**
     * @test
     */
    public function testSetSession()
    {
        $object = new \Yana\Security\Sessions\NullWrapper();
        $this->assertSame($object, $this->object->setSession($object)->getSession());
    }

}
