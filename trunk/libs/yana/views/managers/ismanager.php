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

namespace Yana\Views\Managers;

/**
 * <<interface>> Manager class to automate searching and loading of templates, belonging to view layer.
 *
 * @package     yana
 * @subpackage  views
 */
interface IsManager
{

    /**
     * Create page layout document.
     *
     * A view always consists of at least two parts: a base document and an included document.
     *
     * @param   string  $filename             path to template file that hold the page layout (usually: index.tpl)
     * @param   string  $mainContentTemplate  path to another template file that renders the page content
     * @param   array   $templateVars         possibly multi-dimensional, associative array of template variables
     * @return  \Yana\Views\Templates\IsTemplate
     */
    public function createLayoutTemplate($filename, $mainContentTemplate, array $templateVars);

    /**
     * Create a new template instance.
     *
     * This initializes a new template, also setting up cache- and compile-ids.
     * If a base-layout is defined already, it will be set up as the parent template.
     *
     * @param   string  $filename  path to template file
     * @return  \Yana\Views\Templates\IsTemplate 
     */
    public function createContentTemplate($filename);

    /**
     * Add path to CSS stylesheet file.
     *
     * @param  string  $file  path and file name
     * @return \Yana\Views\Managers\IsManager
     */
    public function addStyle($file);

    /**
     * Add path to javascript file.
     *
     * @param  string  $file  path and file name
     * @return \Yana\Views\Managers\IsManager
     */
    public function addScript($file);

    /**
     * Add multiple CSS files.
     *
     * @param  array  $files  path and file names
     * @return \Yana\Views\Managers\IsManager
     */
    public function addStyles(array $files);

    /**
     * Add multiple JavaScript files.
     *
     * @param  array  $files  path and file names
     * @return \Yana\Views\Managers\IsManager
     */
    public function addScripts(array $files);

    /**
     * Returns list of paths to CSS stylesheets.
     *
     * @return  array
     */
    public function getStyles();

    /**
     * Returns list of paths to javascript files.
     *
     * @return  array
     */
    public function getScripts();

    /**
     * Delete all temporary files in 'cache/' directory.
     *
     * @ignore
     */
    public function clearCache();

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
     * @return  self
     */
    public function setFunction($name, $code);

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
     * @return  self
     */
    public function setModifier($name, $code);

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
     * @return  self
     */
    public function setBlockFunction($name, $code);

    /**
     * Unregister function.
     *
     * By using this, the function named $name will no longer be
     * available in template. Be cautious: If the unregistered funciton is
     * still used inside the template, this will issue a template error
     * and possibly cause your application to exit.
     *
     * @param   string  $name  name of the function
     * @return  self
     */
    public function unsetFunction($name);

    /**
     * Unregister modifier.
     *
     * By using this, the function named $name will no longer be
     * available in template. Be cautious: If the unregistered funciton is
     * still used inside the template, this will issue a template error
     * and possibly cause your application to exit.
     *
     * @param   string  $name  name of the function
     * @return  self
     */
    public function unsetModifier($name);

    /**
     * Unregister block function.
     *
     * By using this, the function named $name will no longer be
     * available in template. Be cautious: If the unregistered funciton is
     * still used inside the template, this will issue a template error
     * and possibly cause your application to exit.
     *
     * @param   string  $name  name of the function
     * @return  self
     */
    public function unsetBlockFunction($name);

    /**
     * Bypass manager class.
     *
     * This function is used to unbox the smarty instance inside the object.
     * It may be used to bypass the template class in cases where direct
     * access to the smarty template engine is necessary.
     *
     * @return  \Smarty
     */
    public function getSmarty();

}

?>