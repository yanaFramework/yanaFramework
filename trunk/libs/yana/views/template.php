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
 * @package     yana
 * @subpackage  views
 */
class Template extends \Yana\Core\Object
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
     * Assign a variable to a key by value.
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
     * @param   string  $varName  address
     * @param   mixed   $var      some new value
     * @return  bool
     */
    public function setVar($varName, $var)
    {
        assert('is_string($varName); // Wrong argument type for argument 1. String expected.');

        /* 1) assign to global namespace */
        if ($varName == '*') {

            assert('is_array($var); // Invalid argument $var: array expected');
            $this->template->assign((array) $var);

        /* 2) assign to var identified by $varName */
        } else {
            $this->template->assign($varName, $var);
        }
        return true;
    }

    /**
     * Assign a variable to a key by reference.
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
     * @param   string  $varName  address
     * @param   mixed   &$var     some new value
     * @return  bool
     */
    public function setVarByReference($varName, &$var)
    {
        assert('is_string($varName); // Invalid argument $varName: string expected');

        /* 1) assign to global namespace */
        if ($varName === '*') {

            assert('is_array($var); // Invalid argument $var: array expected');

            foreach (array_keys((array) $var) as $key)
            {
                $this->template->assignByRef($key, $var[$key]); // assign contents to global namespace
            }

        /* 2) assign to var identified by $varName */
        } else {
            $this->template->assignByRef($varName, $var);
        }
        return true;
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
     * eturns a string with the path and name of the current template.
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