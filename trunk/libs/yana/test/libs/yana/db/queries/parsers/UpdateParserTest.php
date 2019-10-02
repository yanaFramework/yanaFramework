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
class UpdateParserTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var  \SQL_Parser
     */
    protected $parser;

    /**
     * @var \Yana\Db\Queries\Parsers\UpdateParser
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
            $db = new \Yana\Db\FileDb\Connection(\Yana\Files\XDDL::getDatabase('check'));
            $this->object = new \Yana\Db\Queries\Parsers\UpdateParser($db);
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
        $sqlStmt = "UPDATE t SET tvalue='1' WHERE tid = '2'";
        $expectedResult = "UPDATE t SET t.tvalue = 1 WHERE t.tid = " . YANA_DB_DELIMITER . "2" . YANA_DB_DELIMITER;
        $ast = $this->parser->parse($sqlStmt);
        $query = $this->object->parseStatement(\array_shift($ast));
        $this->assertTrue($query instanceof \Yana\Db\Queries\Update);
        $this->assertEquals($expectedResult, (string) $query);
    }

    /**
     * @expectedException \Yana\Db\Queries\Exceptions\NotSupportedException
     * @test
     */
    public function testUpdateWithoutWhere()
    {
        $ast = $this->parser->parse("UPDATE t SET tvalue='1'");
        $this->object->parseStatement(\array_shift($ast));
    }

    /**
     * @expectedException \Yana\Db\Queries\Exceptions\ColumnNotFoundException
     * @test
     */
    public function testParseStatementColumnNotFoundException()
    {
        $ast = $this->parser->parse("UPDATE t SET a = 1 WHERE tvalue = 1");
        $this->object->parseStatement(\array_shift($ast));
    }

}
