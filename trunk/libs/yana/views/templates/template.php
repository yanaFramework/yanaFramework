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

namespace Yana\Views\Templates;

/**
 * <<decorator>> Template.
 *
 * This implements a decorator class for Smarty templates.
 * It provides a cleaned up, simple interface targeted for ease of use.
 *
 * @internal It makes little sense to include this class in unit-tests, as we would end up testing Smarty functionality rather than our own.
 *
 * @package     yana
 * @subpackage  views
 * @codeCoverageIgnore
 */
class Template extends \Yana\Views\Templates\AbstractTemplate implements \Yana\Views\Templates\IsTemplate
{

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
        return $this->_getTemplate()->fetch();
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
        return $this->_getTemplate()->getTemplateVars();
    }

    /**
     * Check if a var exists.
     *
     * Returns bool(true) if the key is known and bool(false) otherwise.
     *
     * @param   string  $key  some key (case insensitive)
     * @return  bool
     */
    public function isVar($key)
    {
        assert(is_scalar($key), 'Invalid argument $key: scalar expected');

        return !is_null($this->getVar($key));
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
        assert(is_string($key), 'Wrong argument type for argument 1. String expected.');

        $resource = $this->getVars();
        assert(is_array($resource), 'unexpected result: $resource should be an array');
        return \Yana\Util\Hashtable::get($resource, "$key");
    }

    /**
     * Assign a variable to a key by value.
     *
     * @param   string  $varName  address
     * @param   mixed   $var      some new value
     * @return  \Yana\Views\Templates\Template
     */
    public function setVar($varName, $var)
    {
        assert(is_string($varName), 'Wrong argument type for argument 1. String expected.');
        $this->_getTemplate()->assign($varName, $var);
        return $this;
    }

    /**
     * Assign a new set of variables.
     *
     * This replaces all template vars with new ones.
     *
     * @param   array  $vars  associative array containg new set of template vars
     * @return  \Yana\Views\Templates\Template
     */
    public function setVars(array $vars)
    {
        $this->_getTemplate()->assign((array) $vars);
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
     * @return  \Yana\Views\Templates\Template
     */
    public function setVarByReference($varName, &$var)
    {
        assert(is_string($varName), 'Invalid argument $varName: string expected');

        $this->_getTemplate()->assignByRef($varName, $var);
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
     * @return  \Yana\Views\Templates\Template
     */
    public function setVarsByReference(array &$vars)
    {
        foreach (array_keys((array) $vars) as $key)
        {
            $this->_getTemplate()->assignByRef($key, $vars[$key]); // assign contents to global namespace
        }
        return $this;
    }

}

?>