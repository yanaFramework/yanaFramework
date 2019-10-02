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
declare(strict_types=1);

namespace Yana\Db\Queries;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class JoinConditionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Queries\JoinCondition
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Db\Queries\JoinCondition(
            "joinedTable", "targetKey", "sourceTableName", "foreignKey", \Yana\Db\Queries\JoinTypeEnumeration::INNER_JOIN
        );
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
    public function testGetJoinedTableName()
    {
        $this->assertSame("joinedTable", $this->object->getJoinedTableName());
    }

    /**
     * @test
     */
    public function testGetTargetKey()
    {
        $this->assertSame("targetKey", $this->object->getTargetKey());
    }

    /**
     * @test
     */
    public function testGetSourceTableName()
    {
        $this->assertSame("sourceTableName", $this->object->getSourceTableName());
    }

    /**
     * @test
     */
    public function testGetForeignKey()
    {
        $this->assertSame("foreignKey", $this->object->getForeignKey());
    }

    /**
     * @test
     */
    public function testIsInnerJoin()
    {
        $inner = new \Yana\Db\Queries\JoinCondition("", "", "", "", \Yana\Db\Queries\JoinTypeEnumeration::INNER_JOIN);
        $left = new \Yana\Db\Queries\JoinCondition("", "", "", "", \Yana\Db\Queries\JoinTypeEnumeration::LEFT_JOIN);
        $natural = new \Yana\Db\Queries\JoinCondition("", "", "", "", \Yana\Db\Queries\JoinTypeEnumeration::NATURAL_JOIN);
        $this->assertTrue($inner->isInnerJoin());
        $this->assertFalse($left->isInnerJoin());
        $this->assertFalse($natural->isInnerJoin());
    }

    /**
     * @test
     */
    public function testIsLeftJoin()
    {
        $inner = new \Yana\Db\Queries\JoinCondition("", "", "", "", \Yana\Db\Queries\JoinTypeEnumeration::INNER_JOIN);
        $left = new \Yana\Db\Queries\JoinCondition("", "", "", "", \Yana\Db\Queries\JoinTypeEnumeration::LEFT_JOIN);
        $natural = new \Yana\Db\Queries\JoinCondition("", "", "", "", \Yana\Db\Queries\JoinTypeEnumeration::NATURAL_JOIN);
        $this->assertFalse($inner->isLeftJoin());
        $this->assertTrue($left->isLeftJoin());
        $this->assertFalse($natural->isLeftJoin());
    }

    /**
     * @test
     */
    public function testIsNaturalJoin()
    {
        $inner = new \Yana\Db\Queries\JoinCondition("", "", "", "", \Yana\Db\Queries\JoinTypeEnumeration::INNER_JOIN);
        $left = new \Yana\Db\Queries\JoinCondition("", "", "", "", \Yana\Db\Queries\JoinTypeEnumeration::LEFT_JOIN);
        $natural = new \Yana\Db\Queries\JoinCondition("", "", "", "", \Yana\Db\Queries\JoinTypeEnumeration::NATURAL_JOIN);
        $this->assertFalse($inner->isNaturalJoin());
        $this->assertFalse($left->isNaturalJoin());
        $this->assertTrue($natural->isNaturalJoin());
    }

}
