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

namespace Yana\Db\Queries\Parsers;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../../include.php';

/**
 * @package  test
 */
class InsertParserTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var  \SQL_Parser
     */
    protected $parser;

    /**
     * @var \Yana\Db\Queries\Parsers\InsertParser
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
            $this->object = new \Yana\Db\Queries\Parsers\InsertParser($db);
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
        $expectedResult = "INSERT INTO t (tid, tvalue, ftid) VALUES ("
            . \YANA_DB_DELIMITER . "1" . \YANA_DB_DELIMITER . ", "
            . \YANA_DB_DELIMITER . "2" . \YANA_DB_DELIMITER . ", "
            . \YANA_DB_DELIMITER . "1" . \YANA_DB_DELIMITER . ")";
        $sqlStmt = "INSERT INTO t (tid, tvalue, ftid) VALUES ('1', '2', '1');";
        $ast = $this->parser->parse($sqlStmt);
        $query = $this->object->parseStatement(\array_shift($ast));
        $this->assertTrue($query instanceof \Yana\Db\Queries\Insert);
        $sqlResult = (string) $query;
        $this->assertEquals($expectedResult, $sqlResult);

        $expectedValues = array(
            'tid' => '1',
            'tvalue' => '2',
            'ftid' => '1'
        );
        $this->assertEquals($expectedValues, $query->getValues());
    }

    /**
     * @test
     */
    public function testInsertWithoutColumns()
    {
        $expectedResult = "INSERT INTO i (iid) VALUES (" . \YANA_DB_DELIMITER . "1" . \YANA_DB_DELIMITER . ")";
        $sqlStmt = "INSERT INTO i VALUES (1);";
        $ast = $this->parser->parse($sqlStmt);
        $query = $this->object->parseStatement(\array_shift($ast));
        $this->assertTrue($query instanceof \Yana\Db\Queries\Insert);
        $sqlResult = (string) $query;
        $this->assertEquals($expectedResult, $sqlResult);
    }

    /**
     * @test
     */
    public function testInsertWithArray()
    {
        $expectedResult = "INSERT INTO i (iid, ta) VALUES ("
            . \YANA_DB_DELIMITER . "1" . \YANA_DB_DELIMITER . ", "
            . \YANA_DB_DELIMITER . "[2]" . \YANA_DB_DELIMITER . ")";
        $sqlStmt = "INSERT INTO i VALUES (1, '[2]');";
        $ast = $this->parser->parse($sqlStmt);
        $query = $this->object->parseStatement(\array_shift($ast));
        $this->assertTrue($query instanceof \Yana\Db\Queries\Insert);
        $sqlResult = (string) $query;
        $this->assertEquals($expectedResult, $sqlResult);
    }

    /**
     * @test
     */
    public function testInsertWithNull()
    {
        $expectedResult = "INSERT INTO i (iid, ta) VALUES (" . \YANA_DB_DELIMITER . "1" . \YANA_DB_DELIMITER . ", NULL)";
        $sqlStmt = "INSERT INTO i VALUES (1, NULL);";
        $ast = $this->parser->parse($sqlStmt);
        $query = $this->object->parseStatement(\array_shift($ast));
        $this->assertTrue($query instanceof \Yana\Db\Queries\Insert);
        $sqlResult = (string) $query;
        $this->assertEquals($expectedResult, $sqlResult);
    }

    /**
     * The SET syntax is not supported by the parser.
     *
     * We include this test to keep an eye on whether this changes in future versions.
     *
     * @test
     */
    public function testInsertWithSet()
    {
        $sqlStmt = "INSERT INTO i SET iid = 1, ta = NULL;";
        $ast = $this->parser->parse($sqlStmt);
        $this->assertInternalType('string', $ast);
    }

    /**
     * The INSERT...SELECT syntax is not supported by the parser.
     *
     * We include this test to keep an eye on whether this changes in future versions.
     *
     * @test
     */
    public function testInsertWithSelect()
    {
        $sqlStmt = "INSERT INTO i SELECT * FROM t;";
        $ast = $this->parser->parse($sqlStmt);
        $this->assertInternalType('string', $ast);
    }

    /**
     * @test
     * @expectedException \Yana\Db\Queries\Exceptions\NotSupportedException
     */
    public function testInsertWithMultipleValueLists()
    {
        $ast = $this->parser->parse("INSERT INTO i VALUES (1), (2);");
        $this->object->parseStatement(\array_shift($ast));
    }

    /**
     * @expectedException \Yana\Db\Queries\Exceptions\ColumnNotFoundException
     * @test
     */
    public function testInsertIntoColumnNotFoundException()
    {
        // supposed to fail (due to missing primary key)
        $ast = $this->parser->parse("INSERT INTO ft(value) VALUES('2')");
        $this->object->parseStatement(\array_shift($ast));
    }

    /**
     * @expectedException Yana\Db\Queries\Exceptions\InvalidPrimaryKeyException
     * @test
     */
    public function testInsertIntoMissingPrimaryKey()
    {
        // supposed to fail (due to missing primary key)
        $ast = $this->parser->parse("INSERT INTO ft(ftvalue) VALUES('2')");
        $this->object->parseStatement(\array_shift($ast));
    }

    /**
     * @expectedException Yana\Core\Exceptions\Forms\MissingFieldException
     * @test
     */
    public function testSelectMissingForeignKeyInvalidException()
    {
        $ast = $this->parser->parse("INSERT INTO t (tid, tvalue) VALUES ('1', '2')");
        $this->object->parseStatement(\array_shift($ast));
    }

}
