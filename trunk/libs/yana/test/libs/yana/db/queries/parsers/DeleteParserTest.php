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
class DeleteParserTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var  \SQL_Parser
     */
    protected $parser;

    /**
     * @var \Yana\Db\Queries\Parsers\DeleteParser
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
            $this->object = new \Yana\Db\Queries\Parsers\DeleteParser($db);
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
        $expectedResult = "DELETE FROM ft WHERE ft.ftvalue = " . \YANA_DB_DELIMITER . "0" . \YANA_DB_DELIMITER;
        $sqlStmt = "DELETE FROM ft WHERE ft.ftvalue = '0'";
        $ast = $this->parser->parse($sqlStmt);
        $query = $this->object->parseStatement(\array_shift($ast));
        $this->assertTrue($query instanceof \Yana\Db\Queries\Delete);
        $sqlResult = (string) $query;
        $this->assertEquals($expectedResult, $sqlResult);
    }

    /**
     * @test
     */
    public function testParseStatement2()
    {
        $expectedResult = "DELETE FROM ft WHERE " .
            "ft.ftvalue = " . \YANA_DB_DELIMITER . "1" . \YANA_DB_DELIMITER . " OR " .
            "ft.ftvalue = " . \YANA_DB_DELIMITER . "0" . \YANA_DB_DELIMITER;
        $sqlStmt = "DELETE FROM ft WHERE ftvalue = '1' or ftvalue = '0';";
        $ast = $this->parser->parse($sqlStmt);
        $query = $this->object->parseStatement(\array_shift($ast));
        $this->assertTrue($query instanceof \Yana\Db\Queries\Delete);
        $sqlResult = (string) $query;
        $this->assertEquals($expectedResult, $sqlResult);
    }

    /**
     * @test
     */
    public function testDeleteTable()
    {
        $expectedResult = "DELETE FROM ft";
        $sqlStmt = "DELETE FROM ft";
        $ast = $this->parser->parse($sqlStmt);
        $query = $this->object->parseStatement(\array_shift($ast));
        $this->assertTrue($query instanceof \Yana\Db\Queries\Delete);
        $sqlResult = (string) $query;
        $this->assertEquals($expectedResult, $sqlResult);
    }

    /**
     * The ORDER BY syntax is not supported by the parser.
     *
     * We include this test to keep an eye on whether this changes in future versions.
     *
     * @test
     */
    public function testDeleteWithOrderBy()
    {
        $sqlStmt = "DELETE FROM ft ORDER BY tvalue";
        $ast = $this->parser->parse($sqlStmt);
        $this->assertInternalType('string', $ast);
    }

    /**
     * The LIMIT syntax is not supported by the parser.
     *
     * We include this test to keep an eye on whether this changes in future versions.
     *
     * @test
     */
    public function testDeleteWithLimit()
    {
        $sqlStmt = "DELETE FROM ft LIMIT 1";
        $ast = $this->parser->parse($sqlStmt);
        $this->assertInternalType('string', $ast);
    }

}
