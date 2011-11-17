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
 * Null-template for testing purposes.
 *
 * Use this to mock a template where necessary.
 *
 * @package     yana
 * @subpackage  views
 */
class NullTemplate extends \Yana\Core\Object implements \Yana\Views\IsTemplate
{

    /**
     * @var array
     */
    private $_vars = array();

    /**
     * @var string
     */
    private $_path = "";

    /**
     * Returns an empty string.
     *
     * @return  string
     */
    public function fetch()
    {
        $string = "";
        if (is_file($this->getPath())) {
            $string = \file_get_contents($this->getPath());
        }
        return $string;
    }

    /**
     * Returns an empty string.
     *
     * @return  string
     */
    public function __toString()
    {
        return $this->fetch();
    }

    /**
     * Get template vars.
     *
     * @return  array
     */
    public function getVars()
    {
        return $this->_vars;
    }

    /**
     * Get template var.
     *
     * @param   string  $key  variable-name
     * @return  mixed
     */
    public function getVar($key)
    {
        return @$this->_vars[$key];
    }

    /**
     * Assign a variable to a key by value.
     *
     * @param   string  $varName  address
     * @param   mixed   $var      some new value
     * @return  \Yana\Views\NullTemplate
     */
    public function setVar($varName, $var)
    {
        $this->_vars[$varName] = $var;
        return $this;
    }

    /**
     * Assign a new set of variables.
     *
     * This replaces all template vars with new ones.
     *
     * @param   array  $vars  associative array containg new set of template vars
     * @return  \Yana\Views\NullTemplate
     */
    public function setVars(array $vars)
    {
        $this->_vars = $vars;
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
     * @return  \Yana\Views\NullTemplate
     */
    public function setVarByReference($varName, &$var)
    {
        $this->_vars[$varName] =& $var;
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
     * @return  \Yana\Views\NullTemplate
     */
    public function setVarsByReference(array &$vars)
    {
        $this->_vars =& $vars;
        return $this;
    }

    /**
     * Set filename to fetch.
     *
     * @param   string  $filename  name of the template file
     * @return  \Yana\Views\NullTemplate
     */
    public function setPath($filename)
    {
        $this->_path = (string) $filename;
        return $this;
    }

    /**
     * Returns a string with the path and name of the current template.
     *
     * @return  string
     */
    public function getPath()
    {
        return $this->_path;
    }

}

?>