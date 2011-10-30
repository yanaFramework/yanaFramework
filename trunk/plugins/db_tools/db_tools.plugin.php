<?php
/**
 * DB-Tools
 *
 * This plugin allows import and export of contents (data) and structure information (meta data)
 * between databases, external modeling tools and the XML format, which is used by the Yana
 * Framework.
 *
 * {@translation
 *
 *   de: DB-Tools
 *
 *       Dieses Plugin erlaubt den Import und Export von Inhalten (Daten) und Strukturinformationen
 *       (Metadaten) zwischen Datenbanken, externen Entwurfswerkzeugen und dem XML-Format,
 *       welches vom Yana Framework verwendet wird.
 * }
 *
 * @author     Thomas Meyer
 * @type       config
 * @group      db_tools
 * @license    http://www.gnu.org/licenses/gpl.txt
 *
 * @package    yana
 * @subpackage plugins
 */

/**
 * class to handle imports from DB-Designer 4
 */
require_once 'dbdesigner4.php';
/**
 * class to handle imports from PEAR MDB2 Schema
 */
require_once 'dbmdb2.php';

/**
 * Database tools
 *
 * This implements a several database modeling and maintenance utilities
 *
 * @access     public
 * @package    yana
 * @subpackage plugins
 */
class plugin_db_tools extends StdClass implements IsPlugin
{

    /**
     * List of exportable DBMS ids and names
     *
     * @access  private
     * @var     array
     * @ignore
     */
    private $listOfExportableDBMS = array(
        'mysql'  => 'MySQL',
        'db2'    => 'DB2',
        'access' => 'MS-Access',
        'mssql'  => 'MS-SQL',
        'oci8'   => 'Oracle',
        'pgsql'  => 'PostgreSQL',
    );

    /**
     * Default event handler
     *
     * @access  public
     * @return  bool
     * @param   string  $event  name of the called event in lower-case
     * @param   array   $ARGS   array of arguments passed to the function
     * @ignore
     */
    public function catchAll($event, array $ARGS)
    {
        return true;
    }

    /**
     * Import
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    DB_TOOLS_IMPORT
     * @menu        group: setup
     * @title       Import
     * @safemode    true
     *
     * @access      public
     */
    public function db_tools_config_import()
    {
        // Just views template - no business logic required.
    }

    /**
     * Export
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    DB_TOOLS_EXPORT
     * @menu        group: setup
     * @title       Export
     * @safemode    true
     *
     * @access      public
     */
    public function db_tools_config_export()
    {
        global $YANA;
        $YANA->setVar('SELECTED_DBMS', YANA_DATABASE_DBMS);
        $YANA->setVar('LIST_OF_EXPORTABLE_DBMS', $this->listOfExportableDBMS);
        $YANA->setVar("DATABASE_LIST", \Yana\Db\Ddl\DDL::getListOfFiles());
        // Just views template - no further business logic required.
    }

    /**
     * Reverse-engineer database
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    null
     * @safemode    true
     *
     * @access      public
     */
    public function db_tools_write_config()
    {
        global $YANA;
        $errorReporting = error_reporting(E_ERROR | E_WARNING); // suppress MDB2 Notices
        $db = new DbServer();
        error_reporting($errorReporting);
        $xml = (string) \Yana\Db\Ddl\DatabaseFactory::createDatabase($db->getConnection());
        $filename = 'database.db.xml';
        if (empty($xml)) {
            $error = new FileNotCreatedError();
            throw $error->setFilename($filename);
        }
        // output file
        header("Content-Disposition: attachment; filename=${filename}");
        header("Content-type: text/xml");
        header("Content-Length: " . mb_strlen($xml));
        exit($xml);
    }

    /**
     * export database content to xml
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    message
     * @onsuccess   goto: DB_TOOLS_CONFIG_EXPORT
     * @onerror     goto: DB_TOOLS_CONFIG_EXPORT
     * @safemode    true
     *
     * @access      public
     * @param       array  $list  list of database schemas
     */
    public function db_tools_exportxml(array $list)
    {
        global $YANA;

        if (empty($list)) {
            throw new InvalidInputWarning();
        }

        $xml = \Yana\Db\Export\DataFactory::createXML(true, array_values($list));
        $filename = 'database.xml';
        if (empty($xml)) {
            $error = new FileNotCreatedError();
            throw $error->setFilename($filename);
        }
        // output file
        header("Content-Disposition: attachment; filename=${filename}");
        header('Content-Type: application/xml');
        header("Content-Length: " . mb_strlen($xml));
        exit($xml);
    }

    /**
     * Import and convert MDB2 schema file to Yana database structure
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    message
     * @onsuccess   goto: DB_TOOLS_CONFIG_IMPORT
     * @onerror     goto: DB_TOOLS_CONFIG_IMPORT
     * @safemode    true
     *
     * @access      public
     * @return      bool
     */
    public function db_tools_importmdb2()
    {
        if (!empty($_FILES['mdb2']['tmp_name'])) {
            $structure = DbMDB2::getStructureFromString($_FILES['mdb2']['tmp_name']);
            if (empty($structure)) {
                return false;
            } else {
                $structure = $structure->getStructure();
            }
            if (!is_array($structure)) {
                return false;
            } else {
                $structure = SML::encode($structure);
            }
            $filename = 'database.config';
            if (!empty($structure)) {
                $error = new FileNotCreatedError();
                throw $error->setFilename($filename);
            }
            // output file
            header("Content-Disposition: attachment; filename=${filename}");
            header("Content-type: text/plain");
            header("Content-Length: " . mb_strlen($structure));
            exit($structure);
        } else {
            return false;
        }
    }

    /**
     * Import and convert MDB2 schema file to Yana database structure
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    message
     * @onsuccess   goto: DB_TOOLS_CONFIG_IMPORT
     * @onerror     goto: DB_TOOLS_CONFIG_IMPORT
     * @safemode    true
     *
     * @access      public
     * @return      bool
     */
    public function db_tools_importdbdesigner4 ()
    {
        if (empty($_FILES['dbdesigner4']['tmp_name'])) {
            return false;
        }
        $structure = DbDesigner4::DbDesigner4($_FILES['dbdesigner4']['tmp_name']);
        if (empty($structure)) {
            return false;
        } else {
            $structure = $structure->getStructure();
        }
        if (!is_array($structure)) {
            return false;
        } else {
            $structure = SML::encode($structure);
        }
        $filename = 'database.config';
        if (empty($structure)) {
            $error = new FileNotCreatedError();
            throw $error->setFilename($filename);
        }
        // output file
        header("Content-Disposition: attachment; filename=${filename}");
        header("Content-type: text/plain");
        header("Content-Length: " . mb_strlen($structure));
        exit($structure);
    }

    /**
     * Forward-engineer database
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    null
     * @safemode    true
     *
     * @access      public
     * @param       string  $dbms           type of DBMS
     * @param       array   $list  list of database schemas
     */
    public function db_tools_exportsql ($dbms, array $list)
    {
        global $YANA;

        $fileContents = "";

        foreach ($list as $dbName)
        {
            if (!is_string($dbName)) {
                continue;
            }

            /* Mapping the DBMS to the SQL export function in class \Yana\Db\Export\SqlFactory */
            switch ($dbms)
            {
                case 'db2':
                    $methodName  = 'createDB2';
                break;
                case 'access':
                    $methodName  = 'createMSAccess';
                break;
                case 'mssql':
                    $methodName  = 'createMSSQL';
                break;
                case 'mysql':
                case 'mysqli':
                    $methodName  = 'createMySQL';
                break;
                case 'oci8':
                    $methodName  = 'createOracleDB';
                break;
                case 'pgsql':
                    $methodName  = 'createPostgreSQL';
                break;
                case 'dbase':
                case 'fbsql':
                case 'ibase':
                case 'ifx':
                case 'sybase':
                default:
                    $error = new InvalidValueWarning();
                    throw $error->setField('DBMS=' . $dbms);
            }
            $dbc = new \Yana\Db\Export\SqlFactory( XDDL::getDatabase($dbName) );
            $arrayOfStmts = $dbc->$methodName();
            $fileContents .= implode("\n", $arrayOfStmts) . "\n";
        }

        $filename = mb_strtolower(preg_replace('/\W/', '_', $dbms) . '.sql');
        if (empty($fileContents)) {
            $error = new FileNotCreatedError();
            throw $error->setFilename($filename);
        }
        header("Content-Disposition: attachment; filename=${filename}");
        header("Content-type: text/plain");
        header("Content-Length: " . mb_strlen($fileContents));
        exit($fileContents);
    }

}

?>