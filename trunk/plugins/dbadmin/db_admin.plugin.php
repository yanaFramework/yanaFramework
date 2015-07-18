<?php
/**
 * DB-Administration
 *
 * This plugin allows configuration of an optional database connection.
 * This setup is only available on the profile "basic settings".
 *
 * {@translation
 *
 *   de: DB-Administration
 *
 *       Mit diesem Plugin kann eine optionale Anbindung an eine Datenbank konfiguriert werden.
 *       Die Konfiguration ist nur im Profil "Basiseinstellungen" möglich.
 *
 * }
 *
 * @author     Thomas Meyer
 * @type       config
 * @license    http://www.gnu.org/licenses/gpl.txt
 *
 * @package    yana
 * @subpackage plugins
 */

/**
 * database administration tool
 *
 * @access     public
 * @package    yana
 * @subpackage plugins
 */
class plugin_db_admin extends StdClass implements \Yana\IsPlugin
{
    /**
     * Constructor
     *
     * @access public
     * @ignore
     */
    public function __construct()
    {
        /**
         * load PEAR-DB
         * @ignore
         */
        @include_once "MDB2.php";
    }

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
     * provide database setup form
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    DB_CONFIGURATION
     * @menu        group: setup
     * @safemode    true
     *
     * @access      public
     */
    public function get_db_configuration()
    {
        $yana = \Yana\Application::getInstance();

        if (!class_exists("MDB2")) {
            throw new \Yana\Db\Mdb2\PearDbException();
        }

        $yana->setVar("DATABASE_ACTIVE",      YANA_DATABASE_ACTIVE);
        $yana->setVar("DATABASE_DBMS",        YANA_DATABASE_DBMS);
        $yana->setVar("DATABASE_HOST",        YANA_DATABASE_HOST);
        $yana->setVar("DATABASE_PORT",        YANA_DATABASE_PORT);
        $yana->setVar("DATABASE_USER",        YANA_DATABASE_USER);
        $yana->setVar("DATABASE_PASSWORD",    YANA_DATABASE_PASSWORD);
        $yana->setVar("DATABASE_PREFIX",      YANA_DATABASE_PREFIX);
        $yana->setVar("DATABASE_NAME",        YANA_DATABASE_NAME);
        $yana->setVar("YANA_DATABASE_ACTIVE", YANA_DATABASE_ACTIVE);
        $DATABASE_LIST = \Yana\Db\Ddl\DDL::getListOfFiles();
        $yana->setVar("DATABASE_LIST", $DATABASE_LIST);
    }

    /**
     * install databases
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    MESSAGE
     * @onsuccess   goto: GET_DB_CONFIGURATION
     * @onerror     goto: GET_DB_CONFIGURATION, text: Yana\Core\Exceptions\InvalidInputException
     * @safemode    true
     *
     * @access      public
     * @param       string  $dbms  type of DBMS
     * @param       array   $list  list of database schemas
     * @throws      \Yana\Core\Exceptions\Forms\MissingInputException  when either DBMS or databases have not been selected
     * @throws      \Yana\Db\Mdb2\PearDbException                      when PEAR MDB2 was not found
     */
    public function db_install($dbms, array $list)
    {
        global $YANA;

        if (!class_exists("MDB2")) {
            throw new \Yana\Db\Mdb2\PearDbException();
        }

        if (empty($dbms) || empty($list)) {
            /* nothing to do */
            $message = "No databases for target DBMS selected.";
            $level = \Yana\Log\TypeEnumeration::INFO;
            throw new \Yana\Core\Exceptions\Forms\MissingInputException($message, $level);
        }
        $dbms = mb_strtoupper($dbms);
        $dbList = $list;

        /* Mapping the DBMS to the SQL export function in class \Yana\Db\Export\SqlFactory */
        switch ($dbms)
        {
            case 'DB2':
                $methodName  = 'createDB2';
            break;
            case 'ACCESS':
                $methodName  = 'createMSAccess';
            break;
            case 'MSSQL':
                $methodName  = 'createMSSQL';
            break;
            case 'MYSQL':
            case 'MYSQLI':
                $dbms         = 'MYSQL';
                $methodName  = 'createMySQL';
            break;
            case 'OCI8':
                $methodName  = 'createOracleDB';
            break;
            case 'PGSQL':
                $methodName  = 'createPostgreSQL';
            break;

            // The following DBMS are currently not supported
            case 'DBASE':
            case 'FBSQL':
            case 'IBASE':
            case 'IFX':
            case 'SYBASE':
            default:
                $message = "Chosen DBMS is invalid.";
                $level = \Yana\Log\TypeEnumeration::WARNING;
                $error = new \Yana\Core\Exceptions\Forms\InvalidSyntaxException($message, $level);
                throw $error->setValue($dbms)->setValid('DB2, MSSQL, MYSQL, OCI8, PGSQL')->setField('DBMS');
        }

        /* Mapping the DBMS to it's installation directory */
        $installDirectory = $YANA->getResource('system:/dbinstall/' . mb_strtolower($dbms));
        if (!$installDirectory instanceof \Yana\Files\Dir || !$installDirectory->exists()) {
            /* invalid option - the choosen dbms is unknown */
            \Yana\Log\LogManager::getLogger()->addLog("Unable to install database. The choosen DBMS '{$dbms}' is unknown.");
            $message = 'Did not create SQL file because the target directoy does not exist.';
            $code = \Yana\Log\TypeEnumeration::WARNING;
            $error = new \Yana\Core\Exceptions\Files\NotFoundException($message, $code);
            throw $error->setFilename("{$dbms}");
        }

        /* we assume the class \Yana\Db\Export\SqlFactory has the desired method. This will be tested later! */

        /* get the list of available installation files */
        $installDirectory = $installDirectory->getPath();

        assert('!isset($initialization); // Cannot redeclare var $initialization');
        $initialization = array();

        foreach ($dbList as $item)
        {
            /* check the input */
            if (!is_string($item)) {
                $message = 'Did not create SQL file because the input is invalid.';
                $code = \Yana\Log\TypeEnumeration::WARNING;
                $error = new \Yana\Core\Exceptions\Forms\InvalidValueException($message, $code);
                throw $error->setField('DBMS');
            } else {
                $item = mb_strtolower($item);
            }

            $installFile = $installDirectory . $item . '.sql';
            $dbSchema = \Yana\Files\XDDL::getDatabase($item); // may throw \Yana\Core\Exceptions\NotFoundException
            $database = new \Yana\Db\Mdb2\Connection($dbSchema);

            /* If no SQL file for the current $item does exist,
             * we need to call the appropriate \Yana\Db\Export\SqlFactory method
             * instead.
             */
            if (!is_readable($installFile)) {
                $sqlFactory = new \Yana\Db\Export\SqlFactory($database->getSchema());

                /* If the \Yana\Db\Export\SqlFactory class does not support the desired function.
                 */
                if (!method_exists($sqlFactory, $methodName)) {

                    if (!file_exists($installFile)) {
                        $message = "Unable to install database '$item'. There is no installation file available.";
                    } else {
                        $message = "Unable to install database '$item'. Cannot read sql file '$installFile'.";
                    }
                    $level = \Yana\Log\TypeEnumeration::WARNING;
                    throw new \Yana\Db\Queries\Exceptions\NotCreatedException($message, $level);

                /* Else, create and execute the SQL statements.
                 */
                } else {
                    /* create ... */
                    $sqlStmts = $sqlFactory->$methodName();
                    /* ... execute */
                    if ($database->importSQL($sqlStmts) === false) {
                        \Yana\Log\LogManager::getLogger()->addLog("Note: Unable to install database '$item'.");
                        continue;
                    }
                } /* end if */

            /* If a SQL file is available, always prefer using the SQL file.
             */
            } elseif ($database->importSQL($installFile) === false) {
                \Yana\Log\LogManager::getLogger()->addLog("Note: Unable to install database '$item'.");
                continue;

            /* If the SQL file has been imported successfully
             */
            } else {

                \Yana\Log\LogManager::getLogger()->addLog("SQL file '$installFile' has been imported.");

            } /* end if */

            /* initialize tables (store)
             */
            assert('!isset($initStmts); // Cannot redeclare var $initStmts');
            $initStmts = $dbSchema->getInit();
            if (is_array($initStmts) && !empty($initStmts)) {
                assert('!isset($parser); // Cannot redeclare var $parser');
                assert('!isset($stmt); // Cannot redeclare var $stmt');
                foreach ($initStmts as $stmt)
                {
                    $parser = new \Yana\Db\Queries\Parser($database);
                    try {
                        $initialization[] = $parser->parseSQL($stmt);
                    } catch (\Yana\Core\Exceptions\InvalidArgumentException $e) {
                        unset($e);
                        continue;
                    }
                }
                unset($stmt, $parser);
            }
            unset($initStmts);

        } /* end foreach */

        /* initialize tables (send)
         */
        foreach ($initialization as $stmt)
        {
            assert($stmt instanceof \Yana\Db\Queries\AbstractQuery);
            $result = $stmt->sendQuery();
            if (!$result) {
                $message = "Unable to initialize database.";
                $level = \Yana\Log\TypeEnumeration::WARNING;
                \Yana\Log\LogManager::getLogger()->addLog($message, $level);
                throw new \Yana\Db\Queries\Exceptions\NotCreatedException($message, $level);
            }
            $stmt->getDatabase()->commit(); // may throw exception
        }
    }

    /**
     * synchronize FileDb with online database
     *
     * @todo sort table definitions by foreign keys, so referenced tables are synchronized before the references
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    MESSAGE
     * @onsuccess   goto: GET_DB_CONFIGURATION
     * @onerror     goto: GET_DB_CONFIGURATION, text: Yana\Core\Exceptions\InvalidInputException
     * @safemode    true
     *
     * @access      public
     * @param       string  $dbms  type of DBMS
     * @param       array   $list  list of database schema to handle
     */
    public function db_sync($dbms, array $list)
    {
        @set_time_limit(500);

        if (!class_exists("MDB2")) {
            throw new \Yana\Db\Mdb2\PearDbException();
        }
        if (empty($dbms) || empty($list)) {
            /* nothing to do */
            $message = "No databases for target DBMS selected.";
            $level = \Yana\Log\TypeEnumeration::INFO;
            throw new \Yana\Core\Exceptions\Forms\MissingInputException($message, $level);
        }

        assert('!isset($dbSchema); // Cannot redeclare var $dbSchema');
        foreach ($list as $item)
        {
            /**
             * read database content
             */
            $dbSchema = \Yana\Files\XDDL::getDatabase($item);
            $db = new \Yana\Db\Mdb2\Connection($dbSchema);
            $fileDb = new \Yana\Db\FileDb\Connection($dbSchema);

            /**
             * prepare queries
             */
            $selectQuery = new \Yana\Db\Queries\Select($db);
            $selectQuery->useInheritance(false);
            $fileSelectQuery = new \Yana\Db\Queries\Select($fileDb);
            $fileSelectQuery->useInheritance(false);

            /* @var $table \Yana\Db\Ddl\Table */
            foreach ($dbSchema->getTables() as $table)
            {
                $tableName = $table->getName();
                /**
                 * select table
                 */
                $selectQuery->setTable($tableName);

                /**
                 * treat columns of types 'image' and 'file' as string
                 */
                $files = $table->getFileColumns();
                /* @var $column \Yana\Db\Ddl\Column */
                foreach ($files as $column)
                {
                    $column->setType('string');
                }

                /* get primary key */
                $primary_key = $table->getPrimaryKey();

                if ($db->exists($tableName)) {
                    $db_keys = $db->select("$tableName.*.$primary_key");
                } else {
                    $db_keys = array();
                }
                if (!is_array($db_keys)) {
                    $db_keys = array();
                }
                if ($fileDb->exists($tableName)) {
                    $file_keys = $fileDb->select("$tableName.*.$primary_key");
                } else {
                    $file_keys = array();
                }
                if (!is_array($file_keys)) {
                    $file_keys = array();
                }

                /**
                 * synchronize: database -> file
                 */
                $i = 0;
                foreach (array_diff($db_keys, $file_keys) as $key)
                {
                    $selectQuery->setRow($key);
                    try {
                        $fileDb->insert("$tableName.$key", $db->select($selectQuery));
                    } catch (\Exception $e) {
                        $message = "Unable to copy value $tableName.$key from database to FileDB";
                        \Yana\Log\LogManager::getLogger()->addLog($message);
                    }
                    if ($i > 20) { // safe point all 20 inserts
                        try {
                            $fileDb->commit(); // may throw exception
                            $i = 0;
                        } catch (\Exception $e) {
                            $message = "Failed to commit changes to FileDB.";
                            $level = \Yana\Log\TypeEnumeration::ERROR;
                            \Yana\Log\LogManager::getLogger()->addLog($message. " " . $e->getMessage(), $level);
                            throw new \Yana\Db\CommitFailedException($message, $level, $e);
                        }
                    } else {
                        $i++;
                    }
                }
                if ($i > 0) { // commit pending transaction
                    try {
                        $fileDb->commit(); // may throw exception
                    } catch (\Exception $e) {
                        $message = "Failed to commit changes to FileDB.";
                        $level = \Yana\Log\TypeEnumeration::ERROR;
                        \Yana\Log\LogManager::getLogger()->addLog($message. " " . $e->getMessage(), $level);
                        throw new \Yana\Db\CommitFailedException($message, $level, $e);
                    }
                }
                unset($i);

                /**
                 * synchronize: file -> database
                 */
                $i = 0;
                assert('!isset($diff); // Cannot redeclare var $diff');
                $diff = array_diff($file_keys, $db_keys);
                if (!empty($diff)) {
                    $fileSelectQuery->setTable($tableName);
                    $fileContent = $fileDb->select($fileSelectQuery);
                    foreach ($diff as $key)
                    {
                        try {
                            $db->insert("$tableName.$key", $fileContent[$key]);
                        } catch (\Exception $e) {
                            $message = "Unable to copy value $tableName.$key from FileDB to database";
                            $level = \Yana\Log\TypeEnumeration::WARNING;
                            \Yana\Log\LogManager::getLogger()->addLog($message, $level);
                            throw new \Yana\Db\Queries\Exceptions\NotCreatedException($message, $level);
                        }
                        if ($i > 20) { // safe point all 20 inserts
                            try {
                                $db->commit(); // may throw exception
                                $i = 0;
                            } catch (\Exception $e) {
                                \Yana\Log\LogManager::getLogger()->addLog("Failed to commit changes to Database.");
                                $level = \Yana\Log\TypeEnumeration::ERROR;
                                \Yana\Log\LogManager::getLogger()->addLog($message. " " . $e->getMessage(), $level);
                                throw new \Yana\Db\CommitFailedException($message, $level, $e);
                            }
                        } else {
                            $i++;
                        }
                        unset($fileContent[$key]);
                    }
                }
                unset($diff);
                if ($i > 0) {
                    try {
                        $db->commit(); // may throw exception
                    } catch (\Exception $e) {
                        \Yana\Log\LogManager::getLogger()->addLog("Failed to commit changes to Database.");
                        $level = \Yana\Log\TypeEnumeration::ERROR;
                        \Yana\Log\LogManager::getLogger()->addLog($message. " " . $e->getMessage(), $level);
                        throw new \Yana\Db\CommitFailedException($message, $level, $e);
                    }
                }
                unset($i);
            } /* end foreach */

        } /* end foreach */
    }

    /**
     * backup databases
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    MESSAGE
     * @onerror     goto: GET_DB_CONFIGURATION
     * @safemode    true
     *
     * @access      public
     * @param       string  $target_dbms  the DBMS to install databases on
     * @param       array   $list         a list of databases to install
     * @param       array   $options      a list of flags structure, data, zip
     * @throws      \Yana\Core\Exceptions\Forms\InvalidSyntaxException  when the chosen DBMS is not valid
     */
    public function db_backup($target_dbms, array $list, array $options)
    {
        $useStructure = !empty($options['structure']);
        $useData = !empty($options['data']);
        $useZip = !empty($options['zip']);
        $dbms = mb_strtoupper($target_dbms);
        $fileContents = "";

        /* Mapping the DBMS to the SQL export function in class \Yana\Db\Export\SqlFactory */
        switch ($dbms)
        {
            case 'DB2':
                $methodName  = 'createDB2';
            break;
            case 'ACCESS':
                $methodName  = 'createMSAccess';
            break;
            case 'MSSQL':
                $methodName  = 'createMSSQL';
            break;
            case 'MYSQL':
            case 'MYSQLI':
                $dbms = 'MySQL';
                $methodName  = 'createMySQL';
            break;
            case 'OCI8':
                $methodName  = 'createOracleDB';
            break;
            case 'PGSQL':
                $dbms = 'PostGreSQL';
                $methodName  = 'createPostgreSQL';
            break;
            case 'DBASE':
            case 'FBSQL':
            case 'IBASE':
            case 'IFX':
            case 'SYBASE':
            default:
                $message = "Chosen DBMS is invalid.";
                $level = \Yana\Log\TypeEnumeration::WARNING;
                $error = new \Yana\Core\Exceptions\Forms\InvalidSyntaxException($message, $level);
                throw $error->setValue($dbms)->setValid('DB2, MSSQL, MYSQL, OCI8, PGSQL')->setField('DBMS');
        }

        foreach ($list as $dbName)
        {
            if (is_string($dbName)) {
                $db = \Yana\Application::connect($dbName);
                $dbc = new \Yana\Db\Export\DataFactory($db);
                $arrayOfStmts = $dbc->$methodName($useStructure, $useData);
                $fileContents .= implode("\n", $arrayOfStmts) . "\n";
            }
        }

        $filename = mb_strtolower(preg_replace('/\W/', '_', $dbms) . '.sql');
        if (empty($fileContents)) {
            $message = 'Did not create SQL file because it is empty.';
            $code = \Yana\Log\TypeEnumeration::WARNING;
            $error = new \Yana\Core\Exceptions\Files\NotCreatedException($message, $code);
            throw $error->setFilename($filename);
        }

        header("Cache-Control: maxage=1"); // Bug in IE8 with HTTPS-downloads
        header("Pragma: public");
        header("Content-type: text/plain");
        if ($useZip) {
            $fileContents = gzencode($fileContents, 9);
            $filename .= '.gz';
        }
        header("Content-Disposition: attachment; filename={$filename}");
        header("Content-Length: " . strlen($fileContents));
        exit($fileContents);
    }

    /**
     * update database setup
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    MESSAGE
     * @onsuccess   goto: GET_DB_CONFIGURATION
     * @onerror     goto: GET_DB_CONFIGURATION, text: Yana\Core\Exceptions\InvalidInputException
     * @safemode    true
     *
     * @access      public
     * @param       string  $active       activate/deactivate connection
     * @param       string  $dbms         type of DBMS to use
     * @param       string  $host         host name
     * @param       string  $port         DBMS port
     * @param       string  $user         DBMS username
     * @param       string  $password     DBMS password
     * @param       string  $name         database name
     * @param       string  $prefix       table prefix
     * @param       bool    $autoinstall  install automatically?
     * @param       bool    $autosync     synchronize automatically?
     * @param       array   $list         list of database schemas
     */
    public function set_db_configuration($active, $dbms, $host = "", $port = "", $user = "", $password = "", $name = "",
        $prefix = "", $autoinstall = false, $autosync = false, array $list = array())
    {
        /**
         * 1) Test if connection is available
         */
        if ($active === 'true') {
            /* check input data */
            $dsn = array(
                'DBMS'     => $dbms,
                'HOST'     => $host,
                'PORT'     => $port,
                'USERNAME' => stripslashes($user),
                'PASSWORD' => stripslashes($password),
                'DATABASE' => stripslashes($name)
            );

            // Test if the connection settings are valid
            $test = \Yana\Db\Mdb2\ConnectionFactory::isAvailable($dsn);
            if ($test !== true) {
                $message = 'Unable to establish connection to database server. Check your input please!';
                $level = \Yana\Log\TypeEnumeration::WARNING;
                throw new \Yana\Db\ConnectionException($message, $level);
            }
        } else {
            /**
             * A connection to FileDB should always be available,
             * so there is no need to check that.
             * Instead we assume this test is always "true".
             */
        }

        /**
         * 2) Contact database server
         *
         * Install and synchronize the database with the current content of FileDb.
         *
         * This only needs to be done when a user activates / deactivates database support.
         * If the "active" setting is unchanged, then this may be skipped
         */
        if (YANA_DATABASE_ACTIVE && $active === 'true') {
            /* continue without changes */
        } elseif (!YANA_DATABASE_ACTIVE && $active === 'false') {
            /* continue without changes */
        } else {
            if ($active === 'true') {
                if ($autoinstall) {
                    try {
                        $this->db_install($dbms, $list);
                    } catch (\Exception $e) {
                        \Yana\Log\LogManager::getLogger()->addLog('Notice: installation of tables failed. ' . $e->getMessage());
                        throw $e;
                    }
                } else {
                    /* continue without changes */
                }
            } else {
                /* continue (FileDB does not need a database installation) */
            }
            if ($autosync) {
                try {
                    $this->db_sync($dbms, $list);
                } catch (\Exception $e) {
                    \Yana\Log\LogManager::getLogger()->addLog('Unable to install tables of plugin "user" with the " .
                        "choosen dbms. Operation aborted. ' . $e->getMessage());
                    throw $e;
                }
            } else {
                /* continue without changes */
            }
        } /* end if */

        /**
         * 3) Write changes to file
         */
        $text = "<?php
        if (!defined('YANA_DATABASE_ACTIVE'))   define('YANA_DATABASE_ACTIVE', $active);
        if (!defined('YANA_DATABASE_DBMS'))     define('YANA_DATABASE_DBMS', \"$dbms\");
        if (!defined('YANA_DATABASE_HOST'))     define('YANA_DATABASE_HOST', \"$host\");
        if (!defined('YANA_DATABASE_PORT'))     define('YANA_DATABASE_PORT', \"$port\");
        if (!defined('YANA_DATABASE_USER'))     define('YANA_DATABASE_USER', \"$user\");
        if (!defined('YANA_DATABASE_PASSWORD')) define('YANA_DATABASE_PASSWORD', \"$password\");
        if (!defined('YANA_DATABASE_PREFIX'))   define('YANA_DATABASE_PREFIX', \"$prefix\");
        if (!defined('YANA_DATABASE_NAME'))     define('YANA_DATABASE_NAME', \"$name\");\n?>";
        assert('!isset($file); // Cannot redeclare var $file');
        /* @var $file \Yana\Files\Text */
        $file = $GLOBALS['YANA']->getPlugins()->{"db_admin:/dbconfig.text"};
        if (!$file->exists()) {
            $file->create();
        }
        $file->read();
        $file->setContent($text);
        $file->failSafeWrite();
    }

}

?>