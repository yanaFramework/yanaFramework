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
class SelectExistParserTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var  \SQL_Parser
     */
    protected $parser;

    /**
     * @var  \Yana\Db\Queries\Parsers\SelectExistParser
     */
    protected $object;

    /**
     * @var  \Yana\Db\FileDb\Connection
     */
    protected $db;

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
            $this->db = new \Yana\Db\FileDb\Connection(\Yana\Files\XDDL::getDatabase('check'));
            $this->object = new \Yana\Db\Queries\Parsers\SelectExistParser($this->db);
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
        $sqlStmt = "SELECT 1 FROM ft WHERE ftid = '2'";
        $expectedResult = "SELECT 1 FROM ft WHERE ft.ftid = " . YANA_DB_DELIMITER . "2" . YANA_DB_DELIMITER;
        $ast = $this->parser->parse($sqlStmt);
        $query = $this->object->parseStatement(\array_shift($ast));
        $this->assertTrue($query instanceof \Yana\Db\Queries\SelectExist, "Parser error: $sqlStmt");
        $sqlResult = (string) $query;
        $this->assertEquals($expectedResult, $sqlResult, "Statement not resolved: $sqlResult");
    }

    /**
     * @test
     */
    public function testParseStatement1()
    {
        $sqlStmt = "SELECT 1 FROM dbo.ft WHERE ft.ftid = '2'";
        $expectedResult = "SELECT 1 FROM ft WHERE ft.ftid = " . YANA_DB_DELIMITER . "2" . YANA_DB_DELIMITER;
        $ast = $this->parser->parse($sqlStmt);
        $query = $this->object->parseStatement(\array_shift($ast));
        $this->assertTrue($query instanceof \Yana\Db\Queries\SelectExist, "Parser error: $sqlStmt");
        $sqlResult = (string) $query;
        $this->assertEquals($expectedResult, $sqlResult, "Statement not resolved: $sqlResult");
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
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testParseStatementInvalidArgumentException2()
    {
        $sqlStmt = "SELECT 1 FROM dbo.ft, dbo.ft WHERE ftid = ftid";
        $ast = $this->parser->parse($sqlStmt);
        $this->object->parseStatement(\array_shift($ast));
    }

}
