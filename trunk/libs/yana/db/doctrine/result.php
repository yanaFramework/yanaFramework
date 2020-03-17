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

namespace Yana\Db\Doctrine;

/**
 * <<wrapper>> Represents a Doctrine DBAL resultset.
 *
 * @package     yana
 * @subpackage  db
 *
 * @ignore
 * @codeCoverageIgnore
 */
class Result extends \Yana\Db\Doctrine\AbstractResult
{

    /**
     * @var \Doctrine\DBAL\Driver\Statement
     */
    private $_result = null;

    /**
     * Creates a new resultset.
     *
     * @param  \Doctrine\DBAL\Driver\Statement  $result  resultset
     */
    public function __construct(\Doctrine\DBAL\Driver\Statement $result)
    {
        $this->_result = $result;
    }

    /**
     * Returns resultset.
     *
     * @return  \Doctrine\DBAL\Driver\Statement
     */
    protected function _getResult()
    {
        return $this->_result;
    }

}

?>