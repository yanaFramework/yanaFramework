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

namespace Yana\Db\Mdb2;

/**
 * <<factory>> Takes a MDB2 error constant and returns the appropriate exception.
 *
 * @package     yana
 * @subpackage  db
 */
class ExceptionFactory extends \Yana\Core\Object implements \Yana\Db\Mdb2\IsExceptionFactory
{

    /**
     * Convert MDB2 error code to the appropriate exception object.
     *
     * This also sets the message according to the given type.
     *
     * @param   int  $errorCode  Some \MDB2_ERROR code
     * @return  \Yana\Core\Exceptions\AbstractException
     */
    public function toException($errorCode)
    {
        assert('is_int($errorCode); // Invalid argument $errorCode: int expected');

        switch ($errorCode)
        {
            case \MDB2_ERROR_ACCESS_VIOLATION:
                return new \Yana\Db\Queries\Exceptions\SecurityException("Access violation.");

            case \MDB2_ERROR_ALREADY_EXISTS:
                return new \Yana\Db\Queries\Exceptions\DuplicateValueException("Already exists.");

            case \MDB2_ERROR_CANNOT_ALTER:
                return new \Yana\Db\Queries\Exceptions\QueryException("Cannot alter table.");

            case \MDB2_ERROR_CANNOT_CREATE:
                return new \Yana\Db\Queries\Exceptions\QueryException("Cannot create table.");

            case \MDB2_ERROR_CANNOT_DELETE:
                return new \Yana\Db\Queries\Exceptions\QueryException("Cannot delete row.");

            case \MDB2_ERROR_CANNOT_DROP:
                return new \Yana\Db\Queries\Exceptions\QueryException("Cannot drop object.");

            case \MDB2_ERROR_CANNOT_REPLACE:
                return new \Yana\Db\Queries\Exceptions\QueryException("Cannot replace row.");

            case \MDB2_ERROR_CONNECT_FAILED:
                return new \Yana\Db\ConnectionException("Cannot connect to database");

            case \MDB2_ERROR_CONSTRAINT:
                return new \Yana\Db\Queries\Exceptions\ConstraintException("Constraint violation.");

            case \MDB2_ERROR_CONSTRAINT_NOT_NULL:
                return new \Yana\Db\Queries\Exceptions\ConstraintException("Not Null constraint violation.");

            case \MDB2_ERROR_DISCONNECT_FAILED:
                return new \Yana\Db\ConnectionException("Cannot disconnect from database");

            case \MDB2_ERROR_DIVZERO:
                return new \Yana\Db\Queries\Exceptions\QueryException("Division by zero.");

            case \MDB2_ERROR_INVALID_NUMBER:
                return new \Yana\Db\Queries\Exceptions\InvalidSyntaxException("Invalid Number.");

            case \MDB2_ERROR_NODBSELECTED:
                return new \Yana\Db\ConnectionException("No database selected.");

            case \MDB2_ERROR_NOSUCHDB:
                return new \Yana\Db\Queries\Exceptions\DatabaseNotFoundException();

            case \MDB2_ERROR_NOSUCHFIELD:
                return new \Yana\Db\Queries\Exceptions\ColumnNotFoundException();

            case \MDB2_ERROR_NOSUCHTABLE:
                return new \Yana\Db\Queries\Exceptions\TableNotFoundException();

            case \MDB2_ERROR_UNSUPPORTED:
            case \MDB2_ERROR_NOT_CAPABLE:
                return new \Yana\Core\Exceptions\NotImplementedException();

            case \MDB2_ERROR_NO_PERMISSION:
                return new \Yana\Db\Queries\Exceptions\SecurityException("Permission denied.");

            case \MDB2_ERROR_INVALID:
                return new \Yana\Db\Queries\Exceptions\InvalidSyntaxException("Invalid SQL.");

            case \MDB2_ERROR_SYNTAX:
                return new \Yana\Db\Queries\Exceptions\InvalidSyntaxException("Error in SQL syntax.");

            case \MDB2_ERROR_TRUNCATED:
                return new \Yana\Db\Queries\Exceptions\NotFoundException("Requested value not found.");

            case \MDB2_ERROR_VALUE_COUNT_ON_ROW:
                $message = "The 'insert'-statement has fewer columns than values specified in the 'values'-clause.";
                return new \Yana\Db\Queries\Exceptions\InvalidSyntaxException($message);

            case \MDB2_ERROR:
            default:
                return new \Yana\Db\DatabaseException();
        }
    }

}

?>