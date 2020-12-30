<?php
$HOOKS[HOOK_PREINSTALL] = function()
{
    /**
     * check version of PHP
     */
    if (!defined('PHP_VERSION')) {
        define('PHP_VERSION', phpversion());
    }
    /**
     * calculate memory limit
     */
    $memoryLimit = strtoupper(ini_get('memory_limit'));
    $memoryLimitInMb = (int) $memoryLimit;
    if (strpos($memoryLimit, 'K') !== false) {
        $memoryLimitInMb /= 1024;
    } elseif (strpos($memoryLimit, 'G') !== false) {
        $memoryLimitInMb *= 1024;
    }

    switch (false)
    {
        case extension_loaded('mbstring'):
            die("Error: This program requires the 'mbstring' extension. To fix this please activate the extension in the 'php.ini' file of your server. If you don't do this, installation cannot continue.");
        break;

        case extension_loaded('zlib'):
            die("Error: This program requires the 'zlib' extension. If you don't install this library, installation cannot continue.");
        break;

        case class_exists('\SimpleXMLElement'):
            die("Error: This program requires the 'simplexml' extension. If you don't install this library, installation cannot continue.");
        break;

        case extension_loaded('xml'):
            die("Error: This program requires the 'xml' (XML Parser) library. If you don't install this library, installation cannot continue.");
        break;

        /**
         * 4) check memory limit
         */
        case $memoryLimitInMb <= 0 || $memoryLimitInMb >= 16:
            die("Error: The memory limit of your server is $memoryLimit. This application requires at least 32MB RAM. To fix this please change the setting 'memory_limit' in the 'php.ini' file of your server. If you don't do this, installation cannot continue.");
        break;

        /**
         * 5) check php version
         */
        case version_compare(PHP_VERSION, '7.2.0') >= 0:
            die("Error: Your version of PHP (" . PHP_VERSION . ") is older than the minimum required version 7.2. Be advised that you should update before you continue with the installation.");
        break;

        /**
         * 6) check warnings and notices
         *
         * 6.1) Outdated PHP version
         */
        case version_compare(PHP_VERSION, '7.3.0') >= 0:
            print "<p>Note: Your version of PHP is reaching its end of life by December this year. Consider upgrading your installation of PHP to ensure your installation remains safe.</p>\n";
        // fall through

        default:
            /**
             * 6.2) memory limit
             */
            if ($memoryLimitInMb > 0 && $memoryLimitInMb < 32) {
                print "<p>Warning: The memory limit of your server is $memoryLimit. This application requires at least 32MB RAM. This means that you may run into an out-of-memory error. You can prevent this, by raising the setting 'memory_limit' in the 'php.ini' file of your server. If you don't do this, be warned that you may not be able to run the application.</p>";

            }
        break;
    } /* end switch */
    return true;
};
$HOOKS[HOOK_DATABASE] = function(string $dbms, string $host, string $port, string $user, string $pass, string $name)
{
    if (!is_dir(INSTALL_DIR)) {
        print "The program has not yet been installed. Complete installation first before updating the password!";
        return false;
    }
    @set_time_limit(500);

    print "<h1>Deploying database</h1>\n<p>started at " . date("r") . "</p>\n";
    chdir(INSTALL_DIR);
    error_reporting(~E_DEPRECATED);
    require_once 'library.php';
    $builder = new \Yana\ApplicationBuilder();
    $builder->setErrorReporting(YANA_ERROR_OFF);
    $application = $builder->buildApplication();

    $dsn = array(
        'DBMS'     => $dbms,
        'HOST'     => $host,
        'PORT'     => $port,
        'USERNAME' => $user,
        'PASSWORD' => $pass6,
        'DATABASE' => $name
    );

    if (!\Yana\Db\Mdb2\ConnectionFactory::isMdb2Available() && !\Yana\Db\Doctrine\ConnectionFactory::isDoctrineAvailable()) {
        print "<p>No suitable database driver available. This application needs either PEAR MDB2 or Doctrine DBAL for database support.</p>";
        return;
    }
    if (!\Yana\Db\ConnectionFactory::isAvailable($dsn)) {
        print "<p>Connection to database failed.</p>";
        return;
    }

    // The "db" directory is where we keep XML files that can be used to create SQL files
    $dbDirectory = 'yana/config/db/';
    // The "install" directory is where we keep the actual SQL files, not all are created in advance
    $installDirectory = $dbDirectory . 'install/' . preg_replace('/[^a-zA-Z0-9_]/gs', '', $dbms) . '/';

    if (!$application->execute("check_login", array("user" => "administrator"))) {
        print "<p>Access denied - perhaps the password has already been set?<br>Operation aborted.</p>\n";
        return;
    }

    $params = array(
        "active" => "true",
        "dbms" => $dbms,
        "host" => $host,
        "port" => $port,
        "user" => $user,
        "password" => $pass,
        "name" => $name,
        "autoinstall" => false,
        "autosync" => false,
        "list" => \Yana\Db\Ddl\DDL::getListOfFiles()
    );
    if (
        !$application->execute("db_install", $params) ||
        !$application->execute("db_sync", $params) ||
        !$application->execute("set_db_configuration", $params)) {

        print "<p>Unable to set database connection. Is the database perhaps not empty?</p>\n";
    } else {
        print "<p>Database configuration saved.</p>\n";
    }
    $application->execute('logout');
};
$HOOKS[HOOK_LDAP] = function(string $hostName)
{
    if (!is_dir(INSTALL_DIR)) {
        print "The program has not yet been installed. Complete installation first before updating the password!";
        return false;
    }
    if (empty($hostName)) {
        print "Invalid input, cannot set database.";
        return false;
    }
    print "<h1>Adding LDAP configuration</h1>\n<p>started at " . date("r") . "</p>\n";
    chdir(INSTALL_DIR);
    error_reporting(~E_DEPRECATED);
    require_once 'library.php';
    $builder = new \Yana\ApplicationBuilder();
    $builder->setErrorReporting(YANA_ERROR_OFF);
    $application = $builder->buildApplication();
    $connection = $application->connect('user');
    $row = array('auth_name' => 'ldap', 'auth_method' => 'ldap', 'auth_host' => $hostName);
    try {
        $connection->insert('authenticationprovider', $row)->commit();
        print "<p>Changes saved successfully.</p>\n";
    } catch (\Exception $e) {
        print "<p>Failed to store LDAP configuration. " . $e->getMessage() . "</p>\n";
    }
};
$HOOKS[HOOK_ADMIN] = function(string $pass)
{
    if (!is_dir(INSTALL_DIR)) {
        print "The program has not yet been installed. Complete installation first before updating the password!";
        return false;
    } else {
        if (empty($pass)) {
            print "Invalid input, installation aborted.";
            return false;
        } else {
            chdir(INSTALL_DIR);
            error_reporting(~E_DEPRECATED);
            require_once 'library.php';
            $builder = new \Yana\ApplicationBuilder();
            $builder->setErrorReporting(YANA_ERROR_OFF);
            $application = $builder->buildApplication();
            print "<h1>Updating password settings</h1>\n<p>started at " . date("r") . "</p>\n";

            if (!$application->execute("check_login", array("user" => "administrator"))) {
                unset($e);
                print "<p>Access denied - perhaps the password has already been set?<br>Operation aborted.</p>\n";
                return false;
            }

            if (!$application->execute("set_pwd", array("new_pwd" => $pass, "repeat_pwd" => $pass))) {
                print "<p>Unable to change password. Check your input.<br>Operation aborted.</p>\n";
                return false;
            }

            print "<p>Changes saved successfully.</p>\n";
            $application->execute('logout');
            return true;
        }
    }
};

$HOOKS[HOOK_TEST] = function($showDetails)
{
    if (!is_dir(INSTALL_DIR)) {
        print "The program has not yet been installed. Complete installation first before running the self test!";
        return false;
    } else {
        chdir(INSTALL_DIR);
        error_reporting(~E_DEPRECATED);
        require_once 'library.php';
        $builder = new \Yana\ApplicationBuilder();
        $builder->setErrorReporting(YANA_ERROR_OFF);
        $application = $builder->buildApplication();
        $diagnostics = new \Plugins\Diagnostics\DiagnosticsHelper($application);

        print $diagnostics->runDiagnosticsReportAsHtml($showDetails);
        return true;
    }
};

$HOOKS[HOOK_INSTALLATION_COMPLETE] = function()
{
    if (!is_dir(INSTALL_DIR)) {
        print "The program has not been installed properly. Unable to init the framework!";
        return false;
    } else {
        chdir(INSTALL_DIR);

        print "<h1>Register installed plugins</h1>\n";
        error_reporting(~E_DEPRECATED);
        require_once 'library.php';
        $builder = new \Yana\ApplicationBuilder();
        $builder->setErrorReporting(YANA_ERROR_OFF);
        $application = $builder->buildApplication();
        try {
            $application->refreshSettings();
            print "<p>Configuration updated successfully.</p>\n";
            return true;

        } catch (\Exception $e) {
            unset($e);
            print "<p>Error while trying to refresh the list of installed plugins.</p>\n";
            return false;
        }
    }
};

$HOOKS[HOOK_TERMINATE_PROGRAM] = function($doRelocate, $baseURI)
{
    if (!is_dir(INSTALL_DIR)) {
        exit("The program has not been installed properly. Unable to start the framework!");
    } else {
        if ($doRelocate && !headers_sent()) {
            header("Location: {$baseURI}index.php");
            return false;
        } else {
            print "<a href=\"{$baseURI}index.php\">Click here to run the application now</a>";
            return true;
        }
    }
};
