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

namespace Yana\Db\Binaries\Uploads;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../../include.php';

/**
 * @package  test
 */
class MyUploader extends \Yana\Db\Binaries\Uploads\AbstractUploader
{

    public function getConfiguration()
    {
        return $this->_getConfiguration();
    }

    public function getTempName(\Yana\Http\Uploads\File $file)
    {
        return $this->_getTempName($file);
    }

}

/**
 * @package  test
 */
class AbstractUploaderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Binaries\Uploads\MyUploader
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Db\Binaries\Uploads\MyUploader();
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
    public function testGetConfiguration()
    {
        $this->assertTrue($this->object->getConfiguration() instanceof \Yana\Db\Binaries\ConfigurationSingleton);
    }

    /**
     * @test
     */
    public function testGetTempName()
    {
        $file = new \Yana\Http\Uploads\File('ignored', '', 'Test', 0, UPLOAD_ERR_OK);
        $this->assertSame('Test', $this->object->getTempName($file));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Files\SizeException
     */
    public function testGetTempNameInvalidSizeException()
    {
        $file = new \Yana\Http\Uploads\File('ignored', '', 'Test', 0, UPLOAD_ERR_INI_SIZE);
        $this->object->getTempName($file);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Files\NotWriteableException
     */
    public function testGetTempNameNotWriteableException()
    {
        $file = new \Yana\Http\Uploads\File('ignored', '', 'Test', 0, -10);
        $this->object->getTempName($file);
    }

}
