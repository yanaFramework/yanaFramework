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
 * For unit tests only.
 *
 * @package     yana
 * @subpackage  views
 * @ignore
 */
class NullManager extends \Yana\Views\Managers\AbstractManager implements \Yana\Views\Managers\IsManager
{

    /**
     * Dummy.
     *
     * @param   string  $filename                 path to template file that hold the page layout (usually: index.tpl)
     * @param   string  $mainContentTemplateName  path to another template file that renders the page content
     * @param   array   $templateVars             possibly multi-dimensional, associative array of template variables
     * @return  \Yana\Views\Templates\IsTemplate
     */
    public function createLayoutTemplate($filename, $mainContentTemplateName, array $templateVars)
    {
        return new \Yana\Views\Templates\NullTemplate();
    }

    /**
     * Dummy.
     *
     * @param   string  $filename  path to template file
     * @return  \Yana\Views\Templates\IsTemplate 
     */
    public function createContentTemplate($filename)
    {
        return new \Yana\Views\Templates\NullTemplate();
    }

    /**
     * Dummy.
     */
    public function clearCache()
    {
        // intentionally left blank
    }

    /**
     * Dummy.
     *
     * @param   string  $name  name of the function
     * @param   mixed   $code  a callable resource
     * @return  self
     */
    public function setFunction($name, $code)
    {
        return $this;
    }

    /**
     * Dummy.
     *
     * @param   string  $name  name of the function
     * @param   mixed   $code  a callable resource
     * @return  self
     */
    public function setModifier($name, $code)
    {
        return $this;
    }

    /**
     * Dummy.
     *
     * @param   string  $name  name of the function
     * @param   mixed   $code  a callable resource
     * @return  self
     */
    public function setBlockFunction($name, $code)
    {
        return $this;
    }

    /**
     * Dummy.
     *
     * @param   string  $name  name of the function
     * @return  self
     */
    public function unsetFunction($name)
    {
        return $this;
    }

    /**
     * Dummy.
     *
     * @param   string  $name  name of the function
     * @return  self
     */
    public function unsetModifier($name)
    {
        return $this;
    }

    /**
     * Dummy.
     *
     * @param   string  $name  name of the function
     * @return  self
     */
    public function unsetBlockFunction($name)
    {
        return $this;
    }

    /**
     * Dummy.
     *
     * @return  \Smarty
     */
    public function getSmarty()
    {
        return new \Smarty();
    }

}

?>