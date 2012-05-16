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
 *
 * @ignore
 */

namespace Yana\Views;

/**
 * Manager class to automate searching and loading of templates, belonging to view layer.
 *
 * {@internal
 *
 * The following two system vars have been added as of version 2.9.2:
 *
 * <ol>
 *   <li> {$SYSTEM_TEMPLATE} = id of current base template </li>
 *   <li> {$SYSTEM_INSERT} = id of current extensional template </li>
 * </ol>
 *
 * }}
 *
 * @package     yana
 * @subpackage  core
 */
class Manager extends \Yana\Core\Object implements \Yana\Views\IsManager
{

    /**
     * List of stylesheets.
     *
     * @var  array
     */
    private $_styles = array();

    /**
     * @var \Smarty_Internal_Template
     */
    private $_layoutTemplate = null;

    /**
     * List of script files.
     *
     * @var  array
     */
    private $_scripts = array();

    /**
     * global Smarty instance
     *
     * @var  \Smarty
     */
    private $_smarty = null;

    /**
     * Contains hash of current request URI + post vars.
     *
     * @var  string
     */
    private static $_cacheId = "";

    /**
     * Initializes the manager class
     *
     * @param  \Smarty  $smarty  template engine
     */
    public function __construct(\Smarty $smarty)
    {
        $this->_smarty = $smarty;
    }

    /**
     * Create page layout document.
     *
     * A view always consists of at least two parts: a base document and an included document.
     *
     * @param   string  $filename                 path to template file that hold the page layout (usually: index.tpl)
     * @param   string  $mainContentTemplateName  path to another template file that renders the page content
     * @param   array   $templateVars             possibly multi-dimensional, associative array of template variables
     * @return  \Yana\Views\Template
     */
    public function createLayoutTemplate($filename, $mainContentTemplateName, array $templateVars)
    {
        assert('is_string($filename); // Invalid argument $filename: string expected');
        assert('is_string($mainContentTemplateName); // Invalid argument $mainContentTemplate: string expected');

        $isAjaxRequest = (bool) \Yana\Core\Request::getVars('is_ajax_request');

        /**
         * If this is an AJAX request we should only output the content, leaving off the frame.
         */
        if ($isAjaxRequest) {
            if (!empty($mainContentTemplateName)) {
                $filename = $mainContentTemplateName; // We drop the layout and just use the content template
                $mainContentTemplateName = '';
            }
        }

        $template = $this->_createTemplate($filename);
        $template->assign($templateVars); // Initialize template variables

        if ($isAjaxRequest) {
            if (headers_sent() === false) {
                header('Content-Type: text/html; charset=UTF-8');
            }
            /**
             * For AJAX-Requests we leave off the layout and just output the template's body-tag (if any).
             * This is done by the output post-processor that will look for the $FILE_IS_INCLUDE flag.
             */
            $template->assign('FILE_IS_INCLUDE', true);
        }
        if (\strpos(':', $mainContentTemplateName) === false) {
            $mainContentTemplateName = ((\is_file($mainContentTemplateName)) ? 'file:' : 'id:') . $mainContentTemplateName;
        }
        $template->assign('SYSTEM_TEMPLATE', $filename);
        $contentTemplate = $this->createContentTemplate($mainContentTemplateName);
        $template->assign('SYSTEM_INSERT', $contentTemplate);

        $this->_layoutTemplate = $template;
        $template = new \Yana\Views\Template($this->_layoutTemplate);
        $template->setVar('BASEDIR', dirname($template->getPath()));
        return $template;
    }

    /**
     * Create a new template instance.
     *
     * This initializes a new template, also setting up cache- and compile-ids.
     * If a base-layout is defined already, it will be set up as the parent template.
     *
     * @param   string  $filename  path to template file
     * @return  \Yana\Views\Template 
     */
    public function createContentTemplate($filename)
    {
        assert('is_string($filename); // Invalid argument $filename: string expected');

        $template = $this->_createTemplate($filename, $this->_layoutTemplate);
        $template = new \Yana\Views\Template($template);
        $template->setVar('BASEDIR', dirname($template->getPath()));
        return $template;
    }

    /**
     * This calls Smarty to create a new template.
     *
     * @param   string                     &$filename  path to template file or template id (which will be resolved)
     * @param   \Smarty_Internal_Template  $parent     parent template (if any)
     * @return  \Smarty_Internal_Template
     */
    private function _createTemplate(&$filename, \Smarty_Internal_Template $parent = null)
    {
        $cacheId = null;
        $compileId = null;
        if ($this->_smarty->caching) {
            $cacheId = $this->_getCacheId();
            $compileId = $cacheId;
        }
        return $this->_smarty->createTemplate($filename, $cacheId, $compileId, $parent);
    }

    /**
     * Calculate cache id.
     *
     * This helps the Smarty template engine to identify when to invalidate the cache,
     * when it is active.
     *
     * @return  string
     */
    private function _getCacheId()
    {
        if (empty(self::$_cacheId)) {

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
            self::$_cacheId = md5($id);
            assert('is_string(self::$_cacheId) && !empty(self::$_cacheId); // failure calculating cache id');
        }
        return self::$_cacheId;
    }

    /**
     * Add path to CSS stylesheet file.
     *
     * @param  string  $file  path and file name
     * @return \Yana\Views\Manager
     */
    public function addStyle($file)
    {
        assert('is_string($file); // Wrong argument type argument 1. String expected');
        $this->_styles[] = "$file";
        return $this;
    }

    /**
     * Add path to javascript file.
     *
     * @param  string  $file  path and file name
     * @return \Yana\Views\Manager
     */
    public function addScript($file)
    {
        assert('is_string($file); // Wrong argument type argument 1. String expected');
        $this->_scripts[] = "$file";
        return $this;
    }

    /**
     * Add multiple CSS files.
     *
     * @param  array  $files  path and file names
     * @return \Yana\Views\Manager
     */
    public function addStyles(array $files)
    {
        $this->_styles = array_merge($this->_styles, $files);
        $this->_styles = array_unique($this->_styles);
        return $this;
    }

    /**
     * Add multiple JavaScript files.
     *
     * @param  array  $files  path and file names
     * @return \Yana\Views\Manager
     */
    public function addScripts(array $files)
    {
        $this->_scripts = array_merge($files, $this->_scripts);
        $this->_scripts = array_unique($this->_scripts);
        return $this;
    }

    /**
     * Returns list of paths to CSS stylesheets.
     *
     * @return  array
     */
    public function getStyles()
    {
        return $this->_styles;
    }

    /**
     * Returns list of paths to javascript files.
     *
     * @return  array
     */
    public function getScripts()
    {
        return $this->_scripts;
    }

    /**
     * Delete all temporary files in 'cache/' directory.
     *
     * @ignore
     */
    public function clearCache()
    {
        $this->getSmarty()->clearAllCache();
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
     * @param   string  $name  name of the function
     * @param   mixed   $code  a callable resource
     * @return  \Yana\Views\Manager
     */
    public function setFunction($name, $code)
    {
        assert('is_string($name); // Wrong type for argument $name. String expected.');
        assert('is_callable($code); // Wrong type for argument $code. Not a callable resource.');

        $this->getSmarty()->registerPlugin(\Smarty::PLUGIN_FUNCTION, $name, $code);
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
     * @param   string  $name  name of the function
     * @param   mixed   $code  a callable resource
     * @return  \Yana\Views\Manager
     */
    public function setModifier($name, $code)
    {
        assert('is_string($name); // Wrong type for argument $name. String expected.');
        assert('is_callable($code); // Wrong type for argument $code. Not a callable resource.');

        $this->getSmarty()->registerPlugin(\Smarty::PLUGIN_MODIFIER, $name, $code);
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
     * @param   string  $name  name of the function
     * @param   mixed   $code  a callable resource
     * @return  \Yana\Views\Manager
     */
    public function setBlockFunction($name, $code)
    {
        assert('is_string($name); // Wrong type for argument $name. String expected.');
        assert('is_callable($code); // Wrong type for argument $code. Not a callable resource.');

        $this->getSmarty()->registerPlugin(\Smarty::PLUGIN_BLOCK, $name, $code);
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
     * @param   string  $name  name of the function
     * @return  \Yana\Views\Manager
     */
    public function unsetFunction($name)
    {
        assert('is_string($name); // Wrong argument type for argument 2. String expected.');
        assert('!empty($name); // Name cannot be empty.');

        $this->getSmarty()->unregisterPlugin(\Smarty::PLUGIN_FUNCTION, $name);
        return $this;
    }

    /**
     * Unregister modifier.
     *
     * By using this, the function named $name will no longer be
     * available in template. Be cautious: If the unregistered funciton is
     * still used inside the template, this will issue a template error
     * and possibly cause your application to exit.
     *
     * @param   string  $name  name of the function
     * @return  \Yana\Views\Manager
     */
    public function unsetModifier($name)
    {
        assert('is_string($name); // Wrong argument type for argument 2. String expected.');
        assert('!empty($name); // Name cannot be empty.');

        $this->getSmarty()->unregisterPlugin(\Smarty::PLUGIN_MODIFIER, $name);
        return $this;
    }

    /**
     * Unregister block function.
     *
     * By using this, the function named $name will no longer be
     * available in template. Be cautious: If the unregistered funciton is
     * still used inside the template, this will issue a template error
     * and possibly cause your application to exit.
     *
     * @param   string  $name  name of the function
     * @return  \Yana\Views\Manager
     */
    public function unsetBlockFunction($name)
    {
        assert('is_string($name); // Wrong argument type for argument 2. String expected.');
        assert('!empty($name); // Name cannot be empty.');

        $this->getSmarty()->unregisterPlugin(\Smarty::PLUGIN_BLOCK, $name);
        return $this;
    }

    /**
     * Bypass manager class.
     *
     * This function is used to unbox the smarty instance inside the object.
     * It may be used to bypass the template class in cases where direct
     * access to the smarty template engine is necessary.
     *
     * @return  \Smarty
     */
    public function getSmarty()
    {
        return $this->_smarty;
    }

}

?>