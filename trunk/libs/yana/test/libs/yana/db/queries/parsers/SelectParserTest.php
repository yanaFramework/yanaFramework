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

namespace Yana\Db\Queries\Parsers;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../../include.php';

/**
 * @package  test
 */
class SelectParserTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var  \SQL_Parser
     */
    protected $parser;

    /**
     * @var \Yana\Db\Queries\Parsers\SelectParser
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if (!\class_exists('\SQL_Parser')) {
            $this->markTestSkipped("SQL parser class not found");
        }
        try {
            chdir(CWD . '../../');
            $db = new \Yana\Db\FileDb\Connection(\Yana\Files\XDDL::getDatabase('check'));
            $this->object = new \Yana\Db\Queries\Parsers\SelectParser($db);
            $this->parser = new \SQL_Parser();
        } catch (\Exception $e) {
            $this->markTestSkipped("Unable to connect to database");
        }
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
    public function testParseStatement()
    {
        $expectedResult = "SELECT * FROM ft WHERE ft.ftid = " . YANA_DB_DELIMITER . "1" . YANA_DB_DELIMITER . " ORDER BY ft.ftid DESC";
        $sqlStmt = "SELECT * FROM ft WHERE ft.ftid = '1' ORDER BY ftid DESC";
        $ast = $this->parser->parse($sqlStmt);
        $query = $this->object->parseStatement(\array_shift($ast));
        $this->assertTrue($query instanceof \Yana\Db\Queries\Select);
        $sqlResult = (string) $query;
        $this->assertEquals($expectedResult, $sqlResult);
    }

    /**
     * @test
     */
    public function testSelectTable()
    {
        $sqlStmt = 'SELECT * FROM ft';
        $ast = $this->parser->parse($sqlStmt);
        $query = $this->object->parseStatement(\array_shift($ast));
        $this->assertTrue($query instanceof \Yana\Db\Queries\Select);
        $sql2 = (string) $query;
        $this->assertEquals($sqlStmt, $sql2);
    }

    /**
     * @test
     */
    public function testSelectRow()
    {
        $sqlStmt = "SELECT * FROM ft WHERE ft.ftid = '2'";
        $expectedResult = "SELECT * FROM ft WHERE ft.ftid = " . YANA_DB_DELIMITER . "2" . YANA_DB_DELIMITER . "";
        $ast = $this->parser->parse($sqlStmt);
        $query = $this->object->parseStatement(\array_shift($ast));
        $this->assertTrue($query instanceof \Yana\Db\Queries\Select);
        $sql2 = (string) $query;
        $this->assertEquals($expectedResult, $sql2);
    }

    /**
     * @test
     */
    public function testSelectColumn()
    {
        $sqlStmt = "SELECT ftid FROM ft WHERE ftid = '2' ;";
        $expectedResult = "SELECT ft.ftid FROM ft WHERE ft.ftid = " . YANA_DB_DELIMITER . "2" . YANA_DB_DELIMITER . "";
        $ast = $this->parser->parse($sqlStmt);
        $query = $this->object->parseStatement(\array_shift($ast));
        $this->assertTrue($query instanceof \Yana\Db\Queries\Select);
        $sql2 = (string) $query;
        $this->assertEquals($expectedResult, $sql2);
    }

    /**
     * @test
     */
    public function testSelectColumn2()
    {        
        $sqlStmt = "SELECT ft.ftid FROM ft WHERE ft.ftid = '1' ORDER BY ftid";
        $expectedResult = "SELECT ft.ftid FROM ft WHERE ft.ftid = " . YANA_DB_DELIMITER . "1" . YANA_DB_DELIMITER . " ORDER BY ft.ftid";
        $ast = $this->parser->parse($sqlStmt);
        $query = $this->object->parseStatement(\array_shift($ast));
        $this->assertTrue($query instanceof \Yana\Db\Queries\Select);
        $sql2 = (string) $query;
        $this->assertEquals($expectedResult, $sql2);
    }

    /**
     * @test
     */
    public function testSelectMultipleColumns()
    {
        $sqlStmt = "SELECT ftid, ftvalue FROM ft WHERE ftid = '2' ;";
        $expectedResult = "SELECT ft.ftid, ft.ftvalue FROM ft WHERE ft.ftid = " . YANA_DB_DELIMITER . "2" . YANA_DB_DELIMITER . "";
        $ast = $this->parser->parse($sqlStmt);
        $query = $this->object->parseStatement(\array_shift($ast));
        $this->assertTrue($query instanceof \Yana\Db\Queries\Select);
        $sql2 = (string) $query;
        $this->assertEquals($expectedResult, $sql2);
    }

    /**
     * @test
     */
    public function testSelectJoin()
    {
        $sqlStmt = "SELECT ftid, ftvalue FROM ft join t on t.ftid = ft.ftid WHERE ftid = '2' ;";
        $expectedResult = "SELECT ft.ftid, ft.ftvalue FROM ft JOIN t ON ft.ftid = t.ftid WHERE ft.ftid = " . YANA_DB_DELIMITER . "2" . YANA_DB_DELIMITER;
        $ast = $this->parser->parse($sqlStmt);
        $query = $this->object->parseStatement(\array_shift($ast));
        $this->assertTrue($query instanceof \Yana\Db\Queries\Select);
        $sql2 = (string) $query;
        $this->assertEquals($expectedResult, $sql2);
    }

    /**
     * @test
     */
    public function testSelectJoinReversed()
    {
        $sqlStmt = "SELECT ftid, ftvalue FROM ft join t on ft.ftid = t.ftid;";
        $expectedResult = "SELECT ft.ftid, ft.ftvalue FROM ft JOIN t ON ft.ftid = t.ftid";
        $ast = $this->parser->parse($sqlStmt);
        $query = $this->object->parseStatement(\array_shift($ast));
        $this->assertTrue($query instanceof \Yana\Db\Queries\Select);
        $sql2 = (string) $query;
        $this->assertEquals($expectedResult, $sql2);
    }

    /**
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     * @test
     */
    public function testAccidentalCrossJoinInvalidArgumentException()
    {
        $sqlStmt = "SELECT * FROM ft join t on ft.ftid = ft.ftid;";
        $ast = $this->parser->parse($sqlStmt);
        $this->object->parseStatement(\array_shift($ast));
    }

    /**
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     * @test
     */
    public function testAccidentalCrossJoinInvalidArgumentException2()
    {
        $sqlStmt = "SELECT * FROM ft join t on t.ftid = t.ftid;";
        $ast = $this->parser->parse($sqlStmt);
        $this->object->parseStatement(\array_shift($ast));
    }

    /**
     * @test
     */
    public function testSelectMultipleJoins()
    {
        $sqlStmt = "SELECT * FROM ft join t on t.ftid = ft.ftid left join i on i.iid = t.tid ;";
        $expectedResult = "SELECT * FROM ft JOIN t ON ft.ftid = t.ftid LEFT JOIN i ON t.tid = i.iid";
        $ast = $this->parser->parse($sqlStmt);
        $query = $this->object->parseStatement(\array_shift($ast));
        $this->assertTrue($query instanceof \Yana\Db\Queries\Select);
        $sql2 = (string) $query;
        $this->assertEquals($expectedResult, $sql2);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testParseStatementInvalidArgumentException()
    {
        $this->object->parseStatement(array());
    }

    /**
     * @expectedException \Yana\Db\Queries\Exceptions\NotSupportedException
     * @test
     */
    public function testCrossJoinNotSupportedException()
    {
        $sqlStmt = "SELECT * FROM ft,t WHERE ft.ftid = '2'";
        $ast = $this->parser->parse($sqlStmt);
        $this->object->parseStatement(\array_shift($ast));
    }

    /**
     * @expectedException \Yana\Db\Queries\Exceptions\NotSupportedException
     * @test
     */
    public function testCrossJoinNotSupportedException2()
    {
        $sqlStmt = "SELECT * FROM ft CROSS JOIN t";
        $ast = $this->parser->parse($sqlStmt);
        $this->object->parseStatement(\array_shift($ast));
    }

    /**
     * @expectedException \Yana\Db\Queries\Exceptions\NotSupportedException
     * @test
     */
    public function testRightJoinNotSupportedException()
    {
        $sqlStmt = "SELECT * FROM ft RIGHT JOIN t ON t.ftid = ft.ftid";
        $ast = $this->parser->parse($sqlStmt);
        $this->object->parseStatement(\array_shift($ast));
    }

    /**
     * @expectedException \Yana\Db\Queries\Exceptions\NotSupportedException
     * @test
     */
    public function testJoinWithoutOnClauseNotSupportedException()
    {
        $sqlStmt = "SELECT * FROM ft, t";
        $ast = $this->parser->parse($sqlStmt);
        $this->object->parseStatement(\array_shift($ast));
    }

    /**
     * @test
     */
    public function testNaturalJoin()
    {
        $sqlStmt = "SELECT * FROM ft natural join t;";
        $expectedResult = "SELECT * FROM ft JOIN t WHERE ft.ftid = t.ftid";
        $ast = $this->parser->parse($sqlStmt);
        $query = $this->object->parseStatement(\array_shift($ast));
        $this->assertTrue($query instanceof \Yana\Db\Queries\Select);
        $sql2 = (string) $query;
        $this->assertEquals($expectedResult, $sql2);
    }

}
