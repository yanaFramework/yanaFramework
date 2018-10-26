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

namespace Yana\Db\Doctrine;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 * @ignore
 */
class MyDoctrineResult extends \Yana\Db\Doctrine\AbstractResult
{

    protected function _getResult()
    {
        return new \Yana\Db\Doctrine\MyDoctrineResultCommon();
    }

}

/**
 * @package  test
 * @ignore
 */
class MyDoctrineResultCommon
{

    public function rowCount()
    {
        return 1;
    }

    public function fetch()
    {
        return array('row');
    }

    public function fetchAll($mode)
    {
        return array(array('all'));
    }

    public function fetchColumn()
    {
        return array('col');
    }
}

/**
 * @package  test
 */
class ResultTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Doctrine\MyDoctrineResult
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Db\Doctrine\MyDoctrineResult();
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
    public function testCountRows()
    {
        $this->assertSame(1, $this->object->countRows());
    }

    /**
     * @test
     */
    public function testFetchRow()
    {
        $this->assertSame(array('row'), $this->object->fetchRow(0));
    }

    /**
     * @test
     */
    public function testFetchAll()
    {
        $this->assertSame(array(array('all')), $this->object->fetchAll());
    }

    /**
     * @test
     */
    public function testFetchColumn()
    {
        $this->assertSame(array('col'), $this->object->fetchColumn());
    }

    /**
     * @test
     */
    public function testFetchOne()
    {
        $this->assertSame('row', $this->object->fetchOne());
    }

}
