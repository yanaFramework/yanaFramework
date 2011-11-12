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

namespace Yana\Forms\Fields;

/**
 * <<abstract>> HTML element.
 *
 * Allows to set standard HTML attributes: id, title aso.
 *
 * @package     yana
 * @subpackage  form
 */
abstract class AbstractHtmlElement extends \Yana\Core\Object
{

    /**
     * HTML attribute "id".
     *
     * @var  string
     */
    private $_id = "";

    /**
     * HTML attribute "name".
     *
     * @var  string
     */
    private $_name = "";

    /**
     * HTML attribute "title".
     *
     * @var     string
     */
    private $_title = "";

    /**
     * HTML attribute "class".
     *
     * @var  string
     */
    private $_class = "";

    /**
     * HTML attribute "maxlength".
     *
     * @var  int
     */
    private $_maxLength = 0;

    /**
     * Other HTML attributes.
     *
     * @var  string
     */
    private $_attr = "";

    /**
     * Get HTML attribute "id".
     *
     * @var  string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Set HTML attribute "id".
     *
     * @param   string  $id  must be valid unique identifier
     * @return  \Yana\Forms\Fields\HtmlBuilder 
     */
    public function setId($id)
    {
        assert('is_string($id); // Invalid argument $id: string expected');
        $this->_id = \Yana\Util\String::htmlSpecialChars($id, ENT_QUOTES);
        return $this;
    }

    /**
     * Get HTML attribute "name".
     *
     * @return  string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Set HTML attribute "name".
     *
     * @param   string  $name  must be valid unique identifier
     * @return  \Yana\Forms\Fields\HtmlBuilder 
     */
    public function setName($name)
    {
        assert('is_string($name); // Invalid argument $name: string expected');
        $this->_name = \Yana\Util\String::htmlSpecialChars($name, ENT_QUOTES);
        return $this;
    }

    /**
     * Get HTML attribute "class".
     *
     * @return  string
     */
    public function getCssClass()
    {
        return $this->_class;
    }

    /**
     * Set HTML attribute "class".
     *
     * @param   string  $class  must be valid CSS class name
     * @return  \Yana\Forms\Fields\HtmlBuilder 
     */
    public function setCssClass($class)
    {
        assert('is_string($class); // Invalid argument $class: string expected');
        $this->_class = \Yana\Util\String::htmlSpecialChars($class, ENT_QUOTES);
        return $this;
    }

    /**
     * Get HTML attribute "title".
     *
     * @return  string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Set HTML attribute "id".
     *
     * @param   string  $title  any text without HTML code
     * @return  \Yana\Forms\Fields\HtmlBuilder 
     */
    public function setTitle($title)
    {
        assert('is_string($title); // Invalid argument $id: string expected');
        $this->_title = \Yana\Util\String::htmlSpecialChars($title, ENT_QUOTES);
        return $this;
    }

    /**
     * Get HTML attribute "maxlength".
     *
     * If the var has no maximum length at all, the function will return a number < 1.
     *
     * @return  int
     */
    public function getMaxLength()
    {
        return $this->_maxLength;
    }

    /**
     * Set HTML attribute "maxlength".
     *
     * To reset the value, set it to 0.
     *
     * @param   int  $maxLength  must be a positive number
     * @return  \Yana\Forms\Fields\HtmlBuilder 
     */
    public function setMaxLength($maxLength)
    {
        assert('is_int($maxLength); // Invalid argument $maxLength: int expected');
        assert('$maxLength >= 0; // Invalid argument $maxLength: must be >= 0');
        $this->_maxLength = (int) $maxLength;
        return $this;
    }

    /**
     * Get other HTML attributes.
     *
     * @return  string
     */
    public function getAttr()
    {
        return $this->_attr;
    }

    /**
     * Set other HTML attributes as HTML code.
     *
     * @param   string  $attr  list of HTML attributes.
     * @return  \Yana\Forms\Fields\HtmlBuilder 
     */
    public function setAttr($attr)
    {
        assert('is_string($attr); // Invalid argument $attr: string expected');
        $this->_attr = \Yana\Util\String::htmlSpecialChars($attr, ENT_NOQUOTES);
        return $this;
    }

}

?>