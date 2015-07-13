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

namespace Yana\Mails\Messages;

/**
 * <<entity>> Class for composing mail messages based on form data.
 *
 * @package     yana
 * @subpackage  mails
 */
class TemplateMessage extends \Yana\Mails\Messages\Message implements \Yana\Core\IsVarContainer
{

    /**
     * @var \Yana\Views\Templates\IsTemplate
     */
    private $_template = null;

    /**
     * This sets up the content of the E-Mail from a template of your choice.
     *
     * @param  \Yana\Views\Templates\IsTemplate  $template  E-Mail template
     */
    public function __construct(\Yana\Views\Templates\IsTemplate $template)
    {
        $this->_setTemplate($template);
    }

    /**
     * Get mail template.
     * 
     * @return  \Yana\Views\Templates\IsTemplate
     */
    protected function _getTemplate()
    {
        return $this->_template;
    }

    /**
     * Set mail template.
     *
     * @param   \Yana\Views\Templates\IsTemplate  $template  some mail template
     * @return  \Yana\Mails\Messages\TemplateMessage
     */
    protected function _setTemplate(\Yana\Views\Templates\IsTemplate $template)
    {
        $this->_template = $template;
        return $this;
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
        return $this->_getTemplate()->isVar($key);
    }

    /**
     * Get registered vars.
     *
     * @return  array
     */
    public function getVars()
    {
        return $this->_getTemplate()->getVars();
    }

    /**
     * Get registered var.
     *
     * @param   string  $key  variable-name
     * @return  mixed
     */
    public function getVar($key)
    {
        assert('is_string($key)', ' Wrong argument type for argument 1. String expected.');

        return $this->_getTemplate()->getVar($key);
    }

    /**
     * Assign a variable to a key by value.
     *
     * @param   string  $varName  address
     * @param   mixed   $var      some new value
     * @return  \Yana\Mails\Mailer
     */
    public function setVar($varName, $var)
    {
        assert('is_string($varName)', ' Wrong argument type for argument 1. String expected.');
        $this->_getTemplate()->setVar($varName, $var);
        return $this;
    }

    /**
     * Assign a new set of variables.
     *
     * This replaces all template vars with new ones.
     *
     * @param   array  $vars  associative array containg new set of template vars
     * @return  \Yana\Mails\Mailer
     */
    public function setVars(array $vars)
    {
        $this->_getTemplate()->setVars($vars);
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
     * @return  \Yana\Mails\Mailer
     */
    public function setVarByReference($varName, &$var)
    {
        assert('is_string($varName)', ' Invalid argument $varName: string expected');

        $this->_getTemplate()->setVarByReference($varName, $var);
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
     * @return  \Yana\Mails\Mailer
     */
    public function setVarsByReference(array &$vars)
    {
        $this->_getTemplate()->setVarsByReference($vars);
        return $this;
    }

    /**
     * Set the path of the template to "string:$text".
     *
     * Note: injection of templates as string must be supported by the used template engine.
     * (It is supported with the default settings.
     * If you changed these, it is in your responsibility to ensure it still works.)
     *
     * @param   string  $text  new template contents
     * @return  \Yana\Mails\Messages\TemplateMessage
     */
    public function setText($text)
    {
        $this->_getTemplate()->setPath('string:' . $text);
        return $this;
    }

    /**
     * Fetches the contents of the template.
     *
     * @return  string
     */
    public function getText()
    {
        return $this->_getTemplate()->fetch();
    }

}

?>