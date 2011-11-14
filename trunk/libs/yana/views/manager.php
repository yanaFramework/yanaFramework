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
class Manager
{

    /**
     * List of stylesheets.
     *
     * @var  array
     */
    private $_styles = array();

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
     * Initializes the manager class
     *
     * @param  \Smarty  $smarty  template engine
     */
    public function __construct(\Smarty $smarty)
    {
        $this->_smarty = $smarty;
    }

    /**
     * add a CSS stylesheet file
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
     * add a javascript file
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
     * add multiple CSS files
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
     * add multiple JavaScript files
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
     * get list of CSS stylesheets
     *
     * @return  array
     */
    public function getStyles()
    {
        return $this->_styles;
    }

    /**
     * get list of javascript files
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
     * @return  SmartTemplate
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
     * @return  SmartTemplate
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
     * @return  SmartTemplate
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