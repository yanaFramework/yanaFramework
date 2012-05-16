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

namespace Yana\Views;

/**
 * <<decorator>> Template.
 *
 * This implements a decorator class for Smarty templates.
 * It provides a cleaned up, simple interface targeted for ease of use.
 *
 * @package     yana
 * @subpackage  views
 */
class Template extends \Yana\Core\Object implements \Yana\Views\IsTemplate
{

    /**
     * local Smarty instance
     *
     * @var  \Smarty_Internal_Template
     * @ignore
     */
    protected $template = null;

    /**
     * create an instance
     *
     * You may enter a filename of a template you want to use.
     *
     * @param  \Smarty_Internal_Template $template
     */
    public function __construct(\Smarty_Internal_Template $template)
    {
        $this->template = $template;
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
     * @return  string
     * @throws  \SmartyException  when template is not found
     */
    public function fetch()
    {
        return $this->template->fetch();
    }

    /**
     * Fetch the current template and return it as a string.
     *
     * Predefined variables may be imported from the global registry to the template.
     * Existing template vars will be replaced.
     *
     * @return  string
     */
    public function __toString()
    {
        try {
            return $this->fetch();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Get template vars.
     *
     * @return  array
     */
    public function getVars()
    {
        return $this->template->getTemplateVars();
    }

    /**
     * Get template var.
     *
     * If you call $template->getVar($varName) it will get the template var $varName and return it.
     *
     * @param   string  $key  variable-name
     * @return  mixed
     */
    public function getVar($key)
    {
        assert('is_string($key); // Wrong argument type for argument 1. String expected.');

        $resource = $this->getVars();
        assert('is_array($resource); /* unexpected result: $resource should be an array */');
        return \Yana\Util\Hashtable::get($resource, "$key");
    }

    /**
     * Assign a variable to a key by value.
     *
     * @param   string  $varName  address
     * @param   mixed   $var      some new value
     * @return  \Yana\Views\Template
     */
    public function setVar($varName, $var)
    {
        assert('is_string($varName); // Wrong argument type for argument 1. String expected.');
        $this->template->assign($varName, $var);
        return $this;
    }

    /**
     * Assign a new set of variables.
     *
     * This replaces all template vars with new ones.
     *
     * @param   array  $vars  associative array containg new set of template vars
     * @return  \Yana\Views\Template
     */
    public function setVars(array $vars)
    {
        $this->template->assign((array) $vars);
        return $this;
    }

    /**
     * Assign a variable to a key by reference.
     *
     * Example of usage:
     * <code>$template->setVarByReference('foo', array  $var) </code>
     *
     * @param   string  $varName  address
     * @param   mixed   &$var     some new value
     * @return  \Yana\Views\Template
     */
    public function setVarByReference($varName, &$var)
    {
        assert('is_string($varName); // Invalid argument $varName: string expected');

        $this->template->assignByRef($varName, $var);
        return $this;
    }

    /**
     * Assign a new set of variables by reference.
     *
     * Example of usage:
     * <code>$template->setVarByReference($array) </code>
     *
     * @param   string  $varName  address
     * @param   mixed   &$var     some new value
     * @return  \Yana\Views\Template
     */
    public function setVarsByReference(array &$vars)
    {
        foreach (array_keys((array) $vars) as $key)
        {
            $this->template->assignByRef($key, $vars[$key]); // assign contents to global namespace
        }
        return $this;
    }

    /**
     * Set filename to fetch.
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
     * @param   string  $filename  name of the template file
     * @return  \Yana\Views\Template
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
     * Returns a string with the path and name of the current template.
     *
     * @return  string
     */
    public function getPath()
    {
        assert('is_string($this->template->template_resource); // Unexpected result: Template path is not a string');
        return $this->template->template_resource;
    }

}

?>