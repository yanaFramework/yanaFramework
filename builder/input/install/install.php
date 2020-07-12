<?php
/**
 * Yana PHP-Framework: automatic installer
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
 * @author      Thomas Meyer <tm@yanaframework.net>
 * @link        http://www.yanaframework.net
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @copyright   2020 Thomas Meyer
 * @package     yana
 * @subpackage  install
 */
declare(strict_types=1);

namespace Yana\Install;

error_reporting(E_ALL & ~E_NOTICE);

/**#@+
 * Installer configuration
 */

/** current working directory */
define('CWD', dirname(__FILE__).'/');
/** installation directory */
define('INSTALL_DIR', 'yana/');
/** installation package */
define('INSTALL_ZIP', 'install.pak');
/** name of installation script */
define('INSTALL_SCRIPT', basename(__FILE__));
/** installation log (filename) */
define('LOG', CWD.'install_log.html');
/** package containing templates */
define('DATA_ZIP', 'yana.zip');
/** name of index page */
define('DATA_TOC', 'page_toc.html');
/** name of blank template */
define('DATA_INSTALL', CWD.'page_install.html');
/** name of definition file for packages */
define('PAK', 'pak.inc');
/** name of definition file for translated strings */
define('STRINGS', 'strings.inc');
/** name of definition file for hook-points */
define('HOOKS', 'hooks.inc');

/* NO NEED TO CHANGE ANYTHING BELOW THIS LINE !!! */

/**#@-*/
/**#@+
 * for internal use only - do not change
 *
 * @ignore
 */

define('LABEL', 0);
define('FILES', 1);
define('IS', 2);
define('DISABLED', 0);
define('ENABLED', 1);
define('MANDATORY', 2);
define('FOLDER', 3);
define('HOOK_PREINSTALL', 0);
define('HOOK_TEST', 1);
define('HOOK_ADMIN', 2);
define('HOOK_INSTALLATION_COMPLETE', 3);
define('HOOK_TERMINATE_PROGRAM', 4);
define('HOOK_DATABASE', 5);
define('HOOK_LDAP', 6);

/**#@-*/

/**
 * <<utility>> Installation Wizard
 *
 * @package     yana
 * @subpackage  install
 */
class InstallUtility
{

    private const OPT_ACTION = 'action';
    private const OPT_LANG = 'language';
    private const OPT_COMPONENTS = 'components';
    private const OPT_PASS = 'pass';
    private const OPT_DETAILS = 'details';
    private const OPT_START = 'start';
    private const OPT_ACTIVE = 'active';
    private const OPT_HOST = 'host';
    private const OPT_PORT = 'port';
    private const OPT_USER = 'user';
    private const OPT_DBMS = 'dbms';
    private const OPT_DB_NAME = 'name';

    private const DEFAULT_LANG = 'en';

    private const ACTION_INSTALL = 'install';
    private const ACTION_ADMIN = 'admin';
    private const ACTION_TEST = 'test';
    private const ACTION_ABORT = 'abort';
    private const ACTION_CLEAN = 'cleanup';
    private const ACTION_LDAP = 'ldap';
    private const ACTION_DATABASE = 'database';

    /**
     * Main program.
     *
     * @param  array  $options
     */
    public static function main(array $options)
    {
        if (!isset($options[self::OPT_ACTION])) {

            self::check();
            if (is_array($options) && isset($options[self::OPT_LANG]) && preg_match('/^\w{2}$/s', $options[self::OPT_LANG])) {
                $choosenLanguage = $options[self::OPT_LANG];
            } elseif (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && is_string($_SERVER['HTTP_ACCEPT_LANGUAGE']) && preg_match('/^[a-z]{2}/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                $choosenLanguage = strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
            } else {
                $choosenLanguage = self::DEFAULT_LANG;
            }
            self::showPage($choosenLanguage);
            return;
        }
        switch ($options[self::OPT_ACTION])
        {
            case self::ACTION_INSTALL:
                @set_time_limit(500);
                ob_start();
                print "<h1>Installation</h1>\n";
                if (isset($options[self::OPT_COMPONENTS]) && is_array($options[self::OPT_COMPONENTS])) {
                    $result = self::unzipComponents($options[self::OPT_COMPONENTS]);
                } else {
                    $result = self::unzipFiles(DATA_ZIP, false);
                }
                $stdout = ob_get_contents();
                ob_end_clean();
                self::writeLog($stdout);
                if (empty($result)) {
                    print "0";
                } else {
                    print "1";
                }
                print str_replace(CWD, '', LOG);
                exit;
            break;

            case self::ACTION_ADMIN:
                if (isset($options[self::OPT_PASS]) && is_string($options[self::OPT_PASS])) {
                    $pass = preg_replace('/\s/s', ' ', $options[self::OPT_PASS]);
                    self::adminAction($pass);
                }
            break;

            case self::ACTION_TEST:
                self::testAction(!empty($options[self::OPT_DETAILS]));
            break;

            case self::ACTION_ABORT:
                print "<h1>Installation aborted</h1>\n";
                if (self::removeInstallFiles(false, true)) {
                    print "<p>All temporary files deleted.</p>";
                } else {
                    print "<p>Note: not all temporary files could be removed.</p>";
                }
            break;

            case self::ACTION_CLEAN:
                self::removeInstallFiles(!empty($options[self::OPT_START]), false);
            break;

            case self::ACTION_LDAP:
                if (!empty($options[self::OPT_ACTIVE]) && isset($options[self::OPT_HOST]) && is_string($options[self::OPT_HOST])) {
                    self::ldapAction($options[self::OPT_HOST]);
                }
            break;

            case self::ACTION_DATABASE:
                if (!empty($options[self::OPT_ACTIVE])) {
                    $dbms = isset($options[self::OPT_DBMS]) ? (string) $options[self::OPT_DBMS] : "";
                    $host = isset($options[self::OPT_HOST]) ? (string) $options[self::OPT_HOST] : "";
                    $port = !empty($options[self::OPT_PORT]) ? (string) $options[self::OPT_PORT] : "";
                    $user = isset($options[self::OPT_USER]) ? (string) $options[self::OPT_USER] : "";
                    $pass = isset($options[self::OPT_PASS]) ? (string) $options[self::OPT_PASS] : "";
                    $name = isset($options[self::OPT_DB_NAME]) ? (string) $options[self::OPT_DB_NAME] : "";
                    self::databaseAction($dbms, $host, $port, $user, $pass, $name);
                }
            break;

            default:
                exit('Unknown action');
            break;
        }
    }

    /**
     * Unzip a winzip archive.
     *
     * This implements an installer action.
     * May only be called via InstallUtility::main();
     *
     * @param   array  $components
     * @return  bool
     */
    private static function unzipComponents(array $components): bool
    {
        $COMPONENTS = array();
        /**
         * load $COMPONENTS
         */
        include_once(PAK); // adds entries to $COMPONENTS

        $nrOfFiles = 0;
        $nrOfDirectories = 0;
        $nrOfErrors = 0;
        $timeStart = time();

        print "<p>started at " . date('r', $timeStart) . "</p>\n";
        print "<hr>";

        assert(!isset($labels), 'Cannot redeclare var $labels');
        $labels = array();
        assert(!isset($filter), 'Cannot redeclare var $filter');
        $filter = "";
        foreach ($COMPONENTS as $name => $component)
        {
            if ((isset($component[FILES][IS]) && $component[FILES][IS] === MANDATORY) || in_array($name, $components)) {
                /* 1) output label */
                if (isset($component[LABEL]['en'])) {
                    $labels[] = $component[LABEL]['en'];
                } elseif (isset($component[LABEL]['de'])) {
                    $labels[] = $component[LABEL]['de'];
                } elseif (is_string($component[LABEL])) {
                    $labels[] = $component[LABEL];
                } else {
                    $labels[] = $name;
                }
                /* 2) build file filter */
                if (isset($component[FILES])) {
                    foreach ($component[FILES] as $file)
                    {
                        $filter .= ( ($filter !== "") ? '|' : '' ) . str_replace('/', '\/', preg_quote(INSTALL_DIR.$file));
                    }
                    unset($file);
                }
            } /* end if */
        } /* end foreach */
        unset($COMPONENTS, $name, $component);

        /* 3) list components */
        print "<h2>Selected components</h2>\n";
        print "<ul>\n";
        foreach ($labels as $label)
        {
            print "<li>{$label}</li>\n";
        }
        print "</ul>\n";
        unset($labels, $label);

        assert(!isset($zip), 'Cannot redeclare var $zip');
        $zip = new \ZipArchive();
        if (!$zip->open(DATA_ZIP)) {
            print "<p>Unable to open archive.</p>\n";
            return false;
        }

        /* 4) extract files */
        print "<h2>Installation results:</h2>\n";
        /* 4.a) extract all */
        assert(!isset($result), 'Cannot redeclare var $result');
        $result = $zip->extractTo('.');
        assert(!isset($files), 'Cannot redeclare var $result');
        $files = self::getZipContents($zip);
        $zip->close();
        unset($zip);

        /* 4.b) remove files not matching */
        assert(!isset($dirs), 'Cannot redeclare var $dirs');
        $dirs = array();
        assert(!isset($file), 'Cannot redeclare var $zip_entry');
        foreach ($files as $file)
        {
            $path = $file['name'];
            if (preg_match("/^{$filter}/", $file['name'])) {

                print self::printZipEntry($file);
                switch (true)
                {
                    case \is_file($path):
                        $nrOfFiles++;
                    break;
                    case \is_dir($path):
                        $nrOfDirectories++;
                    break;
                    default:
                        $nrOfErrors++;
                    break;
                } /* end switch */

            } else {

                assert(!isset($path), 'Cannot redeclare var $path');
                $path = $file['name'];
                if (file_exists($path)) {
                    if (is_dir($path)) {
                        $dirs[] = $path;
                    } elseif (!unlink($path)) {
                        print "<p>Unable to remove temporary file {$path}.</p>\n";
                        $nrOfErrors++;
                    }
                }
                unset($path);

            }
        } /* end foreach */
        unset($result, $file);
        assert(!isset($i), 'Cannot redeclare var $i');
        $i = 0;
        assert(!isset($max), 'Cannot redeclare var $max');
        $max = 2 * count($dirs);
        while (!empty($dirs) && $i < $max)
        {
            assert(!isset($dir), 'Cannot redeclare var $dir');
            $dir = array_shift($dirs);
            if (!@rmdir($dir)) {
                $dirs[] = $dir;
            }
            $i++;
            unset($dir);
        }
        unset($dirs, $max, $i);
        self::installationComplete();

        print "<hr>\n";
        print "<h2>Installation finished</h2>\n";
        print "<ul>\n";
        print "\t<li>Required time: " . (time() - $timeStart) . " seconds</li>\n";
        print "\t<li>Directories created: {$nrOfDirectories}</li>\n";
        print "\t<li>Files processed successfully: {$nrOfFiles}</li>\n";
        print "\t<li>Encountered errors: {$nrOfErrors}</li>\n";
        print "</ul>\n";

        return $nrOfErrors === 0;
    }

    /**
     * Returns archive contents as list.
     *
     * Each entry is an associative array containing the file path and size.
     *
     * @param   \ZipArchive  $zip  program archive
     * @return  array
     */
    private static function getZipContents(\ZipArchive $zip): array
    {
        assert(!isset($files), 'Cannot redeclare var $result');
        $files = array();
        assert(!isset($i), 'Cannot redeclare var $i');
        assert(!isset($stats), 'Cannot redeclare var $stats');
        for ($i = 0; $i < $zip->numFiles; $i++)
        {
            $stats = $zip->statIndex($i);
            if (is_array($stats)) {
                $files[] = $stats;
            }
        }
        unset($i, $stats);
        return $files;
    }

    /**
     * Return entries of archive as string.
     *
     * @param   array   $files
     * @return  string
     */
    private static function printZipEntries(array $files): string
    {
        assert(!isset($output), 'Cannot redeclare var $output');
        $output;
        assert(!isset($file), 'Cannot redeclare var $stats');
        foreach ($files as $file)
        {
            if (is_array($file)) {
                $output .= self::printZipEntry($file);
            }
        }
        unset($file);
        return $output;
    }
    /**
     * Unzip a winzip archive.
     *
     * @param  string  $zipfile  file to unzip
     * @param  bool    $silent   mute output
     */
    private static function unzipFiles(string $zipfile, bool $silent = false): bool
    {
        $timeStart = time();
        $zip = new \ZipArchive();
        if (!$zip->open($zipfile)) {
            print "<p>Error: file '{$zipfile}' not found.</p>";
            return false;
        }
        if (!$silent) {
            print "<p>started at " . date('r', $timeStart) . "</p>\n";
            print "<hr>";
        }
        $nrOfFiles = $zip->numFiles;
        $isError = !$zip->extractTo('.');
        $files = self::getZipContents($zip);
        $zip->close();

        if ($isError) {
            print "Error while reading file. Archive might be corrupt. Automatic installation failed, manual installation is required.<br>\n";
            return false;

        } elseif (!$silent) {
            print self::printZipEntries($files);
        }

        if (!$silent) {
            print "<hr>";
            print "<p>Installation finished.</p>\n";
            print "<ul>\n";
            print "\t<li>Required time: " . (time() - $timeStart) . " seconds</li>\n";
            print "\t<li>Files processed successfully: {$nrOfFiles}</li>\n";
            print "</ul>\n";
        }

        return true;
    }

    /**
     * Get result of extract operation.
     *
     * This implements an installer action.
     * May only be called via InstallUtility::main();
     *
     * @param   array  $zipEntry  associative array produced by extract operation
     * @return  string
     */
    private static function printZipEntry(array $zipEntry): string
    {
        $name     = $zipEntry["name"];
        $filesize = $zipEntry["size"];

        if (file_exists($name)) {
            if ($filesize > 0) {
                return "<p>Unpacking file '{$name}' ({$filesize} Byte)</p>\n";
            } else {
                return "<h2>Creating folder '{$name}'</h2>\n";
            }
        } else {
            return "Error creating file '{$name}'. Archive might be corrupt. Automatic installation failed, manual installation is required.<br>\n";
        }
    }

    /**
     * Remove temporary installation files.
     *
     * @param   bool  $doRelocate  relocate to application when finished (on/off)
     * @param   bool  $silent       mute output (on/off)
     * @return  bool
     */
    private static function removeInstallFiles(bool $doRelocate = false, bool $silent = false): bool
    {
        if (!$silent) {
            $baseURI = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $baseURI = preg_replace('/[^\/]*$/', '', $baseURI);
            $baseURI .= INSTALL_DIR;
            self::terminateInstaller($doRelocate, $baseURI);
            if (!headers_sent()) {
                print '<p>Installation finished. Thank you for using the <a href="http://yanaframework.net">Yana Framework</a> Installer!</p>';
            }
        }
        $zip = new \ZipArchive();
        if (!$zip->open(INSTALL_ZIP)) {
            print "Unable to remove temporary files. Please remove them manually.<br>";
            return false;
        }

        assert(!isset($files), 'Cannot redeclare var $result');
        $files = array();
        assert(!isset($i), 'Cannot redeclare var $i');
        assert(!isset($stats), 'Cannot redeclare var $stats');
        for ($i = 0; $i < $zip->numFiles; $i++)
        {
            $stats = $zip->statIndex($i);
            if (is_array($stats)) {
                $files[] = $stats;
            }
        }
        unset($i, $stats);
        $zip->close();

        $isError = false;
        foreach ($files as $index => $zipEntry)
        {
            $name = $zipEntry["name"];
            if (is_file($name)) {
                if (!unlink($name)) {
                    if (!$silent) {
                        print "Unable to remove temporary file {$name}. Please remove it manually.<br>";
                    }
                    $isError = true;
                }
            }
        }
        foreach ($files as $index => $zipEntry)
        {
            $name    = $zipEntry["name"];
            if (is_dir($name) && file_exists($name)) {
                if (!rmdir($name)) {
                    if (!$silent) {
                        print "Unable to remove temporary directory {$name}. Please remove it manually.<br>";
                    }
                    $isError = true;
                }
            }
        } /* end foreach */
        return !$isError;
    }

    /**
     * Create installer GUI.
     *
     * This implements an installer action.
     * May only be called via InstallUtility::main();
     *
     * @param   string  $choosenLanguage  select prefered language (currently de/en)
     * @return  bool
     */
    private static function showPage(string $choosenLanguage): bool
    {
        if (file_exists(INSTALL_DIR)) {
            die("<h1>Unable to create directory '" . INSTALL_DIR . "'</h1>\n" .
                    "<p>It looks like the application has already been installed. " .
                    "If you want to reinstall it, uninstall your old copy first!</p>" .
                    "<p>If you forgot to delete this install-utility after installation, " .
                    "you might want to do this now to conserve space on your website." .
                    "<a href=\"?action=" . self::ACTION_ABORT . "\">Click here to remove all temporary install files.</a></p>");
        }
        if (!file_exists(DATA_TOC)) {
            if (!file_exists(INSTALL_ZIP)) {
                die('Source file not available. Installation aborted.');
            }

            if (self::unzipFiles(INSTALL_ZIP, true) === false) {
                die("Error while uncompressing '" . INSTALL_ZIP . "'. Archive might be corrupt. Manual installation required.");
            }
        }

        $content = implode("\n", file(DATA_TOC));
        $content = self::addIncludes($content);
        $content = self::addStrings($content, $choosenLanguage);
        $content = self::addMenu($content, $choosenLanguage);
        print $content;
        return true;
    }

    /**#@+
     * hook method
     *
     * @access  private
     * @return  bool
     */

    /**
     * set admin password
     *
     * @param  string  $pass new password
     */
    private static function adminAction(string $pass)
    {
        $function = self::getHook(HOOK_ADMIN);
        return $function($pass);
    }

    /**
     * refresh plugin list
     */
    private static function installationComplete()
    {
        $function = self::getHook(HOOK_INSTALLATION_COMPLETE);
        return $function();
    }

    /**
     * terminate program
     *
     * @param  bool    $do_relocate  start installed application now (on/off)
     * @param  string  $baseURI      base URI
     */
    private static function terminateInstaller(bool $do_relocate, string $baseURI)
    {
        $function = self::getHook(HOOK_TERMINATE_PROGRAM);
        return $function($do_relocate, $baseURI);
    }

    /**
     * perform self test
     *
     * @param   bool  $show_details  show/hide details
     * @return  bool
     */
    private static function testAction(bool $showDetails): bool
    {
        $function = self::getHook(HOOK_TEST);
        return $function($showDetails);
    }

    /**
     * configure LDAP server.
     *
     * @param   string  $hostName  IP or host name
     */
    private static function ldapAction(string $hostName)
    {
        $function = self::getHook(HOOK_LDAP);
        $function($hostName);
    }

    /**
     * configure database connection.
     *
     * @param  string  $dbms  type of database management system
     * @param  string  $host  IP or host address
     * @param  string  $port  port on host server
     * @param  string  $user  user name
     * @param  string  $pass  password
     * @param  string  $name  database name
     */
    private static function databaseAction(string $dbms, string $host, string $port, string $user, string $pass, string $name)
    {
        $function = self::getHook(HOOK_DATABASE);
        $function($dbms, $host, $port, $user, $pass, $name);
    }

    /**#@-*/

    /**#@+
     * helper method
     *
     * A helper method may not call any other method of the class.
     *
     * @access  private
     */

    /**
     * This implements an initial check for compliance with requirements.
     *
     * @return  bool
     */
    private static function check()
    {
        switch (false)
        {
            /**
             * 1) check if directory is writeable
             */
            case is_writeable('./'):
                die("Error: the installation directory '".getcwd()."' is not writeable. Please update the settings for this directory (on Unix/Linux use the command CHMOD 777).");
            break;

            /**
             * 2) check if source file is readable
             */
            case is_readable(DATA_ZIP):
                die("Error: installation package '".DATA_ZIP."' not found. Unable to proceed. Please download a complete package.");
            break;

            /**
             * 3) check required libraries
             */
            case class_exists('\ZipArchive'):
                die("Error: This program requires PHP to be compiled with Zip support: using the config option '--with-zip' for PHP 7.4 or '--enable-zip' for earlier versions.");
            break;

            case extension_loaded('pcre'):
                die("Error: This program requires the 'pcre' library. If you don't install this library, installation cannot continue.");
            break;
        } /* end switch */
    }

    /**
     * Writes the installation log.
     *
     * @param   string  $stdout
     * @return  bool
     */
    private static function writeLog($stdout)
    {
        if (!is_string($stdout)) {
            return false;
        } else if (!file_exists(DATA_INSTALL)) {
            $content = "{stdout}";
        } else {
            $content = implode("\n", file(DATA_INSTALL));
        }
        $content = str_replace('{stdout}', $stdout, $content);

        $log = fopen(LOG, 'w+');
        @chmod(LOG, 0777);
        if (!is_resource($log)) {
            return false;
        } else if (fwrite($log, $content) === false) {
            fclose($log);
            return false;
        } else {
            fclose($log);
            return true;
        }
    }

    /**
     * Create the "components" menu.
     *
     * @param   string  $content  template
     * @param   string  $choosenLanguage  language strings to be used
     * @return  string
     */
    private static function addMenu(string $content, string $choosenLanguage): string
    {
        $COMPONENTS = array();
        include_once(PAK); // Adds entries to $COMPONENTS
        $isFirstFolder = true;
        preg_match_all('/\s*\{begin\}(.*)\{end\}/Us', $content, $blocks);
        for ($i = 0; $i < count($blocks[0]); $i++)
        {
            $result = "";
            foreach ($COMPONENTS as $key => $value)
            {
                if (!isset($value[LABEL][$choosenLanguage])) {
                    $label = $value[LABEL][self::DEFAULT_LANG];
                } else {
                    $label = $value[LABEL][$choosenLanguage];
                }
                $template = $blocks[1][$i];
                switch ($value[IS])
                {
                    case MANDATORY:
                        $template = str_replace("{key}", $key.'" checked="checked" disabled="disabled', $template);
                    break;
                    case ENABLED:
                        $template = str_replace("{key}", $key.'" checked="checked', $template);
                    break;
                    case FOLDER:
                        $template = ((!$isFirstFolder) ? "</ol></li>" : "" ) . "<li class=\"item_open\" id=\"menu_{$key}\"><a href=\"javascript:menu(document.getElementById('menu_{$key}'))\">{$label}:</a>\n<ol>";
                        $isFirstFolder = false;
                    break;
                    default:
                        $template = str_replace("{key}", $key, $template);
                    break;
                }
                $template = str_replace("{value}", $label, $template);
                $result .= $template;
            }
            if (!$isFirstFolder) {
                $result .= "</ol></li>";
            }
            $content = str_replace($blocks[0][$i], $result, $content);
        }
        return $content;
    }

    /**
     * Resolves include(d) files.
     *
     * @param   string  $content  template
     * @return  string
     */
    private static function addIncludes(string $content): string
    {
        preg_match_all('/\s*\{include (page_[\w\d_]+)\}/si', $content, $m);
        for ($i = 0; $i < count($m[0]); $i++)
        {
            if (is_file($m[1][$i] . '.html')) {
                $include = implode('', file($m[1][$i] . '.html'));
                $include = preg_replace('/^.*<body[^>]*>(.*)<\/body>.*$/si', '${1}', $include);
            } else {
                $include = "File not found: " . $m[1][$i] . ".html";
            }
            $content = str_replace($m[0][$i], $include, $content);
        }
        return $content;
    }

    /**
     * Replace text tokens with translation.
     *
     * @param  string  $content           template
     * @param  string  $choosenLanguage  language strings to be used
     */
    private static function addStrings($content, $choosenLanguage)
    {
        $LANGUAGE = array();
        include_once(STRINGS); // Adds entries to $LANGUAGE array
        if (is_array($LANGUAGE)) {

            for ($i = 0; $i < 2; $i++) // $LANGUAGE should contain 2 languages
            {
                foreach ($LANGUAGE as $token => $translation)
                {
                    if (!isset($translation[$choosenLanguage])) {
                        $current = $translation[self::DEFAULT_LANG];
                    } else {
                        $current = $translation[$choosenLanguage];
                    }
                    $content = str_replace("{language {$token}}", nl2br((string) $current), $content);
                }
            }
        }
        return $content;
    }

    /**
     * Implementation to resolve a hook(ed) function.
     *
     * returns the callable name of
     * the function identified by id
     * as a string
     *
     * @param   int  $id
     * @return  string
     */
    private static function getHook(int $id)
    {
        include_once(HOOKS);
        $name = "";

        switch (true)
        {
            case !isset($HOOKS[$id]):
            case !is_callable($HOOKS[$id], false, $name):
                return function () { return true; };
            break;
            default:
                return $HOOKS[$id];
            break;
        }
    }

    /**#@-*/

} /* end class */

InstallUtility::main($_GET);
?>
