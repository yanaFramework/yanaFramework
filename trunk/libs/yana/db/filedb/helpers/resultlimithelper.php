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

namespace Yana\Db\FileDb\Helpers;

/**
 * Result object helper.
 *
 * @package     yana
 * @subpackage  db
 */
class ResultLimitHelper extends \Yana\Core\StdObject
{

    /**
     * @var array
     */
    private $_resultArray = array();

    /**
     * <<constructor>> Initialize object.
     *
     * @param  array  $resultArray  result set to work on
     */
    public function __construct(array $resultArray)
    {
        $this->_resultArray = $resultArray;
    }

    /**
     * Apply limits and offsets.
     *
     * @param  int    $offset   must be a positive integer greater int(0), otherwise defaults to the lenght of the resultset
     * @param  int    $limit    must be a positive integer, otherwise defaults to int(0)
     */
    public function __invoke(int $offset, int $limit): array
    {
        if ($limit > 0 || $offset > 0) {
            if ($limit <= 0) {
                $limit = count($this->_resultArray);
            }
            if ($offset < 0) {
                $offset = 0;
            }
            $this->_resultArray = array_slice($this->_resultArray, $offset, $limit);
        }
        return $this->_resultArray;
    }
}

?>