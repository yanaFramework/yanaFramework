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
 * <<abstract>> Template.
 *
 * This implements a decorator class for Smarty templates.
 * It provides a cleaned up, simple interface targeted for ease of use.
 *
 * @package     yana
 * @subpackage  views
 * @codeCoverageIgnore
 */
abstract class AbstractTemplate extends \Yana\Core\Object implements \Yana\Views\Templates\IsTemplate
{

    /**
     * Template instance.
     *
     * @var  \Smarty_Internal_Template
     */
    private $template = null;

    /**
     * <<constructor>> Create an instance.
     *
     * @param  \Smarty_Internal_Template  $template  template object
     */
    public function __construct(\Smarty_Internal_Template $template)
    {
        $this->template = $template;
    }

    /**
     * Returns template instance.
     *
     * @return \Smarty_Internal_Template
     */
    protected function _getTemplate()
    {
        return $this->template;
    }

}

?>