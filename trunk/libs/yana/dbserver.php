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
 * Connection to a database server
 *
 * This class provides methods to establish or test
 * connections with database servers.
 *
 * @access      public
 * @name        DbServer
 * @package     yana
 * @subpackage  database
 */
class DbServer extends \Yana\Core\Object
{

    /**#@+
     * @ignore
     * @access  private
     */

    /** @var mixed */ private $_database = null;
    /** @var array */ private $_dsn      = array();
    /** @var array */ private $_options  = array();

    /**#@-*/

    /**
     * create a new instance
     *
     * The $dsn parameter became available in version 2.8
     * It is an array containing the following information (all entries are optional):
     *
     * <ul>
     *     <li><pre> string  ( DBMS )     default =  mysql      name of the php-dbms api to be used
     *                                            e.g. mysql, mysqli, db2, ... </pre></li>
     *     <li><pre> string  ( HOST )     default =  localhost  adress of the host e.g. localhost,
     *                                            123.456.789.0,
     *                                            COMPUTER-NAME\DB-INSTANCE (windows+MS-SQL)</pre></li>
     *     <li><pre> integer ( PORT )     default =  0          port number </pre></li>
     *     <li><pre> string  ( USERNAME ) default =  root </pre></li>
     *     <li><pre> string  ( PASSWORD ) default =  n/a </pre></li>
     *     <li><pre> string  ( DATABASE ) default =  yana       name of the database </pre></li>
     * </ul>
     *
     * The default settings may be changed by editing file config/system.config in key DEFAULT.DATABASE.DSN.
     * The parameter $dsn has the following fall-back behaviour: $dsn -> global user settings -> yana default settings
     *
     * {@internal
     *
     * Note on PEAR-DB connection parameters
     *
     * <ul>
     * <li> phptype:    Database backend used in PHP (i.e. mysql  , odbc etc.) </li>
     * <li> dbsyntax:   Database used with regards to SQL syntax etc.
     *                  When using ODBC as the phptype, set this to the DBMS type the ODBC driver is connecting to.
     *                  Examples: access, db2, mssql, navision, solid, etc. </li>
     * <li> protocol:   Communication protocol to use ( i.e. tcp, unix etc.) </li>
     * <li> hostspec:   Host specification (hostname[:port]) </li>
     * <li> database:   Database to use on the DBMS server </li>
     * <li> username:   User name for login </li>
     * <li> password:   Password for login </li>
     * <li> proto_opts: Maybe used with protocol </li>
     * <li> option:     Additional connection options in URI query string format. options get separated by & </li>
     * </ul>
     *
     * Note on PEAR-DB connection options
     *
     * <ul>
     * <li> autofree:       should results be freed automatically when there are no more rows? </li>
     * <li> debug:          debug level </li>
     * <li> persistent:     should the connection be persistent? </li>
     * <li> portability:    portability mode constant. These constants are bitwised,
     *                      so they can be combined using | and removed using ^.
     *                      See the examples below and the "Intro - Portability" for more information. </li>
     * <li> seqname_format: the sprintf() format string used on sequence names.
     *                      This format is applied to sequence names passed to createSequence(),
     *                      nextID() and dropSequence(). </li>
     * <li> ssl:            use ssl to connect? </li>
     * </ul>
     *
     * }}
     *
     * @name   DbServer::__consruct()
     * @param  array  $dsn  for a description of the $dsn parameter see the text above
     * @throws PearDbError  when Pear MDB2 is not available
     */
    public function __construct(array $dsn = null)
    {
        if (!class_exists("MDB2")) {
            /* error handling */
            Log::report("Unable to open connection to database using PEAR-DB. Might not be installed.");
            throw new PearDbError();
            $this->_database = null;
        }

        /*
         * 1 retrieve default settings for fall back
         */

        // get list of ODBC-settings
        $require_odbc = Yana::getDefault('database.require_odbc');
        if (!is_array($require_odbc)) {
            // no ODBC-settings available
            $require_odbc = array();
        }
        assert('is_array($require_odbc);');

        // get list of default connection options
        $this->_options = Yana::getDefault('database.options');
        if (!is_array($this->_options)) {
            // no default options available
            $this->_options = array();
        }
        assert('is_array($this->_options);');

        // get connection settings
        $this->_dsn = array('DBMS'     => YANA_DATABASE_DBMS,
                           'HOST'     => YANA_DATABASE_HOST,
                           'PORT'     => YANA_DATABASE_PORT,
                           'USERNAME' => YANA_DATABASE_USER,
                           'PASSWORD' => YANA_DATABASE_PASSWORD,
                           'DATABASE' => YANA_DATABASE_NAME);

        /*
         * 1.2 auto-detect MySQL port for Server2Go application
         */
        $dbms = strpos(YANA_DATABASE_DBMS, 'mysql');
        if (isset($_ENV["S2G_MYSQL_PORT"]) && empty($this->_dsn["PORT"]) && $dbms !== false) {
            $this->_dsn["PORT"] = $_ENV["S2G_MYSQL_PORT"];
        }

        /*
         * 1.3 there are some static options that always have to be there and can't be changed
         */
        $this->_options['portability'] = MDB2_PORTABILITY_ALL;

        /*
         * 2 process settings provided by the user
         */
        if (is_array($dsn)) {
            $dsn = \Yana\Util\Hashtable::changeCase($dsn, CASE_UPPER);
            $this->_dsn = \Yana\Util\Hashtable::merge($this->_dsn, $dsn);
        }
        assert('is_array($this->_dsn);');
        $dsn = array();

        /*
         * 3 create PEAR-DB compatible dsn-array
         */
        /* 3.1 determine if odbc is required to connect to this dbms */
        if (!empty($this->_dsn['DBMS'])) {
            if (@$this->_dsn['USE_ODBC'] == true || in_array(mb_strtolower($this->_dsn['DBMS']), $require_odbc)) {
                $dsn['phptype']  = 'ODBC';
                $dsn['dbsyntax'] = $this->_dsn['DBMS'];
            } else {
                $dsn['phptype']  = $this->_dsn['DBMS'];
            }
        }
        /* 3.2 now for the database host */
        if (!empty($this->_dsn['HOST'])) {
            $dsn['hostspec'] = $this->_dsn['HOST'];
            if (is_numeric($this->_dsn['PORT']) && $this->_dsn['PORT'] > 0) {
                $dsn['hostspec'] .= ':'.$this->_dsn['PORT'];
            }
        }
        /* 3.3 database name */
        if (!empty($this->_dsn['DATABASE'])) {
            $dsn['database'] = $this->_dsn['DATABASE'];
        }
        /* 3.4 collect login information */
        if (!empty($this->_dsn['USERNAME'])) {
            $dsn['username'] = $this->_dsn['USERNAME'];
        }
        if (!empty($this->_dsn['PASSWORD'])) {
            $dsn['password'] = $this->_dsn['PASSWORD'];
        }
        assert('is_array($dsn);');

        /**
         * 4 if PEAR-DB is available try to connect using the collected settings
         *
         * {@internal
         *
         * Note:
         * Might be a bug - might be a "feature".
         * PEAR-DB does not accept foreign error handlers to be used - at least not
         * while establishing a database connection.
         * Restoring the error handler and then reseting it somehow makes things right
         * for PEAR.
         *
         * The returned object is an instance of DB_common or an instance of DB_Error.
         *
         * }}
         */
        if (defined('YANA_ERROR_REPORTING')) {
            restore_error_handler();
        }
        $this->_database = MDB2::connect($dsn);
        if (defined('YANA_ERROR_REPORTING')) {
            ErrorUtility::setErrorReporting(YANA_ERROR_REPORTING);
        }

        /* error handling */
        if (!MDB2::isConnection($this->_database)) {
            $err_msg = "DATABASE NOT AVAILABLE: Unable to establish connection with database server.\n\t\t".
                        "Open administration panel and choose \"database administration\" to edit settings";
            /* Don't output passwords */
            if (empty($dsn['password'])) {
                $dsn['password'] = 'NO';
            } else {
                $dsn['password'] = 'YES';
            }
            $data = $this->_database->getMessage() . "\nUsing DSN:\n" . print_r($dsn, true);
            Log::report($err_msg, E_USER_NOTICE, $data);
            throw new Warning($err_msg.': '.$data, E_USER_WARNING);
        }
    }

    /**
     * alias of DbServer::getConnection()
     *
     * See {@link DbServer::getConnection()} for details.
     *
     * @access  public
     * @name    DbServer::get()
     * @return  mixed
     * @deprec  since 3.5
     */
    public function &get()
    {
        return $this->getConnection();
    }

    /**
     * get a PEAR-DB connection object
     *
     * This function returns an open database connection via PEAR-DB.
     * The returned values are:
     *
     * <ul>
     *   <li><pre> null               = if PEAR-MDB2 was not found </pre></li>
     *   <li><pre> MDB2_Error         = if the connection failed </pre></li>
     *   <li><pre> MDB2_Driver_Common = if the connection was established successfully </pre></li>
     * </ul>
     *
     * @access  public
     * @name    DbServer::getConnection()
     * @return  MDB2_Driver_Common
     */
    public function getConnection()
    {
        if (!class_exists("MDB2")) {
            return null;
        } else {
            return $this->_database;
        }
    }

    /**
     * get the DSN
     *
     * This function returns an associative array containing
     * information on the current connection:
     *
     * <ul>
     *   <li>USE_ODBC: true, if ODBC is used to connect to the database</li>
     *   <li>DBMS: name of used database system</li>
     *   <li>HOST: host name, e.g. localhost</li>
     *   <li>PORT: port number of database server (may be empty)</li>
     *   <li>USERNAME</li>
     *   <li>PASSWORD</li>
     *   <li>DATABASE: name of database to connect to</li>
     * </ul>
     *
     * Returns bool(false) on error.
     *
     * @access  public
     * @name    DbServer::getDsn()
     * @return  array
     * @since   2.9.8
     */
    public function getDsn()
    {
        if (is_array($this->_dsn)) {
            return $this->_dsn;

        } else {
            return false;
        }
    }

    /**
     * test if a connection is available
     *
     * Returns bool(true) if a connection to a db-server could be established via the provided parameters,
     * and bool(false) otherwise. See the constructor method for details on the $dsn parameter
     *
     * @access  public
     * @static
     * @name    DbServer::isAvailable()
     * @param   array  $dsn   dns (info data for connection)
     * @return  bool
     *
     * @see     DbServer::__consruct()
     */
    public static function isAvailable(array $dsn)
    {
        /* load PEAR-DB */
        @include_once "MDB2.php";
        if (!class_exists("MDB2")) {
            return false;
        }
        $db = new DbServer($dsn);
        /*
         * NOTE: the constructor already created a log-entry if the connection failed,
         * so there's no need to do this again
         */
        return MDB2::isConnection($db->_database);
    }

}

?>