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

/**
 * @ignore
 */
require_once 'isdbimport.php';
/**
 * @ignore
 */
require_once 'dbinfotable.php';

/**
 * DBDesigner 4 import class
 *
 * This class allows you to read DBDesigner 4 configuration files.
 *
 * Use DbDesigner4->getTableInfo(), or DbDesigner4->getStructure()
 * to import the file.
 *
 * Example:
 * <code>
 * $schema = new DbDesigner4('schema.xml');
 * $schema->read();
 * $structure = $schema->getStructure();
 * $structure->create();
 * $structure->write();
 * </code>
 *
 * Once you have a valid structure file,
 * you can open and query it easily, by using the
 * functions provided by DbStructure.
 *
 * @access     public
 * @package    yana
 * @subpackage plugins
 */
class DbDesigner4 extends File implements IsDbImport
{
    /**#@+
     * @ignore
     * @access  private
     */

    /** @var string       */ private $name = "";
    /** @var array        */ private $info = array();
    /** @var DbInfoTable  */ private $currentTable = array('columns' => array());
    /** @var DbInfoColumn */ private $currentColumn = array();
    /** @var array        */ private $currentIndex = array();
    /** @var array        */ private $dataTypes = "";
    /** @var array        */ private $columns = array();
    /** @var array        */ private $tableNames = array();

    /**#@-*/

    /**
     * Return table info for current data
     *
     * @access  public
     * @param   string  $table table name
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
                trigger_error($message, E_USER_WARNING);
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
     * import DBDesigner 4 configuration file to Yana structure files
     *
     * The argument $dbDesignerConfig may either be a file name or XML file content.
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
     * @param   string   $dbDesignerConfig  file name or XML file content
     * @return  DbStructure
     */
    public static function getStructureFromString($dbDesignerConfig)
    {
        assert('is_string($dbDesignerConfig); // Wrong argument type $dbDesignerConfig. String expected.');
        if (is_file($dbDesignerConfig)) {
            $DbDesigner4 = new DbDesigner4($dbDesignerConfig);
            $DbDesigner4->read();
        } else {
            $DbDesigner4 = new DbDesigner4('');
            assert('empty($DbDesigner4->content);');
            $DbDesigner4->content = explode("\n", $dbDesignerConfig);
            assert('is_array($DbDesigner4->content);');
        }
        $structure = $DbDesigner4->getStructure();
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
     * @access  public
     * @param   string  $filename  name of newly created structure file
     * @return  DbStructure
     */
    public function &getStructure($filename = "")
    {
        if (empty($filename)) {
            $filename = $this->path . ".config";
        }
        $structure = new DbStructure($filename);
        foreach ($this->getTableInfo() as $tableInfo)
        {
            $structure->addStructure($tableInfo['name'], $tableInfo);
        }
        return $structure;
    }

    /**
     * _startElement
     *
     * @access  private
     * @param   int     $parser parser
     * @param   string  $name   name
     * @param   array   $attrs  attributes
     * @return  bool
     * @ignore
     */
    function _startElement($parser, $name, array $attrs)
    {
        $name = mb_strtolower($name);
        switch ($name)
        {
            case 'globalsettings':
                $this->name = @$attrs['MODELNAME'];
            break;

            case 'datatype':
                $this->dataTypes[$attrs['ID']] = $attrs['TYPENAME'];
            break;

            case 'table':
                $this->currentTable = new DbInfoTable($attrs['TABLENAME']);
                @$this->currentTable->setComment($attrs['TABLENAME']);
                $this->tableNames[$attrs['ID']] = $attrs['TABLENAME'];
                /**
                 * <table StandardInserts ...>   semicolon-seperated list of sql statements
                 * <table UseStandardInserts ...>  is "0" if the sql statements are meant to be ignored
                 */
                if (!empty($attrs['STANDARDINSERTS']) && !empty($attrs['USESTANDARDINSERTS'])) {
                    assert('!isset($init); // Cannot redeclare var $init');
                    assert('!isset($m); // Cannot redeclare var $m');
                    $init = $attrs['STANDARDINSERTS'];
                    /**
                     * quote values
                     */
                    assert('!isset($quotedValue); // Cannot redeclare var $quotedValue');
                    while (preg_match('/(?<=\\\\a).*?(?=\\\\a)/is', $init, $m))
                    {
                        $quotedValue = preg_replace('/(?<!\\\\)\\\\(\d+)/ise', 'chr($1)', $m[0]);
                        $quotedValue = \Yana\Db\DataExporter::quoteValue($quotedValue);
                        $init = str_replace("\\a" . $m[0] . "\\a", $quotedValue, $init);
                    }
                    unset($quotedValue);
                    $init = preg_replace('/(?:;\s*\\\\n|;\s*\n)\s*$/s', '', $init);
                    $init = preg_split('/;\s*\\\\n|;\s*\n/', $init);
                    if (is_array($init)) {
                        $this->currentTable->setInit($init);
                    }
                    unset($m, $init);
                } /* end if */
            break;

            case 'column':
                $this->currentColumn = new DbInfoColumn($attrs['COLNAME']);
                $this->currentColumn->setTable($this->currentTable->getName());
                if (isset($this->dataTypes[$attrs['IDDATATYPE']])) {

                    $this->currentColumn->setType($this->dataTypes[$attrs['IDDATATYPE']]);

                    switch (mb_strtolower($this->dataTypes[$attrs['IDDATATYPE']]))
                    {
                        case 'set':
                        case 'enum':
                            if (isset($attrs['DATATYPEPARAMS'])) {
                                if (preg_match_all('/(?<=\\\\a)[^,]+?(?=\\\\a)/', $attrs['DATATYPEPARAMS'], $m)) {
                                    $this->currentColumn->setDefault(implode(', ', $m[0]));
                                } else {
                                    $this->currentColumn->setDefault($attrs['DEFAULTVALUE']);
                                }
                            } else {
                                if (isset($attrs['DEFAULTVALUE']) && $attrs['DEFAULTVALUE'] !== '') {
                                    $this->currentColumn->setDefault($attrs['DEFAULTVALUE']);
                                } else {
                                    $this->currentColumn->setDefault(null);
                                }
                            } /* end if */
                        break;
                        default;
                        if (isset($attrs['DEFAULTVALUE']) && $attrs['DEFAULTVALUE'] !== '') {
                            $this->currentColumn->setDefault($attrs['DEFAULTVALUE']);
                        } else {
                            $this->currentColumn->setDefault(null);
                        }
                        break;
                    } /* end switch */

                } else {

                    if (isset($attrs['DEFAULTVALUE']) && $attrs['DEFAULTVALUE'] !== '') {
                        $this->currentColumn->setDefault($attrs['DEFAULTVALUE']);
                        if (is_numeric($attrs['DEFAULTVALUE'])) {
                            $this->currentColumn->setType('float');
                        } else {
                            $this->currentColumn->setType('varchar');
                        }
                    } else {
                        $this->currentColumn->setDefault($attrs['DEFAULTVALUE']);
                    }

                } /* end if */
                $this->currentColumn->setNullable(!empty($attrs['NOTNULL']));
                if (!empty($attrs['PRIMARYKEY'])) {
                    $this->currentColumn->setPrimaryKey(true);
                } else {
                    $this->currentColumn->setPrimaryKey(false);
                }
                $this->currentColumn->setAuto(!empty($attrs['AUTOINC']));
                @$this->currentColumn->setComment($attrs['COMMENTS']);
                $this->currentColumn->setForeignKey(!empty($attrs['ISFOREIGNKEY']));
                /* properties of database */
                $this->currentColumn->setUpdate(true);
                $this->currentColumn->setInsert(true);
                $this->currentColumn->setSelect(true);
                /* properties of index */
                $this->currentColumn->setUnique(false);
                $this->currentColumn->setIndex(false);
                /* add to list */
                $this->columns[$attrs['ID']] = $this->currentColumn;
            break;

            case 'index':
                switch ((int) $attrs['INDEXKIND'])
                {
                    /**
                     * primary index
                     *
                     * ignored: unique indexes on primary keys are created automatically by the DBMS
                     */
                    case 0:
                        $this->currentIndex['unique'] = false;
                        $this->currentIndex['index'] = false;
                    break;
                    /**
                     * standard index
                     */
                    case 1:
                        $this->currentIndex['unique'] = false;
                        $this->currentIndex['index'] = true;
                    break;
                    /*
                     * unique index
                     *
                     * ignored: indexes on unique columns are created automatically by the DBMS, if the "unique"
                     *          key word is present
                     */
                    case 2:
                        $this->currentIndex['unique'] = true;
                        $this->currentIndex['index'] = false;
                    break;
                    /*
                     * fulltext index
                     */
                    case 3:
                        $this->currentIndex['unique'] = false;
                        $this->currentIndex['index'] = true;
                    break;
                    /*
                     * default
                     */
                    default:
                        /* error - unrecognized index */
                        $this->currentIndex['unique'] = false;
                        $this->currentIndex['index'] = false;
                    break;
                }
            break;

            case 'indexcolumn':
                if (isset($attrs['IDCOLUMN']) && isset($this->columns[$attrs['IDCOLUMN']])) {
                    if ($this->currentIndex['index']) {
                        $this->columns[$attrs['IDCOLUMN']]->index = true;
                    }
                    if ($this->currentIndex['unique']) {
                        $this->columns[$attrs['IDCOLUMN']]->unique = true;
                    }
                }
            break;

            case 'relation':
                /*
                 * Note: DBDesigner names relations as follows.
                 *
                 * The "source table" is the referenced table (where FK points to).
                 * The "destination table" is the current referencing table.
                 * The columns are listed 1st) referenced column 2nd) FK column.
                 */
                if (isset($this->tableNames[$attrs['SRCTABLE']])) {
                    $foreignTable = $this->tableNames[$attrs['SRCTABLE']];
                } else {
                    break;
                }
                if (isset($this->tableNames[$attrs['DESTTABLE']])) {
                    $table = $this->tableNames[$attrs['DESTTABLE']];
                } else {
                    break;
                }
                if (isset($attrs['FKFIELDS'])) {
                    $cols = $attrs['FKFIELDS'];
                    $cols = str_replace('\n', '', $cols);
                    $cols = explode('=', $cols);
                } else {
                    break;
                }
                if (isset($this->info[$table]) && !empty($foreignTable) && isset($cols[0]) && isset($cols[1])) {
                    $this->info[$table]->setForeignKey($cols[1], $foreignTable, $cols[0]);
                }
            break;

        }
    }

    /**
     * _endElement
     *
     * @access  private
     * @param   int     $parser  parser
     * @param   string  $name    name
     * @return  bool
     * @ignore
     */
    function _endElement($parser, $name)
    {
        $name = mb_strtolower($name);
        switch ($name)
        {
            case 'table':
                foreach ($this->columns as $column)
                {
                    if (!empty($column->name)) {
                        $this->currentTable->addColumn($column);
                    }
                }
                $this->columns = array();
                $this->info[$this->currentTable->getName()] = $this->currentTable;
                unset($this->currentTable);
            break;
            case 'column':
                unset($this->currentColumn);
            break;
            case 'index':
                $this->currentIndex = array();
            break;
            default:

            break;
        }
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
        /* intentionally left blank */
    }
}

?>