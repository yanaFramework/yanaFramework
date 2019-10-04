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
declare(strict_types=1);

namespace Yana\Db\Queries;

/**
 * <<abstract>> Serialize query object to SQL string.
 *
 * @package     yana
 * @subpackage  db
 */
abstract class AbstractQuerySerializer extends \Yana\Core\StdObject implements \Yana\Db\Queries\IsQuerySerializer
{

    /**
     * When TRUE the class should collect values as parameters instead of embedding them into the created SQL statement.
     *
     * This is FALSE by default.
     *
     * @var bool
     */
    private $_collectParametersForBinding = false;

    /**
     * Collects SQL values to bind them to the query, if the collection of parameters is active.
     *
     * @var  array
     */
    private $_queryParameters = array();

    /**
     * <<constructor>> Create instance and setup parameter handling.
     *
     * When the given argument is bool(true) the class should collect values as parameters
     * instead of embedding them into the created SQL statement.
     *
     * @param  bool  $collectParametersForBinding  collect values as parameters, true = yes, false = no
     */
    public function __construct(bool $collectParametersForBinding = false)
    {
        $this->_collectParametersForBinding = $collectParametersForBinding;
    }

    /**
     * Return list of query parameters to bind to the SQL.
     *
     * If no parameters were recorderd, the result will be empty.
     *
     * If parameters exists, the resulting SQL query must have a "?" symbol for each parameter.
     * The order in which the "?" appear in the query match the order of the parameters returned by this function.
     *
     * @return  array
     */
    public function getQueryParameters(): array
    {
        return $this->_queryParameters;
    }

    /**
     * Add a query parameter to bind to the SQL.
     *
     * For each parameter added, the resulting SQL query must have a "?" symbol.
     * The order in which the "?" appear in the query must be identical to the order of parameters.
     *
     * @param   string  $param  value to be bound to SQL query
     * @return  $this
     */
    protected function _bindQueryParameter(string $param)
    {
        $this->_queryParameters[] = $param;
        return $this;
    }

    /**
     * Check whether or not to collect parameters for binding.
     *
     * When TRUE the class should collect values as parameters instead of embedding them into the created SQL statement.
     *
     * The default is FALSE.
     *
     * @return  bool
     */
    protected function _isCollectingParametersForBinding(): bool
    {
        return $this->_collectParametersForBinding;
    }

}

?>