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

/**
 * View
 *
 * A manager class to automate searching and loading of templates, belonging to view layer.
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
 * @subpackage  templates
 */
class SmartView extends SmartTemplate
{

    /**
     * select template by template id
     *
     * Will select the templates for frame and content of the view,
     * where $frameTemplate and $frameTemplate are the ids of the
     * selected templates.
     *
     * Returns bool(true) on success or bool(false) on error.
     *
     * Example of usage:
     * <code>
     * Yana::getInstance()->getView()->setTemplate('index');
     * </code>
     *
     * @param   string  $template  id or path of template
     * @return  bool
     */
    public function setTemplate($template = null)
    {
        if (is_null($template)) {
            $this->setVar('SYSTEM_INSERT', '');
            return true;
        }
        assert('is_string($template); // Wrong argument type argument 1. String expected');

        /* AJAX-check
         *
         * If this is an AJAX request we should produce shortened output.
         * "Shortened" here means, we leave off the static document "frame" and
         * restrict the output to the template's body-tag (if any).
         * This is done by the output post-processor, thus causing minimum side-effects.
         */
        if (\Request::getVars('is_ajax_request')) {
            $this->setVar('FILE_IS_INCLUDE', true);
            if (headers_sent() === false) {
                header('Content-Type: text/html; charset=UTF-8');
            }
            return $this->setPath($template);
        }
        try {
            $template = self::_getFilename($template);
        } catch (\NotFoundException $e) {
            trigger_error("Template not found: " . $e->getMessage(), E_USER_WARNING);
            return false;
        }
        $this->setVar('SYSTEM_INSERT', $template);
        return true;
    }

    /**
     * Get template filename by id.
     *
     * @param   string  $id  template id
     * @return  string
     * @throws  \Yana\Core\Exceptions\NotFoundException  when id is not found
     */
    private static function _getFilename($id)
    {
        if (!is_file($id)) {
            $id = \Yana::getInstance()->getSkin()->getFile($id); // throws NotFoundException
        }
        return $id;
    }

    /**
     * set path to base document
     *
     * A view always consists of two parts: a base document and an included document.
     *
     * This sets the base document.
     * This setting is shared amongst all views.
     *
     * @param   string  $filename  file name
     * @return  bool
     */
    public function setPath($filename)
    {
        assert('is_string($filename); // Wrong argument type argument 1. String expected');
        try {
            $filename = self::_getFilename($filename);
        } catch (\NotFoundException $e) {
            trigger_error("Template not found: " . $e->getMessage(), E_USER_WARNING);
            return false;
        }
        $this->setVar('SYSTEM_TEMPLATE', $filename);
        return parent::setPath($filename);
    }

    /**
     * fetch a template
     *
     * This function will fetch the current template and return it
     * as a string.
     *
     * Variables are imported from the global registry to the template.
     * Existing template vars will be replaced by vars of the same name
     * in the registry.
     *
     * @return  string
     */
    public function __toString()
    {
        // import vars from global registry and overwrite local vars
        if (isset($GLOBALS['YANA'])) {
            $this->template->assign($GLOBALS['YANA']->getVar('*'));
        }
        return parent::__toString();
    }

    /**
     * output a message to browser and terminate the program
     *
     * NOTE: This function will terminate program execution
     *
     * @param  string  $type      alert, error, aso.
     * @param  string  $event     event to trigger, e.g. 'index'
     * @param  string  $template  id of template to use
     */
    public function showMessage($type, $event = "null", $template = "message")
    {
        assert('is_string($type); // Wrong type for argument 1. String expected');
        assert('is_string($event); // Wrong type for argument 2. String expected');
        assert('is_string($template); // Wrong type for argument 3. String expected');

        $event = mb_strtolower("$event");
        $this->setPath($template);

        $this->setVar('ACTION', $event);
        $this->setVar('STDOUT.LEVEL', mb_strtolower("$type"));

        exit((string) $this);
    }

}

?>