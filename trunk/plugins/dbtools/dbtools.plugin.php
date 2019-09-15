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

namespace Plugins\DbTools;

/**
 * Database tools
 *
 * This implements a several database modeling and maintenance utilities
 *
 * @package    yana
 * @subpackage plugins
 */
class DbToolsPlugin extends \Yana\Plugins\AbstractPlugin
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
        $YANA = $this->_getApplication();
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
     * @template    message
     * @onerror     goto: DB_TOOLS_CONFIG_IMPORT
     * @safemode    true
     *
     * @access      public
     */
    public function db_tools_write_config()
    {
        $factory = new \Yana\Db\Ddl\Factories\DatabaseFactory();
        $schema = $factory->buildWorker()->createDatabase();
        $xmlObject = $schema->serializeToXDDL();
        $filename = 'database.db.xml';
        if (empty($xmlObject)) {
            $message = "Did not create '{$filename}' because the file is empty.";
            $code = \Yana\Log\TypeEnumeration::WARNING;
            $error = new \Yana\Core\Exceptions\Files\NotCreatedException($message, $code);
            throw $error->setFilename($filename);
        }
        $xmlString = $xmlObject->asXML();
        // output file
        header("Cache-Control: maxage=1"); // Bug in IE8 with HTTPS-downloads
        header("Pragma: public");
        header("Content-Disposition: attachment; filename={$filename}");
        header("Content-type: text/xml");
        header("Content-Length: " . mb_strlen($xmlString));
        exit($xmlString);
    }

    /**
     * export database content to xml
     *
     * @type       config
     * @user       group: admin, level: 100
     * @template   message
     * @onsuccess  goto: DB_TOOLS_CONFIG_EXPORT
     * @onerror    goto: DB_TOOLS_CONFIG_EXPORT
     * @safemode   true
     *
     * @param      array  $list  list of database schemas
     * @throws     \Yana\Core\Exceptions\Forms\NothingSelectedException  when nothing was selected to export
     */
    public function db_tools_exportxml(array $list)
    {
        if (empty($list)) {
            $message = "The list of databases to export is empty.";
            $level = \Yana\Log\TypeEnumeration::INFO;
            throw new \Yana\Core\Exceptions\Forms\NothingSelectedException($message, $level);
        }

        $xmlFactory = new \Yana\Db\Export\XmlFactory();
        $xmlFactory
                ->setUsingForeignKeys(true)
                ->setDatabaseNames(array_values($list));

        $xml = $xmlFactory->createXML(new \Yana\Db\ConnectionFactory(new \Yana\Db\SchemaFactory()));
        $filename = 'database.xml';
        if (empty($xml)) {
            $message = "Did not create '{$filename}' because the file is empty.";
            $code = \Yana\Log\TypeEnumeration::WARNING;
            $error = new \Yana\Core\Exceptions\Files\NotCreatedException($message, $code);
            throw $error->setFilename($filename);
        }
        // output file
        header("Cache-Control: maxage=1"); // Bug in IE8 with HTTPS-downloads
        header("Pragma: public");
        header("Content-Disposition: attachment; filename={$filename}");
        header('Content-Type: application/xml');
        header("Content-Length: " . strlen($xml));
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
            $structure = \Plugins\DbTools\MDB2::getStructureFromString($_FILES['mdb2']['tmp_name']);
            if (empty($structure)) {
                return false;
            } else {
                $structure = $structure->getStructure();
            }
            if (!is_array($structure)) {
                return false;
            } else {
                $structure = \Yana\Files\SML::encode($structure);
            }
            $filename = 'database.config';
            if (!empty($structure)) {
                $message = "Did not create '{$filename}' because the file is empty.";
                $code = \Yana\Log\TypeEnumeration::WARNING;
                $error = new \Yana\Core\Exceptions\Files\NotCreatedException($message, $code);
                throw $error->setFilename($filename);
            }
            // output file
            header("Cache-Control: maxage=1"); // Bug in IE8 with HTTPS-downloads
            header("Pragma: public");
            header("Content-Disposition: attachment; filename={$filename}");
            header("Content-type: text/plain");
            header("Content-Length: " . strlen($structure));
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
        $structure = \Plugins\DbTools\DbDesigner4::DbDesigner4($_FILES['dbdesigner4']['tmp_name']);
        if (empty($structure)) {
            return false;
        } else {
            $structure = $structure->getStructure();
        }
        if (!is_array($structure)) {
            return false;
        } else {
            $structure = \Yana\Files\SML::encode($structure);
        }
        $filename = 'database.config';
        if (empty($structure)) {
            $message = "Did not create '{$filename}' because the file is empty.";
            $code = \Yana\Log\TypeEnumeration::WARNING;
            $error = new \Yana\Core\Exceptions\Files\NotCreatedException($message, $code);
            throw $error->setFilename($filename);
        }
        // output file
        header("Cache-Control: maxage=1"); // Bug in IE8 with HTTPS-downloads
        header("Pragma: public");
        header("Content-Disposition: attachment; filename={$filename}");
        header("Content-type: text/plain");
        header("Content-Length: " . strlen($structure));
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
     * @throws      \Yana\Core\Exceptions\Forms\InvalidSyntaxException  when the chosen DBMS is not valid
     */
    public function db_tools_exportsql ($dbms, array $list)
    {
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
                    $message = "Chosen DBMS is invalid.";
                    $level = \Yana\Log\TypeEnumeration::WARNING;
                    $error = new \Yana\Core\Exceptions\Forms\InvalidSyntaxException($message, $level);
                    throw $error->setValue($dbms)->setValid('DB2, MSSQL, MYSQL, OCI8, PGSQL')->setField('DBMS');
            }
            $dbc = new \Yana\Db\Export\SqlFactory( \Yana\Files\XDDL::getDatabase($dbName) );
            $arrayOfStmts = $dbc->$methodName();
            $fileContents .= implode("\n", $arrayOfStmts) . "\n";
        }

        $filename = mb_strtolower(preg_replace('/\W/', '_', $dbms) . '.sql');
        if (empty($fileContents)) {
            $message = "Did not create '{$filename}' because the file is empty.";
            $code = \Yana\Log\TypeEnumeration::WARNING;
            $error = new \Yana\Core\Exceptions\Files\NotCreatedException($message, $code);
            throw $error->setFilename($filename);
        }
        header("Cache-Control: maxage=1"); // Bug in IE8 with HTTPS-downloads
        header("Pragma: public");
        header("Content-Disposition: attachment; filename={$filename}");
        header("Content-type: text/plain");
        header("Content-Length: " . strlen($fileContents));
        exit($fileContents);
    }

}

?>