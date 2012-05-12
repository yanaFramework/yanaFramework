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
class plugin_db_admin extends StdClass implements IsPlugin
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
        $yana = Yana::getInstance();

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
     * @onerror     goto: GET_DB_CONFIGURATION, text: InvalidInputWarning
     * @safemode    true
     *
     * @access      public
     * @param       string  $dbms    type of DBMS
     * @param       array   $list    list of database schemas
     * @param       bool    $silent  mute error messages
     * @return      bool
     */
    public function db_install($dbms, array $list, $silent = false)
    {
        global $YANA;

        if (!class_exists("MDB2") && !$silent) {
            throw new \Yana\Db\Mdb2\PearDbException();
        }

        if (empty($dbms) || empty($list)) {
            /* invalid option - the choosen dbms is unknown */
            if (!$silent) {
                throw new InvalidInputWarning();
            }
        } else {
            $dbms = mb_strtoupper($dbms);
            $dbList = $list;
        }

        /* Mapping the DBMS to the SQL export function in class \Yana\Db\Export\SqlFactory */
        switch ($dbms)
        {
            case 'DBASE':
            case 'FBSQL':
            case 'IBASE':
            case 'IFX':
            case 'SYBASE':
                $method_name  = null;
            break;
            case 'DB2':
                $method_name  = 'createDB2';
            break;
            case 'ACCESS':
                $method_name  = 'createMSAccess';
            break;
            case 'MSSQL':
                $method_name  = 'createMSSQL';
            break;
            case 'MYSQL':
            case 'MYSQLI':
                $dbms         = 'MYSQL';
                $method_name  = 'createMySQL';
            break;
            case 'OCI8':
                $method_name  = 'createOracleDB';
            break;
            case 'PGSQL':
                $method_name  = 'createPostgreSQL';
            break;
            default:
                throw new InvalidInputWarning();
            break;
        }

        /* Mapping the DBMS to it's installation directory */
        $installDirectory = $YANA->getResource('system:/dbinstall/' . mb_strtolower($dbms));
        if ($installDirectory === false) {
            /* invalid option - the choosen dbms is unknown */
            \Yana\Log\LogManager::getLogger()->addLog("Unable to install database. The choosen DBMS '${dbms}' is unknown.");
            if (!$silent) {
                throw new InvalidInputWarning();
            }
            return false;
        }

        /* we assume the class \Yana\Db\Export\SqlFactory has the desired method. This will be tested later! */

        if (!$installDirectory->exists() && is_null($method_name)) {
            if (!$silent) {
                throw new Error("There is no installation file for this dbms available.");
            }
            return false;
        }

        /* get the list of available installation files */
        $installDirectory = $installDirectory->getPath();

        assert('!isset($initialization); // Cannot redeclare var $initialization');
        $initialization = array();

        foreach ($dbList as $item)
        {
            /* check the input */
            if (!is_string($item)) {
                if (!$silent) {
                    throw new InvalidInputWarning();
                }
                return false;
            } else {
                $item = mb_strtolower($item);
            }

            $installFile = $installDirectory . $item . '.sql';
            $dbSchema = \Yana\Files\XDDL::getDatabase($item);
            $database = new \Yana\Db\Mdb2\Connection($dbSchema);

            /* If no SQL file for the current $item does exist,
             * we need to call the appropriate \Yana\Db\Export\SqlFactory method
             * instead.
             */
            if (!is_readable($installFile)) {
                $sqlFactory = new \Yana\Db\Export\SqlFactory($database->getSchema());

                /* If the \Yana\Db\Export\SqlFactory class does not support the desired function.
                 */
                if (!method_exists($sqlFactory, $method_name)) {

                    if (!$silent) {
                        if (!file_exists($installFile)) {
                            throw new Error("Unable to install database. " .
                                "There is no installation file available for database '$item'.");
                        } else {
                            throw new Error("Unable to install database '$item'. " .
                                "Cannot read sql file '$installFile'.");
                        }
                    }
                    return false;

                /* Else, create and execute the SQL statements.
                 */
                } else {
                    /* create ... */
                    $sqlStmts = $sqlFactory->$method_name();
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
            if (!$stmt->sendQuery()) {
                trigger_error($stmt->db->getMessage(), E_USER_WARNING);
                return false;
            } else {
                $stmt->getDatabase()->commit();
            }
        }

        return true;
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
     * @onerror     goto: GET_DB_CONFIGURATION, text: InvalidInputWarning
     * @safemode    true
     *
     * @access      public
     * @param       string  $dbms    type of DBMS
     * @param       array   $list    list of database schema to handle
     * @param       bool    $silent  mute error messages
     * @return      bool
     */
    public function db_sync($dbms, array $list, $silent = false)
    {
        @set_time_limit(500);

        if (!class_exists("MDB2")) {
            if (!$silent) {
                throw new \Yana\Db\Mdb2\PearDbException();
            }
            return false;
        }
        if (empty($dbms) || empty($list)) {
            return false;
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
                    if (!$fileDb->insert("$tableName.$key", $db->select($selectQuery))) {
                        if (!$silent) {
                            $message = "Unable to copy value $tableName.$key from database to FileDB";
                            \Yana\Log\LogManager::getLogger()->addLog($message);
                        }
                        return false;
                    } else if ($i > 20) {
                        if ($fileDb->commit()) {
                            $i = 0;
                        } else {
                            if (!$silent) {
                                \Yana\Log\LogManager::getLogger()->addLog("Failed to commit changes to FileDB.");
                            }
                            return false;
                        }
                    } else {
                        $i++;
                    }
                }
                if ($i > 0 && !$fileDb->commit()) {
                    if (!$silent) {
                        \Yana\Log\LogManager::getLogger()->addLog("Failed to commit changes to FileDB.");
                    }
                    return false;
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
                        if (!$db->insert("$tableName.$key", $fileContent[$key])) {
                            if (!$silent) {
                                $message = "Unable to copy value $tableName.$key from FileDB to database";
                                \Yana\Log\LogManager::getLogger()->addLog($message);
                            }
                            return false;
                        } else if ($i > 20) {
                            if ($db->commit()) {
                                $i = 0;
                            } else {
                                if (!$silent) {
                                    \Yana\Log\LogManager::getLogger()->addLog("Failed to commit changes to Database.");
                                }
                                return false;
                            }
                        } else {
                            $i++;
                        }
                        unset($fileContent[$key]);
                    }
                }
                unset($diff);
                if ($i > 0 && !$db->commit()) {
                    if (!$silent) {
                        \Yana\Log\LogManager::getLogger()->addLog("Failed to commit changes to Database.");
                    }
                    return false;
                }
                unset($i);
            } /* end foreach */

        } /* end foreach */

        return true;
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
                $error = new InvalidValueWarning();
                throw $error->setField('DBMS=' . $dbms);
        }

        foreach ($list as $dbName)
        {
            if (is_string($dbName)) {
                $db = Yana::connect($dbName);
                $dbc = new \Yana\Db\Export\DataFactory($db);
                $arrayOfStmts = $dbc->$methodName($useStructure, $useData);
                $fileContents .= implode("\n", $arrayOfStmts) . "\n";
            }
        }

        $filename = mb_strtolower(preg_replace('/\W/', '_', $dbms) . '.sql');
        if (empty($fileContents)) {
            $error = new FileNotCreatedError();
            throw $error->setFilename($filename);
        }

        header("Cache-Control: maxage=1"); // Bug in IE8 with HTTPS-downloads
        header("Pragma: public");
        header("Content-type: text/plain");
        if ($useZip) {
            $fileContents = gzencode($fileContents, 9);
            $filename .= '.gz';
        }
        header("Content-Disposition: attachment; filename=${filename}");
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
     * @onerror     goto: GET_DB_CONFIGURATION, text: InvalidInputWarning
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
     * @param       bool    $silent       mute error messages
     */
    public function set_db_configuration($active, $dbms, $host = "", $port = "", $user = "", $password = "", $name = "", $prefix = "", $autoinstall = false, $autosync = false, $silent = false)
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

            $test = \Yana\Db\Mdb2\ConnectionFactory::isAvailable($dsn);
            if ($test !== true) {
                throw new Error('Unable to establish connection to database server. " .
                    "Check your input please!');
            } else {
                /* all fine - proceed */
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
            $silent = true;
            if ($active === 'true') {
                if ($autoinstall) {
                    $test = $this->db_install($ARGS);
                    if ($test !== true) {
                        \Yana\Log\LogManager::getLogger()->addLog('Notice: installation of tables failed. " .
                            "(May be tables already exist?)');
                        throw new Error('Installation of tables failed. " .
                            "(Do tables already exist?)');
                    } else {
                        /* success - proceed to next step */
                    }
                } else {
                    /* continue without changes */
                }
            } else {
                /* continue (FileDB does not need a database installation) */
            }
            if ($autosync) {
                $test = $this->db_sync($ARGS);
                if ($test !== true) {
                    \Yana\Log\LogManager::getLogger()->addLog('Unable to install tables of plugin "user" with the " .
                        "choosen dbms. Operation aborted.');
                    throw new Error();
                } else {
                    /* success - proceed to next step */
                } /* end if */
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
        /* @var $file TextFile */
        $file = $GLOBALS['YANA']->getPlugins()->{"db_admin:/dbconfig.textfile"};
        if (!$file->exists()) {
            $file->create();
        }
        $file->read();
        $file->setContent($text);
        $file->failSafeWrite();
    }

}

?>