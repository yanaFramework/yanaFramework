<?php
/**
 * YANA PHP-Framework
 *
 * Constant definitions and basic configuration-file
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
declare(strict_types=1);

/**
 * Check system requirements
 */
switch (false)
{
    case \function_exists('mb_http_input'):
        $message = "The Multibyte String PHP extension is required for UTF-8 support. However, this exentsion was not found. "
            . "Linux users: install this feature via 'sudo apt-get install php7.0-mbstring'. "
            . "Windows users: edit php.ini and add 'extension=php_mbstring.dll'.";
        \trigger_error($message, \E_USER_ERROR);
        exit;
    case \function_exists('simplexml_load_file'):
        $message = "The SimpleXML PHP extension is required to load the primary configuration file. However, this exentsion was not found. "
            . "Linux users: install this feature via 'sudo apt-get install php-xml'. "
            . "Windows users: edit php.ini and add 'extension=php_xsl.dll'.";
        \trigger_error($message, \E_USER_ERROR);
        exit;
}

/**
 * Set encoding to UTF-8
 */
ini_set('default_charset', 'UTF-8');
ini_set('input_encoding', 'UTF-8');
ini_set('output_encoding', 'UTF-8');
ini_set('internal_encoding', 'UTF-8');
mb_http_input("UTF-8");
mb_internal_encoding("UTF-8");
mb_http_output("UTF-8");
mb_language('uni');

/**
 * Note: to make PHP produce clickable PHP error messages, the ini vars docref_root and docref_ext need to be set.
 * In case they have not been set, the following passage will point them to the PHP manual.
 */

$docrefRoot = ini_get('docref_root');
$docrefExt  = ini_get('docref_ext');
if (empty($docrefRoot)) {
    ini_set('docref_root', 'http://www.php.net/manual/en/');
}
if (empty($docrefExt)) {
    ini_set('docref_ext', '.php');
}
unset($docrefRoot, $docrefExt);
if (!defined('CASE_MIXED')) {
    /**
     * used for change case commands
     *
     * @ignore
     */
    define('CASE_MIXED', -1);
}
if (!defined('YANA_VERSION')) {
    /**
     * currently installed version of the Yana Framework
     *
     * Note: you can compare two version strings using the PHP-function
     * version_compare(). See the PHP manual for details.
     */
    define('YANA_VERSION', '{VERSION}');
}
if (!defined('YANA_IS_STABLE')) {
    /**
     * this is true, if the current version is stable
     */
    define('YANA_IS_STABLE', false);
}

/**#@+
 * error reporting
 */
if (!defined('YANA_ERROR_OFF')) {
    /**
     * turn error reporting off
     *
     * Use this in any production environment to prevent information leak to
     * possible attackers.
     */
    define('YANA_ERROR_OFF', 'off');
}
if (!defined('YANA_ERROR_ON')) {
    /**
     * turn error reporting on
     *
     * Use this to debug your scripts.
     */
    define('YANA_ERROR_ON',  'on');
}
if (!defined('YANA_ERROR_LOG')) {
    /**
     * send errors to a log file
     *
     * Write all reported messages to 'cache/error.log'.
     * Use this for testing.
     */
    define('YANA_ERROR_LOG', 'log');
}
/**#@-*/

if (!defined('ENT_FULL')) {
    /**
     * Constant ENT_FULL
     *
     * This adds a new constant ENT_FULL to the php predefined constants
     * ENT_COMPAT=2, ENT_QUOTES=3, ENT_NOQUOTES=0
     *
     * Use of this constant with the method \Yana\Util\Strings::encode(), will force
     * ALL characters of the string to be encoded as html entities.
     *
     * See this example:
     * <code>$encoded = \Yana\Util\Strings::encode($string, 'entities', ENT_FULL)</code>
     *
     * {@internal
     * Note:
     * Just in case there may be any additional predefined PHP constants in
     * the future there is a good chance they will continue as 4, 5, ...
     * So setting the value ENT_FULL to 10 should avoid a collision.
     * }}
     *
     * @see  \Yana\Util\Strings::encode()
     */
    define('ENT_FULL', 10);
}

/**#@-*/

if (!defined('YANA_INSTALL_DIR')) {
    /**
     * this is true, if the current version is stable
     */
    define('YANA_INSTALL_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);
}
if (!defined('YANA_CDROM')) {
    /**
     * select type of media (online / offline)
     *
     * This should be bool(false) when installed on a webserver.
     * If you want to run yana from a DVD or CD-ROM, this should be set to bool(true).
     *
     * In more detail: This setting defines where to store user settings and files.
     * When false they will be kept in the yana installation directory.
     * When true they will be moved to a temporary directory on a local HDD.
     */
    define('YANA_CDROM', false);
}

if (!defined('YANA_CDROM_DIR')) {
    if (YANA_CDROM === true) {

        /**
         * temporary directory when running on CD-ROM or DVD
         *
         * Here you may specify a path, where files should be stored, when running
         * the YANA framework on a media, where you can't store files.
         *
         * Example:
         * <code>
         * // Try to determine the system's temporary directory automatically, ...
         * define('YANA_CDROM_DIR', sys_get_temp_dir() . "yana/");
         * // or set it to a fixed directory.
         * define('YANA_CDROM_DIR', "c:/temp/yana/");
         * // for Server2Go on CD-ROM you may also use the following ...
         * define('YANA_CDROM_DIR', $_ENV['S2G_TEMP_FOLDER'] . "yana/");
         * </code>
         */
        define('YANA_CDROM_DIR', sys_get_temp_dir() . "yana/");

    } else {
        /**
         * @ignore
         */
        define('YANA_CDROM_DIR', '');
    }
}

if (!defined('YANA_EMBTAG_ALLOW_PHP')) {
    /**
     * activate/deactivate php emb-tag
     *
     * This enables/disables the emb-tag "php", which transfers php code in guestbook
     * or forum posts to a higlighted text representation in HTML.
     * Note that this does NOT execute the code.
     *
     * Under certain circumstances, e.g. where you do not need this feature, you may want
     * to disable it. To do so, set this constant to bool(false). Otherwise it should be
     * set to bool(true).
     */
    define('YANA_EMBTAG_ALLOW_PHP', true);
}

if (!defined('YANA_DB_STRICT')) {
    /**
     * activate/deactivate strict database checks
     *
     * This enables validation of database queries against the stored database schema.
     * Disabling this may slightly increase performance, but may also reduce the security
     * for poorly written database applications.
     * If you are in an environment where your application is available to the public,
     * you should keep this setting to true.
     */
    define('YANA_DB_STRICT',             true);
}

/**#@+
 * regular expressions and word filter constants
 *
 * @ignore
 */

if (!defined('YANA_DB_DELIMITER')) {
    define('YANA_DB_DELIMITER',          '\'');
}
if (!defined('YANA_LEFT_DELIMITER')) {
    define('YANA_LEFT_DELIMITER',        '{');
}
if (!defined('YANA_RIGHT_DELIMITER')) {
    define('YANA_RIGHT_DELIMITER',       '}');
}
if (!defined('YANA_LEFT_DELIMITER_REGEXP')) {
    define('YANA_LEFT_DELIMITER_REGEXP', '\{');
}
if (!defined('YANA_RIGHT_DELIMITER_REGEXP')) {
    define('YANA_RIGHT_DELIMITER_REGEXP', '\}');
}

/**#@-*/
/**#@+
 * configurations and settings
 *
 * @ignore
 */

if (!defined('YANA_SESSION_NAME')) {
    define('YANA_SESSION_NAME', 'ysid');
}

/**#@-*/
/**#@+
 * import external definitions
 *
 * @ignore
 */

/* [database settings] */

require_once __DIR__ . '/config/dbconfig.php';

/* [main class] */

require_once __DIR__ . '/libs/yana/core/autoloadbuilder.php';

/* [fallbacks] */

if (!\interface_exists('Throwable')) {
    require_once __DIR__ . '/libs/yana/core/exceptions/throwable.php';
}

/**#@-*/

$builder = new \Yana\Core\AutoLoadBuilder();
$builder->addClassMapper(\Yana\Core\AutoLoadBuilder::DIRECT_MAPPER)
    ->setNameSpace('MDB2');
$builder->addClassMapper(\Yana\Core\AutoLoadBuilder::DIRECT_MAPPER)
    ->setNameSpace('Smarty')
    ->setFileExtension('.class.php')
    ->setBaseDirectory(__DIR__ . '/libs/smarty/');
$builder->addClassMapper(\Yana\Core\AutoLoadBuilder::GENERIC_MAPPER)
    ->setNameSpace('SQL_Parser');
$builder->addClassMapper(\Yana\Core\AutoLoadBuilder::GENERIC_MAPPER)
    ->setNameSpace('Doctrine')
    ->setBaseDirectory(__DIR__ . '/libs/');
$builder->addClassMapper(\Yana\Core\AutoLoadBuilder::LOWERCASED_MAPPER)
    ->setNameSpace('Yana')
    ->setBaseDirectory(__DIR__ . '/libs/');
$builder->addClassMapper(\Yana\Core\AutoLoadBuilder::LOWERCASED_MAPPER)
    ->setNameSpace('Plugins')
    ->setBaseDirectory(__DIR__ . '/');
$builder->registerLoader();
unset($builder);

?>
