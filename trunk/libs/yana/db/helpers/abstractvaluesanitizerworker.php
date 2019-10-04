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
declare(strict_types=1);

namespace Yana\Db\Helpers;

/**
 * <<abstract>> This does the actual work on the given value.
 *
 * @package     yana
 * @subpackage  db
 */
abstract class AbstractValueSanitizerWorker extends \Yana\Core\StdObject implements \Yana\Db\Helpers\IsValueSanitizerWorker
{

    /**
     * @var  mixed
     */
    private $_value = null;

    /**
     * <<constructor>> Initialize value to work on.
     *
     * @param  mixed  $value  to be sanitized
     */
    public function __construct($value)
    {
        $this->_value = $value;
    }

    /**
     * Returns the given value.
     *
     * @return  mixed
     */
    protected function _getValue()
    {
        return $this->_value;
    }

}

?>