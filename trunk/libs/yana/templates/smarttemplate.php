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
class SmartTemplate extends \Yana\Core\Object
{

    /**
     * @access  private
     * @static
     * @var     string
     */
    private static $cacheId = "";

    /**
     * local Smarty instance
     *
     * @access  protected
     * @var     Smarty_Internal_Template
     * @ignore
     */
    protected $template = null;

    /**
     * global Smarty instance
     *
     * @access  protected
     * @static
     * @var     Smarty
     * @ignore
     */
    protected static $smarty = null;

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
        $smarty = self::_getSmarty();
        $cacheId = self::_getCacheId();
        if ($smarty->caching > 0) {
            $this->template = $smarty->createTemplate($filename, $cacheId, $cacheId);
        } else {
            $this->template = $smarty->createTemplate($filename, null, $cacheId);
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
    public function __toString()
    {
        return $this->template->fetch();
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
            return $this->template->getTemplateVars();
        }

        /* 2) get one template var, identified by $key */
        $resource = $this->template->getTemplateVars();
        assert('is_array($resource); /* unexpected result: $resource should be an array */');
        return \Yana\Util\Hashtable::get($resource, "$key");
    }

    /**
     * Initializes 
     *
     * @access  protected
     * @static
     * @return  Smarty
     * @since   3.1.0
     * @ignore
     */
    protected static function _getSmarty()
    {
        /**
         * 1) Config Smarty
         */
        if (! self::$smarty instanceof Smarty) {
            $registry = \Yana\VDrive\Registry::getGlobalInstance();

            self::$smarty = new \Smarty();

            /**
             * 1.1) delimiters
             */
            self::$smarty->left_delimiter = YANA_LEFT_DELIMITER;
            self::$smarty->right_delimiter = YANA_RIGHT_DELIMITER;

            /**
             * 1.2) directories
             */
            if ($registry instanceof \Yana\VDrive\Registry) {
                self::$smarty->template_dir = '.';
                self::$smarty->config_dir = $registry->getVar('SKINCONFIGDIR');
                self::$smarty->compile_dir = $registry->getVar('TEMPDIR');
            } else {
                self::$smarty->template_dir = '.';
                self::$smarty->config_dir = 'skins/.config/';
                if (YANA_CDROM === true) {
                    self::$smarty->compile_dir = YANA_CDROM_DIR;
                } else {
                    self::$smarty->compile_dir = 'cache/';
                }
            }

            /**
             * 1.3) security settings
             */
            self::$smarty->php_handling = Smarty::PHP_REMOVE;
            self::$smarty->security = true;
            /**
             * PHP-handling
             *
             * If set to TRUE, the $php_handling  setting is not checked for
             * security.
             */
            self::$smarty->security_settings['PHP_HANDLING'] = false;
            /**
             * Include any
             *
             * If set to TRUE, any template can be included  from the file
             * system, regardless of the $secure_dir list.
             */
            self::$smarty->security_settings['INCLUDE_ANY'] = false;
            /**
             * PHP-tags
             *
             * If set to TRUE, {php}{/php}  tags are permitted in the templates.
             */
            self::$smarty->security_settings['PHP_TAGS'] = false;
            /**
             * PHP-constants
             *
             * If set to TRUE, constants via {$smarty.const.FOO} are allowed in
             * the templates.
             */
            self::$smarty->security_settings['ALLOW_CONSTANTS'] = false;
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
            self::$smarty->security_settings['ALLOW_SUPER_GLOBALS'] = false;

            /*
             * 1.4) caching behaviour
             */
            self::$smarty->caching = YANA_TPL_CACHE;
            self::$smarty->use_sub_dirs = YANA_TPL_CACHE_DIR;

            /*
             * 1.4.1) default setting for compile check
             */
            if (!defined('YANA_ERROR_REPORTING') || !defined('YANA_ERROR_OFF')) {
                self::$smarty->compile_check = true;

            /*
             * 1.4.2) distinguish between debug-mode and production-mode
             */
            } else {
                if (YANA_ERROR_REPORTING !== YANA_ERROR_OFF) {
                    self::$smarty->compile_check = true;
                } else {
                    self::$smarty->compile_check = false;
                }
                if (YANA_ERROR_REPORTING === YANA_ERROR_ON) {
                    self::$smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['varDump']  = array(
                        'SmartUtility::varDump', true
                    );
                }
            }

            /**
             * 1.5) extensions
             */
            $modifiers = array(
                'embeddedTags' => array('SmartUtility::embeddedTags', true),
                'replaceToken' => array('SmartUtility::replaceToken', false),
                'css' =>          array('SmartUtility::css', true),
                'date' =>         array('SmartUtility::date', true),
                'entities' =>     array('SmartUtility::entities', true),
                'href' =>         array('SmartUtility::href', false),
                'scanForAt' =>    array('SmartUtility::scanForAt', true),
                'smilies' =>      array('SmartUtility::smilies', true),
                'url' =>          array('SmartUtility::url', false),
                'urlEncode' =>    array('SmartUtility::urlEncode', true)
            );
            if (!empty(self::$smarty->registered_plugins[Smarty::PLUGIN_MODIFIER])) {
                $modifiers = array_merge(self::$smarty->registered_plugins[Smarty::PLUGIN_MODIFIER], $modifiers);
            }
            self::$smarty->registered_plugins[Smarty::PLUGIN_MODIFIER] = $modifiers;
            $functions = array(
                'printArray' => array('SmartUtility::printArray', true),
                'printUnorderedList' =>  array('SmartUtility::printUnorderedList', true),
                'rss' =>                 array('SmartUtility::rss', true),
                'import' =>              array('SmartUtility::import', false),
                'smilies' =>             array('SmartUtility::guiSmilies', true),
                'embeddedTags' =>        array('SmartUtility::guiEmbeddedTags', true),
                'create' =>              array('SmartFormUtility::createForm', false),
                'captcha' =>             array('SmartUtility::captcha', true),
                'slider' =>              array('SmartUtility::slider', true),
                'sizeOf' =>              array('SmartUtility::sizeOf', true),
                'toolbar' =>             array('SmartUtility::toolbar', false),
                'preview' =>             array('SmartUtility::preview', true),
                'colorpicker' =>         array('SmartUtility::colorpicker', true),
                'sml_load' =>            array('SmartUtility::smlLoad', true),
                'smlLoad' =>             array('SmartUtility::smlLoad', true),
                'lang' =>                array('SmartUtility::lang', true),
                'visitorCount' =>        array('SmartUtility::visitorCount', true),
                'portlet' =>             array('SmartUtility::portlet', true),
                'applicationBar' =>      array('SmartUtility::applicationBar', true),
                'selectDate' =>          array('SmartUtility::selectDate', true),
                'selectTime' =>          array('SmartUtility::selectTime', true)
            );
            if (!empty(self::$smarty->registered_plugins[Smarty::PLUGIN_FUNCTION])) {
                $functions = array_merge(self::$smarty->registered_plugins[Smarty::PLUGIN_FUNCTION], $functions);
            }
            self::$smarty->registered_plugins[Smarty::PLUGIN_FUNCTION] = $functions;
            self::$smarty->registered_plugins[Smarty::PLUGIN_BLOCK]['loop'] = array(
                'SmartUtility::loopArray', false
            );
            self::$smarty->registered_filters[Smarty::FILTER_PRE][] =
                array(new \Yana\Templates\Helpers\Processors\PreProcessor(), '__invoke');

            self::$smarty->registered_filters[Smarty::FILTER_POST][] =
                array(new \Yana\Templates\Helpers\Processors\PostProcessor(), '__invoke');

            self::$smarty->registered_filters[Smarty::FILTER_OUTPUT][] =
                array(new \Yana\Templates\Helpers\Processors\OutputFilter(), '__invoke');

            self::$smarty->default_modifiers =
                array('replaceToken');

            self::$smarty->registered_resources["template"] = array(
                array(
                    "SmartFileResource::getTemplate",
                    "SmartFileResource::getTimestamp",
                    "SmartFileResource::isSecure",
                    "SmartFileResource::isTrusted"
                ),
                false
            );

            self::$smarty->registered_resources["string"] = array(
                array(
                    "SmartStringResource::getTemplate",
                    "SmartStringResource::getTimestamp",
                    "SmartStringResource::isSecure",
                    "SmartStringResource::isTrusted"
                ),
                false
            );

            self::$smarty->registered_resources["id"] = array(
                array(
                    "SmartIdResource::getTemplate",
                    "SmartIdResource::getTimestamp",
                    "SmartIdResource::isSecure",
                    "SmartIdResource::isTrusted"
                ),
                false
            );
            self::$smarty->default_resource_type = 'template';
            self::$smarty->error_reporting = E_ALL & ~E_NOTICE;
        } // end if

        return self::$smarty;
    }

    /**
     * Bypass template class.
     *
     * This function is used to unbox the smarty instance inside the object.
     * It may be used to bypass the template class in caseswhere direct
     * access to the smarty template engine is necessary.
     *
     * @access  public
     * @return  Smarty
     * @since   2.8.9
     */
    public function getSmarty()
    {
        if (!empty($this->template)) {
            return $this->template;
        } else {
            return self::_getSmarty();
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
            $this->template->assign($var);

        /* 2) assign to var identified by $varName */
        } else {
            $this->template->assign($varName, $var);
        }
        return true;

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
                $this->template->assignByRef($key, $var[$key]);
            }
            unset($key);
            return true;

        /* 2) assign to var identified by $varName */
        } else {
            $this->template->assignByRef($varName, $var);
            return true;
        }

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
     * @return  SmartTemplate
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the filename is invalid
     */
    public function setPath($filename)
    {
        assert('is_string($filename); // Wrong argument type for argument 1. String expected.');

        if (preg_match("/.*\.(register|config|cfg|lock|dat|htaccess|php|inc|conf)/Ui", $filename)) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException("Access denied for file '$filename'.");
        }
        $this->template->template_resource = "$filename";
        return $this;
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
        $registry = \Yana\VDrive\Registry::getGlobalInstance();

        /* 1) make sure .htaccess does'nt get deleted */
        $dir = '';
        if (isset($registry)) {
            $dir = $registry->getVar('TEMPDIR');
        } else {
            $dir = 'cache/';
        }
        if (is_writeable($dir . '/.htaccess')) {
            chmod($dir . '/.htaccess', 0550);
        }
        /* 2) clear Smarty cache */
        $smarty = self::_getSmarty();
        $smarty->clearAllCache();

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
     * Register function.
     *
     * This function registers the function at $code as a template
     * function under the name $name in the template engine.
     *
     * The template function is called as {foo }.
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
     * @param   string  $name  name of the function
     * @param   mixed   $code  a callable resource
     * @return  SmartTemplate
     */
    public function setFunction($name, $code)
    {
        assert('is_string($name); // Wrong type for argument $name. String expected.');
        assert('is_callable($code); // Wrong type for argument $code. Not a callable resource.');

        $smarty = self::_getSmarty();
        $smarty->registered_plugins[Smarty::PLUGIN_FUNCTION][$name] = array($code, true);
        return $this;
    }

    /**
     * Register modifier.
     *
     * This function registers a template modifier, called as {$bar|foo}.
     *
     * Note: For details on how the called function should look like,
     * see the smarty documentation at http://smarty.php.net/docs.php,
     * chapter 16) "Extending smarty", sections: "template functions",
     * "modifiers" and "block functions".
     *
     * @access  public
     * @param   string  $name  name of the function
     * @param   mixed   $code  a callable resource
     * @return  SmartTemplate
     */
    public function setModifier($name, $code)
    {
        assert('is_string($name); // Wrong type for argument $name. String expected.');
        assert('is_callable($code); // Wrong type for argument $code. Not a callable resource.');

        $smarty = self::_getSmarty();
        $smarty->registered_plugins[Smarty::PLUGIN_MODIFIER][$name] = array($code, true);
        return $this;
    }

    /**
     * Register block function.
     *
     * This function registers a template block function, called as {foo }...{/foo}.
     *
     * Note: For details on how the called function should look like,
     * see the smarty documentation at http://smarty.php.net/docs.php,
     * chapter 16) "Extending smarty", sections: "template functions",
     * "modifiers" and "block functions".
     *
     * @access  public
     * @param   string  $name  name of the function
     * @param   mixed   $code  a callable resource
     * @return  SmartTemplate
     */
    public function setBlockFunction($name, $code)
    {
        assert('is_string($name); // Wrong type for argument $name. String expected.');
        assert('is_callable($code); // Wrong type for argument $code. Not a callable resource.');

        $smarty = self::_getSmarty();
        $smarty->registered_plugins[Smarty::PLUGIN_BLOCK][$name] = array($code, true);
        return $this;
    }

    /**
     * Unregister function.
     *
     * By using this, the function named $name will no longer be
     * available in template. Be cautious: If the unregistered funciton is
     * still used inside the template, this will issue a template error
     * and possibly cause your application to exit.
     *
     * @access  public
     * @param   string  $name  name of the function
     * @return  bool
     */
    public function unsetFunction($name)
    {
        assert('is_string($name); // Wrong argument type for argument 2. String expected.');
        assert('!empty($name); // Argument 2 cannot be empty.');

        $smarty = self::_getSmarty();
        unset($smarty->registered_plugins[Smarty::PLUGIN_FUNCTION][$name]);
    }

    /**
     * Unregister modifier.
     *
     * By using this, the function named $name will no longer be
     * available in template. Be cautious: If the unregistered funciton is
     * still used inside the template, this will issue a template error
     * and possibly cause your application to exit.
     *
     * @access  public
     * @param   string  $name  name of the function
     * @return  bool
     */
    public function unsetModifier($name)
    {
        assert('is_string($name); // Wrong argument type for argument 2. String expected.');
        assert('!empty($name); // Argument 2 cannot be empty.');

        $smarty = self::_getSmarty();
        unset($smarty->registered_plugins[Smarty::PLUGIN_MODIFIER][$name]);
    }

    /**
     * Unregister block function.
     *
     * By using this, the function named $name will no longer be
     * available in template. Be cautious: If the unregistered funciton is
     * still used inside the template, this will issue a template error
     * and possibly cause your application to exit.
     *
     * @access  public
     * @param   string  $name  name of the function
     * @return  bool
     */
    public function unsetBlockFunction($name)
    {
        assert('is_string($name); // Wrong argument type for argument 2. String expected.');
        assert('!empty($name); // Argument 2 cannot be empty.');

        $smarty = self::_getSmarty();
        unset($smarty->registered_plugins[Smarty::PLUGIN_BLOCK][$name]);
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
        assert('is_string($this->template->template_resource); // Unexpected result: Template path is not a string');
        return $this->template->template_resource;
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
            $queryString = "";
            if (isset($_SERVER['QUERY_STRING'])) {
                $query = $_REQUEST;
                ksort($query);
                unset($query[YANA_SESSION_NAME]);
                assert('is_array($query); /* Array expected: $query */');
                $queryString = http_build_query($query);
                unset($query);
            }

            // build id
            $id = "";
            if (!empty($queryString)) {
                // get language
                $language = "";
                if (isset($GLOBALS['YANA'])) {
                    $language = $GLOBALS['YANA']->getLanguage()->getLocale();
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