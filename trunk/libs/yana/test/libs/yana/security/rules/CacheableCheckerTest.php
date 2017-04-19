<?php
/**
 * PHPUnit test-case
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

namespace Yana\Security\Rules;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * Test-case
 *
 * @package  test
 */
class CacheableCheckerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Security\Rules\CacheableChecker
     */
    protected $emptyChecker;

    /**
     * @var \Yana\Security\Rules\CacheableChecker
     */
    protected $filledChecker;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->emptyChecker = new \Yana\Security\Rules\CacheableChecker(new \Yana\Security\Rules\Requirements\NullReader());

        $adapter = new \Yana\Security\Rules\Requirements\NullReader(new \Yana\Security\Rules\Requirements\Requirement("group", "role", 0));
        $this->filledChecker = new \Yana\Security\Rules\CacheableChecker($adapter);
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
    public function testSetCache()
    {
        $cache = new \Yana\Data\Adapters\ArrayAdapter();
        $this->assertTrue($this->emptyChecker->setCache($cache) instanceof \Yana\Security\Rules\CacheableChecker, 'Instance of CacheableChecker expected');
    }

    /**
     * @test
     */
    public function testCheckRules()
    {
        $cache = new \Yana\Data\Adapters\ArrayAdapter();
        $this->emptyChecker->setCache($cache);

        $profileId = "test";
        $action = "test";
        $user = new \Yana\Security\Data\Users\Entity("test");

        $this->assertTrue($this->emptyChecker->checkRules($profileId, $action, $user), 'Excpecting to write TRUE to cache');

        $this->assertFalse($this->filledChecker->checkRules($profileId, $action, $user), 'Without cache the instance must return FALSE');

        $this->filledChecker->setCache($cache);
        $this->assertTrue($this->filledChecker->checkRules($profileId, $action, $user), 'With the same cache, this instance must now also return TRUE');
    }

}
