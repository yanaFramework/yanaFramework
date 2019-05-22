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

namespace Yana\Forms;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../include.php';

/**
 * @package  test
 */
class AbstractBuilderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Forms\NullBuilder
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Forms\NullBuilder();
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
    public function testGetFile()
    {
        $this->assertSame("", $this->object->getFile());
    }

    /**
     * @test
     */
    public function testGetId()
    {
        $this->assertSame("", $this->object->getId());
    }

    /**
     * @test
     */
    public function testSetId()
    {
        $this->assertSame("Test", $this->object->setId("Test")->getId());
    }

    /**
     * @test
     */
    public function testGetTable()
    {
        $this->assertSame("", $this->object->getTable());
    }

    /**
     * @test
     */
    public function testSetTable()
    {
        $this->assertSame("Test", $this->object->setTable("Test")->getTable());
    }

    /**
     * @test
     */
    public function testGetShow()
    {
        $this->assertSame(array(), $this->object->getShow());
    }

    /**
     * @test
     */
    public function testSetShow()
    {
        $this->assertSame(array(1, 2, 3), $this->object->setShow(array(1, 2, 3))->getShow());
    }

    /**
     * @test
     */
    public function testGetHide()
    {
        $this->assertSame(array(), $this->object->getHide());
    }

    /**
     * @test
     */
    public function testSetHide()
    {
        $this->assertSame(array(1, 2, 3), $this->object->setHide(array(1, 2, 3))->getHide());
    }

    /**
     * @test
     */
    public function testGetWhere()
    {
        $this->assertSame("", $this->object->getWhere());
    }

    /**
     * @test
     */
    public function testSetWhere()
    {
        $where = array('a', '=', 'b');
        $this->assertSame($where, $this->object->setWhere($where)->getWhere());
    }

    /**
     * @test
     */
    public function testGetSort()
    {
        $this->assertSame("", $this->object->getSort());
    }

    /**
     * @test
     */
    public function testSetSort()
    {
        $this->assertSame("Test", $this->object->setSort("Test")->getSort());
    }

    /**
     * @test
     */
    public function testIsDescending()
    {
        $this->assertFalse($this->object->isDescending());
    }

    /**
     * @test
     */
    public function testSetDescending()
    {
        $this->assertTrue($this->object->setDescending(true)->isDescending());
        $this->assertFalse($this->object->setDescending(false)->isDescending());
        $this->assertTrue($this->object->setDescending(true)->isDescending());
    }

    /**
     * @test
     */
    public function testGetPage()
    {
        $this->assertSame(0, $this->object->getPage());
    }

    /**
     * @test
     */
    public function testSetPage()
    {
        $this->assertSame(1, $this->object->setPage(1)->getPage());
    }

    /**
     * @test
     */
    public function testGetEntries()
    {
        $this->assertSame(20, $this->object->getEntries());
    }

    /**
     * @test
     */
    public function testSetEntries()
    {
        $this->assertSame(10, $this->object->setEntries(10)->getEntries());
    }

    /**
     * @test
     */
    public function testGetOninsert()
    {
        $this->assertSame("", $this->object->getOninsert());
    }

    /**
     * @test
     */
    public function testSetOninsert()
    {
        $this->assertSame(__FUNCTION__, $this->object->setOninsert(__FUNCTION__)->getOninsert());
    }

    /**
     * @test
     */
    public function testGetOnupdate()
    {
        $this->assertSame("", $this->object->getOnupdate());
    }

    /**
     * @test
     */
    public function testSetOnupdate()
    {
        $this->assertSame(__FUNCTION__, $this->object->setOnupdate(__FUNCTION__)->getOnupdate());
    }

    /**
     * @test
     */
    public function testGetOndelete()
    {
        $this->assertSame("", $this->object->getOndelete());
    }

    /**
     * @test
     */
    public function testSetOndelete()
    {
        $this->assertSame(__FUNCTION__, $this->object->setOndelete(__FUNCTION__)->getOndelete());
    }

    /**
     * @test
     */
    public function testGetOnsearch()
    {
        $this->assertSame("", $this->object->getOnsearch());
    }

    /**
     * @test
     */
    public function testSetOnsearch()
    {
        $this->assertSame(__FUNCTION__, $this->object->setOnsearch(__FUNCTION__)->getOnsearch());
    }

    /**
     * @test
     */
    public function testGetOndownload()
    {
        $this->assertSame("download_file", $this->object->getOndownload());
    }

    /**
     * @test
     */
    public function testSetOndownload()
    {
        $this->assertSame(__FUNCTION__, $this->object->setOndownload(__FUNCTION__)->getOndownload());
    }

    /**
     * @test
     */
    public function testGetOnexport()
    {
        $this->assertSame("", $this->object->getOnexport());
    }

    /**
     * @test
     */
    public function testSetOnexport()
    {
        $this->assertSame(__FUNCTION__, $this->object->setOnexport(__FUNCTION__)->getOnexport());
    }

    /**
     * @test
     */
    public function testGetLayout()
    {
        $this->assertNull($this->object->getLayout());
    }

    /**
     * @test
     */
    public function testSetLayout()
    {
        $this->assertSame(2, $this->object->setLayout(2)->getLayout());
    }

    /**
     * @test
     */
    public function testGetCache()
    {
        $this->assertTrue($this->object->getCache() instanceof \Yana\Data\Adapters\SessionAdapter);
    }

    /**
     * @test
     */
    public function testSetCache()
    {
        $adapter = new \Yana\Data\Adapters\ArrayAdapter();
        $this->assertSame($adapter, $this->object->setCache($adapter)->getCache());
    }

}
