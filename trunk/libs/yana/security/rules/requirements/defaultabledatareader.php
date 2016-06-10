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

namespace Yana\Security\Rules\Requirements;

/**
 * Adds a default value to the wrapped adapter.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class DefaultableDataReader extends \Yana\Security\Rules\Requirements\DataReader
{

    /**
     * @var  array
     */
    private $_default = array();

    /**
     * Initialize defaults.
     *
     * @param  array  $default  added default settings
     */
    public function __construct(array $default = array())
    {
        $this->_default = $default;
    }

    /**
     * Get default settings.
     *
     * @return  array
     */
    protected function _getDefault()
    {
        return $this->_default;
    }

    /**
     * Find and return (active) requirements for the given action.
     *
     * An exception is thrown if the datasource is empty.
     * If the datasource is not empty, but no requirements are found for (this) action nonetheless, an empty collection will be returned.
     *
     * @param   string  $action  loaded requirements must be associated with this rule
     * @return  \Yana\Security\Rules\Requirements\Collection
     * @throws  \Yana\Security\Rules\Requirements\NotFoundException  when no rules are found in the datasource
     */
    public function loadRequirementsByAssociatedAction($action)
    {
        assert('is_string($action); // Invalid argument type: $action. String expected');

        assert('!isset($requirements); // Cannot redeclare var $requirements');
        $requirements = parent::loadRequirementsByAssociatedAction($action);

        if ($requirements->count() === 0) {
            assert('!isset($default); // Cannot redeclare var $default');
            $default = $this->_getDefault();
            if (!empty($default)) {
                $requirements[] = $this->_mapRowFromDatabasetoEntity($default);
            }
            unset($default);
        }

        return $requirements;
    }

}

?>