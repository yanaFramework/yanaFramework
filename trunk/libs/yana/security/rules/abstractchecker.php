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

namespace Yana\Security\Rules;

/**
 * <<abstract>> Rule checking class.
 *
 * Allows collection and checking of security rules.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
abstract class AbstractChecker extends \Yana\Core\Object implements \Yana\Security\Rules\IsChecker
{

    /**
     * Requirements adapter.
     *
     * @var  \Yana\Security\Rules\Requirements\IsDataReader
     */
    private $_requirementsAdapter = null;

    /**
     * @var  \Yana\Security\Rules\RuleCollection
     */
    private $_rules = null;

    /**
     * Returns collection of security rules.
     *
     * Lazy-loads one, if none exists.
     *
     * @return  \Yana\Security\Rules\RuleCollection
     */
    protected function _getRules()
    {
        if (!isset($this->_rules)) {
            $this->_rules = new \Yana\Security\Rules\RuleCollection();
        }
        return $this->_rules;
    }

    /**
     * Set requirements-adapter.
     *
     * @param  \Yana\Security\Rules\Requirements\IsDataReader  $adapter  to load requirements
     */
    public function __construct(\Yana\Security\Rules\Requirements\IsDataReader $adapter)
    {
        $this->_requirementsAdapter = $adapter;
    }

    /**
     * Get requirements adapter.
     *
     * @return  \Yana\Security\Rules\Requirements\IsDataReader
     */
    protected function _getRequirementsAdapter()
    {
        return $this->_requirementsAdapter;
    }

}

?>