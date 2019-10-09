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

namespace Yana\Core\MetaData;

/**
 * Collection of data providers.
 *
 * @package     yana
 * @subpackage  core
 * @ignore
 */
class DataProviderCollection extends \Yana\Core\AbstractCollection
{

    /**
     * Add or replace a data provider.
     *
     * @param   scalar                               $offset  index of item to replace
     * @param   \Yana\Core\MetaData\IsDataProvider   $value   new value of item
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if the value is not a valid collection item
     * @return  \Yana\Core\MetaData\IsDataProvider
     */
    public function offsetSet($offset, $value)
    {
        assert(is_null($offset) || is_scalar($offset), '$offset expected to be Scalar');
        if (!$value instanceof \Yana\Core\MetaData\IsDataProvider) {
            $message = 'Instance of IsDataProvider expected';
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, $level);
        }
        $this->_offsetSet($offset, $value);
        return $value;
    }

}

?>