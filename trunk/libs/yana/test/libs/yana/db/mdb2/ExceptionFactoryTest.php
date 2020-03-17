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

namespace Yana\Db\Mdb2;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class ExceptionFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Mdb2\ExceptionFactory
     */
    protected $object;

    /**
     * @return bool
     */
    protected function isAvailable()
    {
        $factory = new \Yana\Db\Mdb2\ConnectionFactory();
        return $factory->isAvailable($factory->getDsn());
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if (!\Yana\Db\Mdb2\ConnectionFactory::isMdb2Available()) {
            $this->markTestSkipped();
        }
        if (\version_compare(\phpversion(), '7.0.0') >= 0 && \version_compare(\MDB2::apiVersion(), '2.5.0b5') < 0) {
            $this->markTestSkipped('MDB2 version not compatible with PHP7.');
        }
        $this->object = new \Yana\Db\Mdb2\ExceptionFactory();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }

    /**
     * Data provider.
     *
     * @return  array
     */
    public function provider()
    {
        return array(
            array(-1, '\Yana\Db\DatabaseException'),
            array(-2, '\Yana\Db\Queries\Exceptions\InvalidSyntaxException'),
            array(-3, '\Yana\Db\Queries\Exceptions\ConstraintException'),
            array(-4, '\Yana\Db\DatabaseException'),
            array(-5, '\Yana\Db\Queries\Exceptions\DuplicateValueException'),
            array(-6, '\Yana\Db\Queries\Exceptions\NotSupportedException'),
            array(-7, '\Yana\Db\DatabaseException'),
            array(-8, '\Yana\Db\Queries\Exceptions\InvalidSyntaxException'),
            array(-9, '\Yana\Db\Queries\Exceptions\NotSupportedException'),
            array(-10, '\Yana\Db\Queries\Exceptions\NotFoundException'),
            array(-11, '\Yana\Db\Queries\Exceptions\InvalidSyntaxException'),
            array(-12, '\Yana\Db\DatabaseException'),
            array(-13, '\Yana\Db\Queries\Exceptions\QueryException'),
            array(-14, '\Yana\Db\ConnectionException'),
            array(-15, '\Yana\Db\Queries\Exceptions\QueryException'),
            array(-16, '\Yana\Db\Queries\Exceptions\QueryException'),
            array(-17, '\Yana\Db\Queries\Exceptions\QueryException'),
            array(-18, '\Yana\Db\Queries\Exceptions\TableNotFoundException'),
            array(-19, '\Yana\Db\Queries\Exceptions\ColumnNotFoundException'),
            array(-20, '\Yana\Db\DatabaseException'),
            array(-21, '\Yana\Db\DatabaseException'),
            array(-22, '\Yana\Db\Queries\Exceptions\InvalidSyntaxException'),
            array(-23, '\Yana\Db\DatabaseException'),
            array(-24, '\Yana\Db\ConnectionException'),
            array(-25, '\Yana\Db\DatabaseException'),
            array(-26, '\Yana\Db\Queries\Exceptions\DatabaseNotFoundException'),
            array(-27, '\Yana\Db\Queries\Exceptions\SecurityException'),
            array(-28, '\Yana\Db\Queries\Exceptions\QueryException'),
            array(-29, '\Yana\Db\Queries\Exceptions\ConstraintException'),
            array(-30, '\Yana\Db\DatabaseException'),
            array(-31, '\Yana\Db\Queries\Exceptions\QueryException'),
            array(-32, '\Yana\Db\DatabaseException'),
            array(-33, '\Yana\Db\DatabaseException'),
            array(-34, '\Yana\Db\DatabaseException'),
            array(-35, '\Yana\Db\DatabaseException'),
            array(-36, '\Yana\Db\Queries\Exceptions\SecurityException'),
            array(-37, '\Yana\Db\ConnectionException')
        );
    }

    /**
     * @test
     * @dataProvider provider
     */
    public function testToException(int $code, $expectedException)
    {
        $error = new \MDB2_Error($code);
        $exception = $this->object->toException($error);
        $this->assertInstanceOf($expectedException, $exception);
    }

}
