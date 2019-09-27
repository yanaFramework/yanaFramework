<?php
/**
 * YANA library
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
 * @package  yana
 * @license  http://www.gnu.org/licenses/gpl.txt
 */

namespace Plugins\DbTools;

/**
 * MDB2 import class
 *
 * This class allows you to read MDB2 schema files.
 *
 * Use DbMDB2->getTableInfo(), or DbMDB2->getStructure() to import the file.
 *
 * Example:
 * <code>
 * $schema = new \Plugins\DbTools\MDB2('schema.xml');
 * $structure = $schema->getStructure();
 * $structure->write();
 * </code>
 *
 * Once you have a valid structure file, you can open and query it easily, by using the
 * functions provided by DbStructure.
 *
 * Note: this class currently relies on version v 1.9 2006/10/21 of MDB2 XML Schema.
 * As this version is still beta, this class also cannot be a final version.
 * If you find any problems with this implementation report them to me.
 *
 * Also note: while this class allows you to import the tags "function" and "expression",
 * which are used to embed function calls and maths etc., you are strongly recommended to
 * avoid these elements.
 *
 * The reason is simple: They break compatibility with FileDb, the database-simulator of
 * the framework, which allows you to run simulated, file-based databases without the need for
 * ANY database server - not even SQLite, which works only with PHP5. "Expressions" and "functions"
 * cannot be simulated and you may need a "real" DBMS to get your script up and running.
 *
 * Also these elements are not understood by the query parser, which is also used by FileDb.
 * So you may not be able to auto-validate such statements, or auto-install the table via the
 * "database admin" plugIn.
 *
 * If you really need to use such statements, you may still write your own SQL file with a
 * translation for any SQL dialect you wish to support and place it in the corresponding sub-directory
 * of directory "config/db/.install/".
 *
 * @package    yana
 * @subpackage plugins
 */
class MDB2 extends \Yana\Files\File implements \Plugins\DbTools\IsImport
{

    /**
     * @var string
     */
    private $name = "";

    /**
     * @var array
     */
    private $info = array();

    /**
     * @var array
     */
    private $currentTable = array('columns' => array());

    /**
     * @var array
     */
    private $currentColumn = array();

    /**
     * @var string
     */
    private $currentName = "";

    /**
     * @var string
     */
    private $xPath = "";

    /**
     * Return table info for current data
     *
     * @param   string  $table  table name
     * @return  array
     */
    public function getTableInfo($table = null)
    {
        if (empty($this->info)) {
            $parser = xml_parser_create();
            xml_set_element_handler($parser, array(&$this, "_startElement"), array(&$this, "_endElement"));
            xml_set_character_data_handler($parser, array(&$this, "_characterData"));

            if (!xml_parse($parser, $this->getContent())) {
                $message = "XML error: " . xml_error_string(xml_get_error_code($parser)) . "\n\t\t" .
                    " in file '" . $this->getPath() . "' at line " . xml_get_current_line_number($parser);
                $level = \Yana\Log\TypeEnumeration::WARNING;
                \Yana\Log\LogManager::getLogger()->addLog($message, $level);
                return false;
            }
            xml_parser_free($parser);
        }

        if (is_null($table)) {
            $array = array();
            foreach ($this->info as $info)
            {
                $array[] = $info->toArray();
            }
            return $array;

        } elseif (isset($this->info[$table])) {
            return $this->info[$table]->toArray();

        } else {
            return false;
        }
    }

    /**
     * import MDB2 schema to Yana structure files
     *
     * The argument $mdb2Schema may either be a file name or XML file content.
     *
     * This function will import the database structure from the given file and
     * transform it into a compatible structure file, that can be used to create
     * and modify databases via the framework's database API.
     *
     * The function returns an instance of class DbStructure, or bool(false)
     * on error.
     *
     * @access  public
     * @static
     * @param   string   $mdb2Schema    file name or XML file content
     * @return  \Yana\Db\Structure
     */
    public static function getStructureFromString($mdb2Schema)
    {
        assert('is_string($mdb2Schema); // Wrong argument type $dbDesignerConfig. String expected.');
        if (is_file($mdb2Schema)) {
            $MDB2 = new \Plugins\DbTools\MDB2($mdb2Schema);
            $MDB2->read();
        } else {
            $MDB2 = new \Plugins\DbTools\MDB2('');
            assert('empty($MDB2->content);');
            $MDB2->content = explode("\n", $mdb2Schema);
            assert('is_array($MDB2->content);');
        }
        $structure = $MDB2->getStructure();
        if (is_object($structure)) {
            $structure->dropChangelog();
            return $structure;
        } else {
            return false;
        }
    }

    /**
     * Return database structure for current data
     *
     * @return  \Yana\Db\Structure
     */
    public function &getStructure()
    {
        $structure = new \Yana\Db\Structure("");
        foreach ($this->getTableInfo() as $tableInfo)
        {
            $structure->addStructure($tableInfo['name'], $tableInfo);
        }
        return $structure;
    }

    /**
     * _startElement
     *
     * @param   int     $parser   parser
     * @param   string  $name     name
     * @param   array   $attrs    attributes
     * @return  bool
     * @ignore
     */
    function _startElement($parser, $name, array $attrs)
    {
        $this->xPath .= '/' . $name;
        $this->currentName = mb_strtolower($name);
    }

    /**
     * _endElement
     *
     * @param   int     $parser   parser
     * @param   string  $name     name
     * @return  bool
     * @ignore
     */
    function _endElement($parser, $name)
    {
        $name = mb_strtolower($name);
        switch (true)
        {
            case $name === 'table':
                $table = new \Plugins\DbTools\InfoTable($this->currentTable['name']);
                foreach ($this->currentTable['columns'] as $column)
                {
                    $table->addColumn($column);
                }
                if (!$table->getPrimaryKey() && isset($this->currentTable['primary'])) {
                    $table->setPrimaryKey($this->currentTable['primary']);
                }
                if (!empty($this->currentTable['init'])) {
                    $table->setInit($this->currentTable['init']);
                }
                /* add current table */
                $this->info[$this->currentTable['name']] = $table;
                /* reset current table */
                $this->currentTable = array('columns' => array());
            break;
            case $name === 'field' && $this->_xPath('//table/declaration/field'):
                if (!empty($this->currentColumn['name'])) {
                    $column = new \Plugins\DbTools\InfoColumn($this->currentColumn['name']);
                    if (!empty($this->currentColumn['type'])) {
                        $column->setType($this->currentColumn['type']);
                    }
                    if (empty($this->currentColumn['notnull'])) {
                        $column->setNullable(true);
                    } elseif (strcasecmp($this->currentColumn['notnull'], 'false') === 0 ) {
                        $column->setNullable(true);
                    } else {
                        $column->setNullable(false);
                    }
                    $column->setPrimaryKey(false);
                    $column->setUnique(false);
                    $column->setIndex(false);
                    if (!empty($this->currentColumn['autoincrement'])) {
                        $column->setAuto(true);
                    } elseif (isset($this->currentColumn['autoincrement']) &&
                        strcasecmp($this->currentColumn['autoincrement'], 'true') === 0) {
                        $column->setAuto(true);
                    } else {
                        $column->setAuto(false);
                    }
                    if (isset($this->currentColumn['default'])) {
                        $column->setDefault($this->currentColumn['default']);
                    }
                    if (isset($this->currentColumn['unsigned'])) {
                        $column->setUnsigned($this->currentColumn['unsigned']);
                    }
                    $column->setZerofill(null);
                    if (isset($this->currentColumn['comments'])) {
                        $column->setComment($this->currentColumn['comments']);
                    }
                    if (isset($this->currentColumn['length'])) {
                        $column->setLength((int) $this->currentColumn['length']);
                    }
                    $column->setUpdate(true);
                    $column->setInsert(true);
                    $column->setSelect(true);
                    $this->currentTable['columns'][] = $column;
                    unset($column);
                }
                $this->currentColumn = array();
            break;
            case $name === 'index' && $this->_xPath('//table/declaration/index'):
                if (!empty($this->currentColumn['name'])) {
                    foreach ($this->currentTable['columns'] as $i => $column)
                    {
                        if (strcasecmp($column->getName(), $this->currentColumn['name']) === 0) {
                            if (!empty($this->currentColumn['primary']) && strcasecmp($this->currentColumn['primary'], 'false') !== 0) {
                                    $column->setPrimaryKey(true);
                                    $this->currentTable['primary'] = $this->currentColumn['name'];

                            } elseif (!empty($this->currentColumn['unique']) && strcasecmp($this->currentColumn['unique'], 'false') !== 0) {
                                $column->setUnique(true);

                            } else {
                                $column->setIndex(true);

                            }
                            break;
                        }
                    }
                }
                $this->currentColumn = array();
            break;
            default:

            break;
        } // end switch
        $this->xPath = preg_replace('/\/' . preg_quote($name, '/') . '$/i', '', $this->xPath);
    }

    /**
     * _characterData
     *
     * @access  private
     * @param   int     $parser   parser
     * @param   string  $data     data
     * @return  bool
     * @ignore
     */
    function _characterData($parser, $data)
    {
        switch (true)
        {
            case $this->_xPath('/database/name'):
                $this->name = $data;
            break;
            case $this->_xPath('//table/declaration/field/.*'):
                if (trim($data)) {
                    $this->currentColumn[$this->currentName] = trim($data);
                }
            break;
            case $this->_xPath('//table/declaration/index/name'):
                /* ignore */
            break;
            case $this->_xPath('//table/declaration/index/field/name'):
                if (trim($data)) {
                    $this->currentColumn['name'] = trim($data);
                }
            break;
            case $this->_xPath('//table/declaration/index/\w+'):
                if (trim($data)) {
                    $this->currentColumn[$this->currentName] = trim($data);
                }
            break;
            case $this->_xPath('//table/initialization$'):
                assert('!isset($_); // Cannot redeclare var $_');
                if (!isset($this->content)) {
                    $this->content = $this->getContent();
                }
                if (isset($this->currentTable['name'])) {
                    assert('!isset($regExp); // Cannot redeclare var $regExp');
                    $regExp = '<table>.*?<name>' . preg_quote($this->currentTable['name'], '/') .
                        '<\/name>.*?<\/table>';
                    if (preg_match("/$regExp/si", $this->content, $_)) {
                        $this->content = preg_replace("/$regExp/si", "", $this->content);
                        $_ = $_[0];
                        if (preg_match("/(?<=<initialization>).*?(?=<\/initialization>)/si", $_, $_)) {
                            $_ = $_[0];
                            assert('!isset($stmts); // Cannot redeclare var $stmts');
                            $stmts = array();
                            assert('!isset($m); // Cannot redeclare var $m');
                            while (preg_match('/^\s*<(insert|update|delete)>(.*?)<\/(?:(insert|update|delete))>/si', $_, $m))
                            {
                                $_ = str_replace($m[0], '', $_);

                                assert('!isset($stmt); // Cannot redeclare var $stmt');
                                /**
                                 * query type
                                 */
                                switch (mb_strtolower($m[1]))
                                {
                                    case 'insert':
                                        $stmt = "INSERT INTO %TABLE% (%KEYS%) VALUES (%VALUES%)";
                                    break;
                                    case 'update':
                                        $stmt = "UPDATE %TABLE% SET %SET% %WHERE%";
                                    break;
                                    case 'delete':
                                        $stmt = "DELETE FROM %TABLE% %WHERE%";
                                    break;
                                    /* error - unexpected statement */
                                    default:
                                        return;
                                    break;
                                }
                                /**
                                 * table name
                                 */
                                $stmt = str_replace('%TABLE%', $this->currentTable['name'], $stmt);

                                /**
                                 * fields
                                 */
                                $set = $this->_handleField($m[0]);

                                /**
                                 * set, keys, values
                                 */
                                if (mb_strpos($stmt, '%SET%') !== false && isset($set)) {
                                    assert('!isset($_set); // Cannot redeclare var $_set');
                                    $_set = "";
                                    assert('!isset($key); // Cannot redeclare var $key');
                                    assert('!isset($value); // Cannot redeclare var $value');
                                    foreach ($set as $key => $value)
                                    {
                                        if ($_set !== '') {
                                            $_set .= ", ";
                                        }
                                        $_set .= "$key=$value";
                                    } /* end foreach */
                                    unset($key, $value); /* clean up garbage */
                                    if (!empty($_set)) {
                                        $stmt = str_replace('%SET%', $_set, $stmt);
                                    } else {
                                        $stmt = "";
                                    }
                                    unset($_set);

                                } else {
                                    $stmt = str_replace('%KEYS%', implode(', ', array_keys($set)), $stmt);
                                    $stmt = str_replace('%VALUES%', implode(', ', array_values($set)), $stmt);

                                } /* end if */
                                unset($field, $set);

                                /**
                                 * where
                                 */
                                assert('!isset($m1); // Cannot redeclare var $m1');
                                if (mb_strpos($stmt, '%WHERE%') !== false) {
                                    if (preg_match('/^.*?<where>\s*<expression>(.*?)<\/expression>\s*<\/where>.*$/si', $m[0], $m1)) {
                                        $m1 = $this->_handleExpression($m1[1]);
                                    } else {
                                        $m1 = "";
                                    }
                                    if (!empty($m1)) {
                                        $stmt = str_replace('%WHERE%', "WHERE $m1", $stmt);
                                    } else {
                                        $stmt = str_replace('%WHERE%', '', $stmt);
                                    }
                                }
                                unset($m1);

                                /**
                                 * add statement to list
                                 */
                                if (!empty($stmt)) {
                                    $stmts[] = $stmt;
                                }
                                unset($stmt);
                            } /* end while */
                            $this->currentTable['init'] = $stmts;
                            unset($stmts);
                        } /* end if */
                    } /* end if */
                    unset($_, $regExp);
                } /* end if */
            break;
            case $this->_xPath('//table/[^/]*'):
                if (trim($data)) {
                    $this->currentTable[$this->currentName] = trim($data);
                }
            break;
        } /* end switch */
    }

    /**
     * _handleField
     *
     * @access  private
     * @param   string  $data     data
     * @return  array
     * @ignore
     */
    function _handleField($data)
    {
        $set = array();

        assert('!isset($m1); // Cannot redeclare var $m1');
        if (preg_match_all('/<field>(.*?)<\/field>/si', $data, $m1)) {
            assert('!isset($field); // Cannot redeclare var $field');
            foreach ($m1[1] as $string)
            {
                $field = \Yana\Files\SML::decode($string);

                if (isset($field['name'])) {
                    $value = $this->_handleValue($field, $string);
                    if (is_string($value)) {
                        $set[$field['name']] = $value;
                    } /* end if */
                } /* end if */
            } /* end foreach */
        } /* end if */
        unset($m1);

        return $set;
    }


    /**
     * _handleExpression
     *
     * @access  private
     * @param   string  $string   string
     * @return  string
     * @ignore
     */
    function _handleExpression($string = "")
    {
        $expression = trim($string);

        $expression = preg_replace('/\s*(<\w+>(?:.*?)<\/\w+>)\s*/s', '$1', $string);

        /**
         * Function
         */
        $expression = preg_replace("/\s*<function>\s*<name>(.*?)<\/name>\s*/si", '$1(', $expression);
        $expression = preg_replace("/\s*<\/function>\s*/si", ')', $expression);

        /**
         * Commas
         */
        $expression = preg_replace("/(<\/null>|<null\s*\/>|<\/value>|<\/column>|<\/function>|<\/expression>)\s*".
            "(<null>|<null\s*\/>|<value>|<column>|<function>|<expression>)/si", '$1, $2', $expression);

        /**
         * Operator
         */
        preg_match_all('/\s*<operator>(.*?)<\/operator>\s*/si', $expression, $m);
        foreach (array_unique($m[1]) as $operator)
        {
            /**
             * operator list according to documentation of version v 1.9 2006/10/21 of MDB2 XML Schema
             */
            switch (mb_strtoupper($operator))
            {
                case 'PLUS':
                    $newOperator = '+';
                break;
                case 'MINUS':
                    $newOperator = '-';
                break;
                case 'TIMES':
                    $newOperator = '*';
                break;
                case 'DIVIDED':
                    $newOperator = '/';
                break;
                case 'EQUAL':
                    $newOperator = '=';
                break;
                case 'NOT EQUAL':
                    $newOperator = '!=';
                break;
                case 'LESS THAN':
                    $newOperator = '<';
                break;
                case 'GREATER THAN':
                    $newOperator = '>';
                break;
                case 'LESS THAN OR EQUAL':
                    $newOperator = '<=';
                break;
                case 'GREATER THAN OR EQUAL':
                    $newOperator = '>=';
                break;
                default:
                    $newOperator = $operator;
                break;
            } /* end switch */
            $expression = preg_replace("/\s*<operator>$operator<\/operator>\s*/si", " $newOperator ", $expression);
        } /* end foreach */

        /**
         * Column
         */
        $expression = preg_replace("/\s*<column>(.*?)<\/column>\s*/si", ' $1 ', $expression);

        /**
         * Value
         */
        $expression = preg_replace("/\s*<value>(.*?)<\/value>\s*/sie",
            '" " . ( (is_numeric("$1")) ? "$1" : \Yana\Db\Export\DataExporter::quoteValue("$1")) . " "', $expression);

        /**
         * Null
         */
        $expression = preg_replace("/\s*(?:<null><\/null>|<null\s*\/>)\s*/si", ' NULL ', $expression);

        return trim($expression);
    }

    /**
     * _handleFunction
     *
     * @access  private
     * @param   array   $data     data
     * @param   string  $string   string
     * @return  string
     * @ignore
     */
    function _handleFunction(array $data, $string = "")
    {
        $function = "";
        $i = 0;

        if (isset($data['name'])) {
            $function .= $data['name'] . "(";
            foreach (array_keys($data) as $name)
            {
                if (preg_match('/^.*?<\/' . $name . '>/si', $string, $m)) {
                    $m = $m[0];
                    $array = \Yana\Files\SML::decode($m);
                    $string = mb_substr($string, mb_strlen($m[0]));
                }
                $value = false;
                switch ($name)
                {
                    case 'value':
                        $value = $this->_handleValue($array, $m);
                    break;
                    case 'column':
                        $value = $this->_handleValue($array, $m);
                    break;
                    case 'function':
                        $value = $this->_handleFunction($array, $m);
                    break;
                    case 'expression':
                        $value = $this->_handleExpression($m);
                    break;
                    default:
                        continue;
                    break;
                }
                if (is_string($value)) {
                    if ($i > 0) {
                        $function .= ", ";
                    }
                    $function .= $value;
                    $i++;
                }
            }
            $function .= ")";
        } /* end if */

        return $function;
    }

    /**
     * _handleValue
     *
     * @access  private
     * @param   array   $data     data
     * @param   string  $string   string
     * @return  string
     * @ignore
     */
    function _handleValue(array $data, $string)
    {
        switch (true)
        {
            case isset($data['value']):
                return \Yana\Data\StringValidator::sanitize((string) $data['value'], 255);

            case isset($data['column']):
                return (string) $data['column'];

            case array_key_exists('null', $data):
                return 'NULL';

            case isset($data['function']):
                $string = preg_replace('/^.*?<function>(.*?)<\/function>.*$/si', '$1', $string);
                return $this->_handleFunction(\Yana\Files\SML::decode($string), $string);

            case isset($data['expression']):
                $string = preg_replace('/^.*?<expression>(.*?)<\/expression>.*$/si', '$1', $string);
                return $this->_handleExpression($string);

            default:
                return false;
        } /* end switch */
    }

    /**
     * _xPath
     *
     * @access  private
     * @param   string  $name    name
     * @return  bool
     * @ignore
     */
    function _xPath($name)
    {
        if (mb_strpos($name, '//') === 0) {
            $name = mb_substr($name, 1);
            return preg_match('/' . addcslashes($name, '/') . '$/i', $this->xPath);

        } else {
            return preg_match('/^' . addcslashes($name, '/') . '$/i', $this->xPath);

        }
    }
}

?>