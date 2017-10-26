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

namespace Yana\Db\Binaries;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class FileNameCacheTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Binaries\FileNameCache
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $configuration = new \Yana\Db\Binaries\Configuration();
        $configuration->setFileNameCache(new \Yana\Data\Adapters\ArrayAdapter());
        $this->object = new \Yana\Db\Binaries\FileNameCache($configuration);
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
     * @expectedException \Yana\Core\Exceptions\Files\NotFoundException
     */
    public function testGetFilename()
    {
        $this->object->getFilename(1);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Files\NotFoundException
     */
    public function testStoreFilenameNotFoundException()
    {
        $this->object->storeFilename('');
    }

    /**
     * @test
     */
    public function testStoreFilename()
    {
        $id = $this->object->storeFilename(__FILE__);
        $this->assertSame(0, $id);
        $this->assertSame(__FILE__, $this->object->getFilename($id, true));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Files\NotFoundException
     */
    public function testGetFilenameThumbnailNotFoundException()
    {
        $id = $this->object->storeFilename(__FILE__);
        $this->assertSame(0, $id);
        $this->object->getFilename($id, false); // this file (obviously) is not an image and thus has no thumbnail
    }

}
