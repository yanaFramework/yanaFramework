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
 * @ignore
 */

namespace Yana\Db\Helpers\Triggers;

/**
 * <<abstract>> Internal class meant to help evaluate triggers.
 *
 * @package     yana
 * @subpackage  db
 */
abstract class AbstractTrigger extends \Yana\Core\Object implements \Yana\Db\Helpers\Triggers\IsTrigger
{

    /**
     * @var  \Yana\Db\Helpers\Triggers\Container
     */
    protected $_container = null;

    /**
     * Returns the name of the function to call to evaluate the trigger.
     *
     * @return  callable
     */
    abstract protected function _getTriggerFunction();

    /**
     * Returns an element of {@see \Yana\Db\Helpers\Triggers\TypeEnumeration}.
     *
     * @return  string
     */
    abstract protected function _getTriggerType();

    /**
     * @param  \Yana\Db\Helpers\Triggers\Container  $container  passed to called function that contains the trigger code
     */
    public function __construct(\Yana\Db\Helpers\Triggers\Container $container)
    {
        $this->_container = $container;
    }

    /**
     * Evaluate triggers.
     */
    public function __invoke()
    {
        $functionName = $this->_getTriggerFunction();
        if (is_callable($functionName)) {
            call_user_func($functionName, $this->_container);
        }
    }

}

?>