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
 * <<decorator>> SmartTemplate
 *
 * This implements a decorator class for the smarty
 * template engine. The use of the "decorator" pattern
 * actually means it "implements an API" on top
 * of the original.
 *
 * Note that this does not touch or even change the
 * engine itself.
 *
 * This class provides two things: most obviously it
 * provides some features, that smarty does not have and
 * in addition it does some more type checking and
 * automates the initialization process.
 *
 * @access      public
 * @package     yana
 * @subpackage  core
 */
class SmartTemplate extends Object
{
    /**#@+
     * @ignore
     *
     * @access  private
     * @static
     */

    /** @var array  */ private static $functions = array();
    /** @var array  */ private static $modifiers = array();
    /** @var array  */ private static $blocks = array();
    /** @var string */ private static $cacheId = "";

    /**#@-*/

    /**
     * template path
     *
     * @access  protected
     * @var     string
     * @ignore
     */
    protected $path = "";

    /**
     * local Smarty instance
     *
     * @access  protected
     * @var     Smarty
     * @ignore
     */
    protected $localSmarty = null;

    /**
     * global Smarty instance
     *
     * @access  protected
     * @static
     * @var     Smarty
     * @ignore
     */
    protected static $globalSmarty = null;

    /**
     * create an instance
     *
     * You may enter a filename of a template you want to use.
     *
     * @param  string  $filename  (optional)
     */
    public function __construct($filename = "")
    {
        // initialize smarty instance
        $this->localSmarty = clone self::getGlobalSmarty();

        // set selected template (if any)
        if ($filename) {
            $this->setPath("$filename");
        }
    }

    /**
     * fetch a template
     *
     * This function will fetch the current template and return it
     * as a string.
     *
     * Predefined variables may be imported from the global registry
     * to the template.
     * Existing template vars will be replaced.
     *
     * @access  public
     * @return  string
     */
    public function toString()
    {
        // set functions, modifiers and block functions
        if (is_array(self::$functions)) {
            assert('!isset($name); // Cannot redeclare var $name');
            assert('!isset($code); // Cannot redeclare var $code');
            foreach (self::$functions as $name => $code)
            {
                $this->localSmarty->register_function($name, $code);
            }
            unset($name, $code);
        }
        if (is_array(self::$modifiers)) {
            assert('!isset($name); // Cannot redeclare var $name');
            assert('!isset($code); // Cannot redeclare var $code');
            foreach (self::$modifiers as $name => $code)
            {
                $this->localSmarty->register_modifier($name, $code);
            }
            unset($name, $code);
        }
        if (is_array(self::$blocks)) {
            assert('!isset($name); // Cannot redeclare var $name');
            assert('!isset($code); // Cannot redeclare var $code');
            foreach (self::$blocks as $name => $code)
            {
                $this->localSmarty->register_block($name, $code);
            }
            unset($name, $code);
        }

        // fetch and return template
        $cacheId = self::_getCacheId();
        if (self::$globalSmarty->caching > 0) {
            return $this->localSmarty->fetch($this->getPath(), $cacheId, $cacheId);
        } else {
            return $this->localSmarty->fetch($this->getPath(), null, $cacheId);
        }
    }

    /**
     * get template var
     *
     * There are two ways to call this function:
     *
     * If you call $smartTemplate->getVar($varName) it will get the
     * template var $varName and return it.
     *
     * If you call $smartTemplate->getVar("*") with the wildcard '*'
     * or an empty string '' it will return an associative array
     * containing all template vars.
     *
     * @access  public
     * @param   string  $key  variable-name
     * @return  mixed
     */
    public function getVar($key = '*')
    {
        assert('is_string($key); // Wrong argument type for argument 1. String expected.');
        /* 1) get all template vars */
        if (empty($key) || $key === '*') {
            return $this->localSmarty->get_template_vars();
        }

        /* 2) get one template var, identified by $key */
        $resource = $this->localSmarty->get_template_vars();
        assert('is_array($resource); /* unexpected result: $resource should be an array */');
        return Hashtable::get($resource, "$key");
    }

    /**
     * bypass template class
     *
     * This function is used to unbox the smarty instance inside the
     * object. It may be used to bypass the template class in cases
     * where direct access to the smarty template engine is necessary.
     *
     * @access  public
     * @static
     * @return  Smarty
     * @since   3.1.0
     * @ignore
     */
    public static function getGlobalSmarty()
    {
        /**
         * 1) Config Smarty
         */
        if (! self::$globalSmarty instanceof Smarty) {
            /* @var $YANA Yana */
            global $YANA;

            self::$globalSmarty = new Smarty();

            /**
             * 1.1) delimiters
             */
            self::$globalSmarty->left_delimiter = YANA_LEFT_DELIMITER;
            self::$globalSmarty->right_delimiter = YANA_RIGHT_DELIMITER;

            /**
             * 1.2) directories
             */
            if (isset($YANA)) {
                SmartUtility::loadSmilies($YANA->getVar("PROFILE.SMILEYDIR"));
                self::$globalSmarty->template_dir = '.';
                self::$globalSmarty->config_dir = $YANA->getVar('SKINCONFIGDIR');
                self::$globalSmarty->compile_dir = $YANA->getVar('TEMPDIR');
            } else {
                self::$globalSmarty->template_dir = '.';
                self::$globalSmarty->config_dir = 'skins/.config/';
                if (YANA_CDROM === true) {
                    self::$globalSmarty->compile_dir = YANA_CDROM_DIR;
                } else {
                    self::$globalSmarty->compile_dir = 'cache/';
                }
            }

            /**
             * 1.3) security settings
             */
            self::$globalSmarty->php_handling = SMARTY_PHP_REMOVE;
            self::$globalSmarty->security = true;
            /**
             * PHP-handling
             *
             * If set to TRUE, the $php_handling  setting is not checked for
             * security.
             */
            self::$globalSmarty->security_settings['PHP_HANDLING'] = false;
            /**
             * Include any
             *
             * If set to TRUE, any template can be included  from the file
             * system, regardless of the $secure_dir list.
             */
            self::$globalSmarty->security_settings['INCLUDE_ANY'] = false;
            /**
             * PHP-tags
             *
             * If set to TRUE, {php}{/php}  tags are permitted in the templates.
             */
            self::$globalSmarty->security_settings['PHP_TAGS'] = false;
            /**
             * PHP-constants
             *
             * If set to TRUE, constants via {$smarty.const.FOO} are allowed in
             * the templates.
             */
            self::$globalSmarty->security_settings['ALLOW_CONSTANTS'] = false;
            /**
             * PHP-super globals
             *
             * If set to TRUE, super-globals like $GLOBAL or $_COOKIE are
             * allowed in the templates.
             *
             * {@internal
             * This setting is available since Smarty 2.6.26
             * }}
             */
            self::$globalSmarty->security_settings['ALLOW_SUPER_GLOBALS'] = false;

            /*
             * 1.4) caching behaviour
             */
            self::$globalSmarty->caching = YANA_TPL_CACHE;
            self::$globalSmarty->use_sub_dirs = YANA_TPL_CACHE_DIR;

            /*
             * 1.4.1) default setting for compile check
             */
            if (!defined('YANA_ERROR_REPORTING') || !defined('YANA_ERROR_OFF')) {
                self::$globalSmarty->compile_check = true;

            /*
             * 1.4.2) distinguish between debug-mode and production-mode
             */
            } else {
                if (YANA_ERROR_REPORTING !== YANA_ERROR_OFF) {
                    self::$globalSmarty->compile_check = true;
                } else {
                    self::$globalSmarty->compile_check = false;
                }
                if (YANA_ERROR_REPORTING === YANA_ERROR_ON) {
                    self::$globalSmarty->register_function('varDump',     array('SmartUtility', 'varDump'));
                }
            }

            /**
             * 1.5) extensions
             */
            self::$globalSmarty->register_modifier('embeddedTags',        array('SmartUtility', 'embeddedTags'));
            self::$globalSmarty->register_modifier('replaceToken',        array('SmartUtility', 'replaceToken'), false);
            self::$globalSmarty->register_modifier('css',                 array('SmartUtility', 'css'));
            self::$globalSmarty->register_modifier('date',                array('SmartUtility', 'date'));
            self::$globalSmarty->register_modifier('entities',            array('SmartUtility', 'entities'));
            self::$globalSmarty->register_modifier('href',                array('SmartUtility', 'href'), false);
            self::$globalSmarty->register_modifier('scanForAt',           array('SmartUtility', 'scanForAt'));
            self::$globalSmarty->register_modifier('smilies',             array('SmartUtility', 'smilies'));
            self::$globalSmarty->register_modifier('url',                 array('SmartUtility', 'url'), false);
            self::$globalSmarty->register_modifier('urlEncode',           array('SmartUtility', 'urlEncode'));
            self::$globalSmarty->register_function('printArray',          array('SmartUtility', 'printArray'));
            self::$globalSmarty->register_function('printUnorderedList',  array('SmartUtility', 'printUnorderedList'));
            self::$globalSmarty->register_function('rss',                 array('SmartUtility', 'rss'));
            self::$globalSmarty->register_function('import',              array('SmartUtility', 'import'), false);
            self::$globalSmarty->register_function('smilies',             array('SmartUtility', 'guiSmilies'));
            self::$globalSmarty->register_function('embeddedTags',        array('SmartUtility', 'guiEmbeddedTags'));
            self::$globalSmarty->register_function('create',              array('SmartUtility', 'create'), false);
            self::$globalSmarty->register_function('captcha',             array('SmartUtility', 'captcha'));
            self::$globalSmarty->register_function('slider',              array('SmartUtility', 'slider'));
            self::$globalSmarty->register_function('sizeOf',              array('SmartUtility', 'sizeOf'));
            self::$globalSmarty->register_function('toolbar',             array('SmartUtility', 'toolbar'), false);
            self::$globalSmarty->register_function('preview',             array('SmartUtility', 'preview'));
            self::$globalSmarty->register_function('colorpicker',         array('SmartUtility', 'colorpicker'));
            self::$globalSmarty->register_function('sml_load',            array('SmartUtility', 'smlLoad'));
            self::$globalSmarty->register_function('smlLoad',             array('SmartUtility', 'smlLoad'));
            self::$globalSmarty->register_function('lang',                array('SmartUtility', 'lang'));
            self::$globalSmarty->register_function('visitorCount',        array('SmartUtility', 'visitorCount'));
            self::$globalSmarty->register_function('portlet',             array('SmartUtility', 'portlet'));
            self::$globalSmarty->register_function('applicationBar',      array('SmartUtility', 'applicationBar'));
            self::$globalSmarty->register_function('selectDate',          array('SmartUtility', 'selectDate'));
            self::$globalSmarty->register_function('selectTime',          array('SmartUtility', 'selectTime'));
            self::$globalSmarty->register_block('loop',                   array('SmartUtility', 'loopArray'), false);
            self::$globalSmarty->register_prefilter(array('SmartUtility', 'htmlPreProcessor'));
            self::$globalSmarty->register_postfilter(array('SmartUtility', 'htmlPostProcessor'));
            self::$globalSmarty->register_outputfilter(array('SmartUtility', 'outputFilter'));
            self::$globalSmarty->default_modifiers = array('replaceToken');

            self::$globalSmarty->register_resource(
                "template",
                array(
                    "SmartFileResource::getTemplate",
                    "SmartFileResource::getTimestamp",
                    "SmartFileResource::isSecure",
                    "SmartFileResource::isTrusted"
                )
            );

            self::$globalSmarty->register_resource(
                "string",
                array(
                    "SmartStringResource::getTemplate",
                    "SmartStringResource::getTimestamp",
                    "SmartStringResource::isSecure",
                    "SmartStringResource::isTrusted"
                )
            );

            self::$globalSmarty->register_resource(
                "id",
                array(
                    "SmartIdResource::getTemplate",
                    "SmartIdResource::getTimestamp",
                    "SmartIdResource::isSecure",
                    "SmartIdResource::isTrusted"
                )
            );
            self::$globalSmarty->default_resource_type = 'template';
        } // end if

        return self::$globalSmarty;
    }

    /**
     * bypass template class
     *
     * This function is used to unbox the smarty instance inside the
     * object. It may be used to bypass the template class in cases
     * where direct access to the smarty template engine is necessary.
     *
     * @access  public
     * @return  Smarty
     * @since   2.8.9
     */
    public function &getSmarty()
    {
        if (!empty($this->localSmarty)) {
            return $this->localSmarty;
        } else {
            return self::getGlobalSmarty();
        }
    }

    /**
     * assign a variable by value
     *
     * This assigns the $var to the name $varName.
     *
     * Unlike Smarty's "assign()" this function takes an
     * additional value for $varName:
     *
     * You may use the wildcard '*' to merge an associative array with the template vars.
     * Example of usage: <code>$smartTemplate->setVar('*', array  $var) </code>
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * {@internal
     *
     * The following synopsis: <code>$smartTemplate->setVar('*', string $var)</code>
     * has been dropped as of version 2.9.2.
     *
     * }}
     *
     * @access  public
     * @param   string  $varName  address
     * @param   mixed   $var      some new value
     * @return  bool
     */
    public function setVar($varName, $var)
    {
        assert('is_string($varName); // Wrong argument type for argument 1. String expected.');

        /* 1) assign to global namespace */
        if ($varName == '*') {

            if (!is_array($var)) {
                trigger_error("When using the wildcard '*' with the function 'insert',\n\t\t" .
                    "argument 2 is expected to be an array. Found '" . gettype($var) .
                    "' instead.", E_USER_WARNING);
                return false;
            }
            $this->localSmarty->assign($var);
            return true;

        /* 2) assigned var is an object */
        } elseif (is_object($var)) {

            $this->localSmarty->assign_by_ref($varName, $var);
            return true;

        /* 3) assign to var identified by $varName */
        } else {
            $this->localSmarty->assign($varName, $var);
            return true;
        }

    }

    /**
     * assign a variable by reference
     *
     * This assigns the $var to the name $varName.
     *
     * Unlike Smarty's "assign()" this function takes an
     * additional value for $varName:
     *
     * You may use the wildcard '*' to merge an associative array
     * with the template vars.
     *
     * Example of usage:
     * <code>$smartTemplate->setVarByReference('*', array  $var) </code>
     *
     * {@internal
     *
     * The following synopsis:
     * <code>$smartTemplate->setVarByReference('*', string $var)</code>
     * has been dropped as of version 2.9.2.
     *
     * }}
     *
     * @access  public
     * @param   string  $varName  address
     * @param   mixed   &$var     some new value
     * @return  bool
     */
    public function setVarByReference($varName, &$var)
    {
        assert('is_string($varName); // Wrong argument type for argument 1. String expected.');

        /* 1) assign to global namespace */
        if ($varName === '*') {

            /* error: invalid input */
            if (!is_array($var)) {
                trigger_error("When using the wildcard '*' with the function 'insertByReference',\n\t\t" .
                "argument 2 is expected to be an array. Found '".gettype($var)."' instead.", E_USER_WARNING);
                return false;
            }

            /* assign contents to global namespace */
            assert('!isset($key); /* cannot redeclare variable $key */');
            foreach (array_keys($var) as $key)
            {
                $this->localSmarty->assign_by_ref($key, $var[$key]);
            }
            unset($key);
            return true;

        /* 2) assign to var identified by $varName */
        } else {
            $this->localSmarty->assign_by_ref($varName, $var);
            return true;
        }

    }

    /**
     * insert a file
     *
     * Load a file and assign it's content as a string
     * to the provided template var.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * {@internal
     *
     * The following synopsis:
     * <code>$smartTemplate->insertFile('*', string $filename)</code>
     * has been dropped as of version 2.9.2.
     *
     * }}
     *
     * @access  public
     * @param   string  $varName    variable name
     * @param   string  $filename   filename
     * @return  bool
     */
    public function insertFile($varName, $filename)
    {
        assert('is_string($varName); // Wrong argument type for argument 1. String expected.');
        assert('!empty($varName); // Argument 1 must not be empty.');
        assert('$varName !== "*"; // Argument 1 must not be a wildcard.');
        assert('is_string($filename); // Wrong argument type for argument 2. String expected.');
        assert('is_file($filename); // Argument 2 must be a valid file.');

        if (!file_exists("$filename") || !is_readable("$filename")) {
            trigger_error("The file '{$filename}' is not readable.", E_USER_WARNING);
            return false;
        }

        /* import file */
        /* @var $template SmartTemplate */
        $template = clone $this;
        $template->setPath($filename);
        $template->setVar('FILE_IS_INCLUDE', 'true');
        $this->localSmarty->assign("$varName", $template->toString());
        return true;
    }

    /**
     * set filename of current template
     *
     * You may set another filename of a template to fetch.
     *
     * Please note:
     * <ol>
     *   <li>  Template files may not have a reserved extension like
     *         "htaccess", "php", "config" or the like.
     *   </li>
     *   <li>  Files should be adressed from the root.
     *         This is where "index.php" is stored.
     *   </li>
     *   <li>  If you can't access a file, the file does not exist
     *         or is not readable, a template error is thrown.
     *   </li>
     *   <li>  Filenames are case-sensitive!  </li>
     * </ol>
     *
     * @access  public
     * @param   string  $filename  name of the template file
     * @return  bool
     */
    public function setPath($filename)
    {
        assert('is_string($filename); // Wrong argument type for argument 1. String expected.');

        if (preg_match("/.*\.(register|config|cfg|lock|dat|htaccess|php|inc|conf)/Ui", $filename)) {
            self::$globalSmarty->trigger_error("ERROR 403: Access denied for file '$filename'.");
            return false;
        } else {
            $this->path = "$filename";
            return true;
        }
    }

    /**
     * clear cache
     *
     * Deletes all temporary files in the 'cache/' directory.
     *
     * @access  public
     * @static
     * @ignore
     */
    public static function clearCache()
    {
        $registry = Registry::getGlobalInstance();

        /* 1) make sure .htaccess does'nt get deleted */
        if (isset($registry)) {
            $dir = $registry->getVar('TEMPDIR');
        } else {
            $dir = 'cache/';
        }
        if (is_writeable($dir . '/.htaccess')) {
            chmod($dir . '/.htaccess', 0550);
        }
        /* 2) clear Smarty cache */
        $smarty = self::getGlobalSmarty();
        $smarty->clear_all_cache();

        /* 3) clear Yana cache */
        $files = dirlist($dir, '.php|.cache|.tmp');
        for ($i = 0; $i < count($files); $i++)
        {
            $current_file = $dir.$files[$i];
            /* If file can't be deleted due to active write-protection
              (e.g. when running under Windows), check wether this can be fixed. */
            if (!is_writeable($current_file)) {
                chmod($current_file, 0666);
            }
            if (is_writeable($current_file)) {
                unlink($current_file);
            } else {
                $message = "Unable to delete file '{$current_file}', because the file is not writeable.";
                trigger_error($message, E_USER_WARNING);
            }
        }
    }

    /**
     * Register function
     *
     * This function registers the function at $code as a template
     * function under the name $name in the template engine.
     *
     * Argument $as can be one of the followin constants:
     * <ul>
     *  <li>  YANA_TPL_FUNCTION
     *        - registers the resource as a template function,
     *          called as {foo }
     *  </li>
     *  <li>  YANA_TPL_MODIFIER
     *        - registers the resource as a template modifier,
     *          called as {$bar|foo}
     *  </li>
     *  <li>  YANA_TPL_BLOCK
     *        - registers the resource as a template block function,
     *          called as {foo }...{/foo}
     *  </li>
     * </ul>
     *
     * Argument $name:
     * <ul>
     * <li> Say, you register some function as YANA_TPL_FUNCTION under the name "foo",
     * then you can call this function from within a template using: {foo } </li>
     * </ul>
     *
     * Argument $code:
     * <ul>
     * <li>Say, you got a PHP function like foo().
     * To refer to this function use the code-argument with the string
     * "foo".</li>
     * <li>Say, you got a PHP class "Foo" and a static function "bar",
     * which you would call from PHP as Foo::bar().
     * To refer to this function use the code-argument with the array
     * array("Foo", "bar").</li>
     * <li>Say, you got a PHP object $foo and a non-static function "bar",
     * which you would call from PHP as $foo->bar().
     * To refer to this function use the code-argument with the array
     * array($foo, "bar").</li>
     * </ul>
     *
     * Note: For details on how the called function should look like,
     * see the smarty documentation at http://smarty.php.net/docs.php,
     * chapter 16) "Extending smarty", sections: "template functions",
     * "modifiers" and "block functions".
     *
     * @access  public
     * @param   int     $as    one of: YANA_TPL_FUNCTION, YANA_TPL_MODIFIER, YANA_TPL_BLOCK
     * @param   string  $name  name of the function
     * @param   mixed   $code  a callable resource
     */
    public function setFunction($as, $name, $code)
    {
        assert('is_int($as); // Wrong argument type for argument 1. Integer expected.');
        assert('is_string($name); // Wrong argument type for argument 2. String expected.');
        assert('is_callable($code); // Wrong argument type for argument 3. Not a callable resource.');

        switch ($as)
        {
            case YANA_TPL_FUNCTION:
                self::$functions["$name"] = $code;
            break;
            case YANA_TPL_MODIFIER:
                self::$modifiers["$name"] = $code;
            break;
            case YANA_TPL_BLOCK:
                self::$blocks["$name"] = $code;
            break;
            default:
                $message = "Invalid argument 1 in function ".__METHOD__.".";
                throw new InvalidArgumentException($message, E_USER_WARNING);
            break;
        }
    }

    /**
     * Unregister function
     *
     * By using this, the function named $name will no longer be
     * available in template. Be cautious: If the unregistered funciton is
     * still used inside the template, this will issue a template error
     * and possibly cause your application to exit.
     *
     * @access  public
     * @param   int     $as    one of: YANA_TPL_FUNCTION, YANA_TPL_MODIFIER, YANA_TPL_BLOCK
     * @param   string  $name  name of the function
     * @return  bool
     */
    public function unsetFunction($as, $name)
    {
        assert('is_int($as); // Wrong argument type for argument 1. Integer expected.');
        assert('is_string($name); // Wrong argument type for argument 2. String expected.');
        assert('!empty($name); // Argument 2 cannot be empty.');
        switch ($as)
        {
            case YANA_TPL_FUNCTION:
                self::$globalSmarty->unregister_function($name);
                $this->localSmarty->unregister_function($name);
                if (isset(self::$functions[$name])) {
                    unset(self::$functions[$name]);
                }
            break;
            case YANA_TPL_MODIFIER:
                self::$globalSmarty->unregister_modifier($name);
                $this->localSmarty->unregister_modifier($name);
                if (isset(self::$modifiers[$name])) {
                    unset(self::$modifiers[$name]);
                }
            break;
            case YANA_TPL_BLOCK:
                self::$globalSmarty->unregister_block($name);
                $this->localSmarty->unregister_block($name);
                if (isset(self::$blocks[$name])) {
                    unset(self::$blocks[$name]);
                }
            break;
            default:
                $message = "Invalid argument 1 in function ".__METHOD__.".";
                throw new InvalidArgumentException($message, E_USER_WARNING);
            break;
        }
    }

    /**
     * get path to the resource
     *
     * Returns a string with the path and name of the current template.
     *
     * @access  public
     * @return  string
     */
    public function getPath()
    {
        assert('is_string($this->path); // Unexpected result: $this->path is not a string');
        return $this->path;
    }

    /**
     * get cache id
     *
     * @access   private
     * @static
     * @return   string
     */
    private static function _getCacheId()
    {
        if (empty(self::$cacheId)) {

            // get query string (with session-id stripped)
            if (isset($_SERVER['QUERY_STRING'])) {
                $query = $_REQUEST;
                ksort($query);
                assert('!isset($queryString); /* Cannot redeclare variable $queryString */');
                unset($query[YANA_SESSION_NAME]);
                assert('is_array($query); /* Array expected: $query */');
                $queryString = http_build_query($query);
                unset($query);
            } else {
                $queryString = "";
            }

            // build id
            if (!empty($queryString)) {
                // get language
                if (isset($GLOBALS['YANA'])) {
                    $language = $GLOBALS['YANA']->language->getLocale();
                } else {
                    $language = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
                }
                $id = $language . '/' . $queryString;
                unset($language);

            } else {
                $id = $_SERVER['PHP_SELF'];
            }
            unset($queryString);

            // move id to cache;
            self::$cacheId = md5($id);
            assert('is_string(self::$cacheId) && !empty(self::$cacheId); // failure calculating cache id');
        }
        return self::$cacheId;
    }

}

?>