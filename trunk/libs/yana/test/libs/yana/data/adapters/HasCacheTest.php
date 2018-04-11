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

namespace Yana\Data\Adapters;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @ignore
 * @package  test
 */
class MyHasCache {
    use \Yana\Data\Adapters\HasCache;

    public function getCache()
    {
        return $this->_getCache();
    }
}

/**
 * @package  test
 */
class HasCacheTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Data\Adapters\MyHasCache
     */
    protected $_object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_object = new \Yana\Data\Adapters\MyHasCache();
    }

    /**
     * @test
     */
    public function testGetCache()
    {
        $this->assertTrue($this->_object->getCache() instanceof \Yana\Data\Adapters\ArrayAdapter);
    }

    /**
     * @test
     */
    public function testSetCache()
    {
        $cache = new \Yana\Data\Adapters\SessionAdapter();
        $this->assertSame($cache, $this->_object->setCache($cache)->getCache());
    }

}
