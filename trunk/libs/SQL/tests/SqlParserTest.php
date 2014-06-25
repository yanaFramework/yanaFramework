<?php
/**
 * @package SQL_Parser
 * @ignore
 */

require_once '../Parser.php';

/**
 * SQL-Parser test case
 *
 * @package SQL_Parser
 */
class SqlParserTest extends PHPUnit_Framework_TestCase {
    // contains the object handle of the parser class
    protected $parser;

    public function setUp() {
        $this->parser = new Sql_parser();
    }

    public function tearDown() {
        unset($this->parser);
    }

    private function _runTest($sql, $expect) {
        try {
            $result = $this->parser->parse($sql);
        } catch (ParserError $e) {
            $result = $e->getMessage();
            if (is_array($expect)) {
                $this->fail($result);
            }
        }
        $this->assertEquals($expect, $result);
    }

    public static function providerSelectStmts()
    {
        include 'select.php';
        return $tests;
    }

    /**
     * @dataProvider providerSelectStmts
     * @test
     */
    public function testSelect($sql, $expect) {
        $this->_runTest($sql, $expect);
    }

    public static function providerUpdateStmts()
    {
        include 'update.php';
        return $tests;
    }

    /**
     * @dataProvider providerUpdateStmts
     * @test
     */
    public function testUpdate($sql, $expect) {
        $this->_runTest($sql, $expect);
    }

    public static function providerInsertStmts()
    {
        include 'insert.php';
        return $tests;
    }

    /**
     * @dataProvider providerInsertStmts
     * @test
     */
    public function testInsert($sql, $expect) {
        $this->_runTest($sql, $expect);
    }

    public static function providerDeleteStmts()
    {
        include 'delete.php';
        return $tests;
    }

    /**
     * @dataProvider providerDeleteStmts
     * @test
     */
    public function testDelete($sql, $expect) {
        $this->_runTest($sql, $expect);
    }

    public static function providerDropStmts()
    {
        include 'drop.php';
        return $tests;
    }

    /**
     * @dataProvider providerDropStmts
     * @test
     */
    public function testDrop($sql, $expect) {
        $this->_runTest($sql, $expect);
    }

    public static function providerCreateStmts()
    {
        include 'create.php';
        return $tests;
    }

    /**
     * @dataProvider providerCreateStmts
     * @test
     */
    public function testCreate($sql, $expect) {
        $this->_runTest($sql, $expect);
    }
}
?>