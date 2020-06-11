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
declare(strict_types=1);

namespace Yana\Views\Helpers\Html;

/**
 * <<abstract>> HTML element.
 *
 * Allows to set standard HTML attributes: id, title aso.
 *
 * @package     yana
 * @subpackage  views
 */
abstract class AbstractHelper extends \Yana\Core\StdObject
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
    public function getId(): string
    {
        return $this->_id;
    }

    /**
     * Set HTML attribute "id".
     *
     * @param   string  $id  must be valid unique identifier
     * @return  $this
     */
    public function setId(string $id)
    {
        $this->_id = \Yana\Util\Strings::htmlSpecialChars($id, ENT_QUOTES);
        return $this;
    }

    /**
     * Get HTML attribute "name".
     *
     * @return  string
     */
    public function getName(): string
    {
        return $this->_name;
    }

    /**
     * Set HTML attribute "name".
     *
     * @param   string  $name  must be valid unique identifier
     * @return  $this
     */
    public function setName(string $name)
    {
        $this->_name = \Yana\Util\Strings::htmlSpecialChars($name, ENT_QUOTES);
        return $this;
    }

    /**
     * Get HTML attribute "class".
     *
     * @return  string
     */
    public function getCssClass(): string
    {
        return $this->_class;
    }

    /**
     * Set HTML attribute "class".
     *
     * @param   string  $class  must be valid CSS class name
     * @return  $this
     */
    public function setCssClass(string $class)
    {
        $this->_class = \Yana\Util\Strings::htmlSpecialChars($class, ENT_QUOTES);
        return $this;
    }

    /**
     * Get HTML attribute "title".
     *
     * @return  string
     */
    public function getTitle(): string
    {
        return $this->_title;
    }

    /**
     * Set HTML attribute "id".
     *
     * @param   string  $title  any text without HTML code
     * @return  $this
     */
    public function setTitle(string $title)
    {
        $this->_title = \Yana\Util\Strings::htmlSpecialChars($title, ENT_QUOTES);
        return $this;
    }

    /**
     * Get HTML attribute "maxlength".
     *
     * If the var has no maximum length at all, the function will return a number < 1.
     *
     * @return  int
     */
    public function getMaxLength(): int
    {
        return $this->_maxLength;
    }

    /**
     * Set HTML attribute "maxlength".
     *
     * To reset the value, set it to 0.
     *
     * @param   int  $maxLength  must be a positive number
     * @return  $this
     */
    public function setMaxLength(int $maxLength)
    {
        assert($maxLength >= 0, 'Invalid argument $maxLength: must be >= 0');
        $this->_maxLength = $maxLength;
        return $this;
    }

    /**
     * Get other HTML attributes.
     *
     * @return  string
     */
    public function getAttr(): string
    {
        return $this->_attr;
    }

    /**
     * Set other HTML attributes as HTML code.
     *
     * @param   string  $attr  list of HTML attributes.
     * @return  $this
     */
    public function setAttr(string $attr)
    {
        $this->_attr = \Yana\Util\Strings::htmlSpecialChars($attr, ENT_NOQUOTES);
        return $this;
    }

}

?>