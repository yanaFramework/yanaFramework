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
 */
class ConnectionFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Doctrine\ConnectionFactory
     */
    protected $object;

    /**
     * @var array
     */
    private $_dsn = array(
        'DBMS' => 'mysqli',
        'HOST' => 'localhost',
        'PORT' => 3306,
        'USERNAME' => 'root',
        'PASSWORD' => '',
        'DATABASE' => 'test'
    );

    /**
     * @var bool
     */
    private $_isAvailable = null;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if (!$this->isAvailable()) {
            $this->markTestSkipped();
        }
        $this->object = new \Yana\Db\Doctrine\ConnectionFactory($this->_dsn);
    }

    /**
     * @return bool
     */
    protected function isAvailable()
    {
        if (!isset($this->_isAvailable)) {
            $factory = new \Yana\Db\Doctrine\ConnectionFactory();
            $this->_isAvailable = $factory->isAvailable($this->_dsn);
        }
        return $this->_isAvailable;
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
    public function testGetConnection()
    {
        $this->assertTrue($this->object->getConnection() instanceof \Doctrine\DBAL\Connection);
    }

    /**
     * @test
     */
    public function testGetDsn()
    {
        $this->assertEquals($this->_dsn, $this->object->getDsn());
    }

    /**
     * @test
     */
    public function testIsAvailable()
    {
        $this->assertTrue($this->object->isAvailable($this->_dsn));
        $this->assertFalse($this->object->isAvailable(array('USERNAME' => 'No such user (hopefully)')));
    }

}
