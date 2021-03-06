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

namespace Yana\Db\FileDb;

/**
 * Readonly FileDb-Driver for test purposes.
 *
 * Mapper for sql statements to SML-file commands.
 * It implements only a required subset of the interface
 * of PEAR MDB2 as needed by the DbStream class.
 *
 * @package     yana
 * @subpackage  db
 * @ignore
 * @codeCoverageIgnore
 */
class NullDriver extends \Yana\Db\FileDb\Driver
{

    /**
     * Returns bool(true) if all queries should be committed automatically.
     *
     * @return  bool
     */
    protected function _isAutoCommit(): bool
    {
        return false;
    }

    /**
     * Commit transaction.
     *
     * @param   bool  $commit on / off
     * @return  \Yana\Db\FileDb\Result
     */
    protected function _write(bool $commit = false): \Yana\Db\FileDb\Result
    {
        return parent::_write(false);
    }

    /**
     * Overwrite this with null-object in unit tests to avoid side-effects.
     *
     * @param   \Yana\Files\SML  $smlfile   data (SML object)
     * @param   string           $filename  filename
     * @return  \Yana\Db\FileDb\Index
     * @ignore
     */
    protected function _createIndex(\Yana\Files\SML $smlfile, string $filename): \Yana\Db\FileDb\Index
    {
        return new \Yana\Db\FileDb\NullIndex($this->_getTable(), $smlfile, $filename);
    }

}

?>