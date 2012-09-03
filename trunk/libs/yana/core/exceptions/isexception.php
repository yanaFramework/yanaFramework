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

namespace Yana\Core\Exceptions;

/**
 * <<interface>> Use this for exception classes.
 *
 * @package     yana
 * @subpackage  core
 */
interface IsException
{

    /**
     * Returns given context data (where available).
     *
     * @return  mixed
     */
    public function getData();

    /**
     * Set context data.
     *
     * You may provide parameters and additional context information to an exception.
     * E.g. for a failed insert-query you may provide the row which you could not insert.
     *
     * If you are using language references, you may use this to replace tokens used in the translation.
     *
     * Example usage:
     * <code>
     * $data = array('FILE' => $filename);
     * $error = new MyFileException();
     * $error->setData($data);
     * throw $error;
     * </code>
     *
     * @param   mixed  $data  context data
     * @return  \Yana\Core\Exceptions\AbstractException
     */
    public function setData($data);

    /**
     * Get the Exception message.
     *
     * @return string
     */
    public function getMessage();

    /**
     * Get the Exception code.
     *
     * @return mixed
     */
    public function getCode();

    /**
     * Get path to file where exception was thrown.
     *
     * @return string
     */
    public function getFile();

    /**
     * Get line at which the exception was thrown.
     *
     * @return int
     */
    public function getLine();

    /**
     * Get stack-trace.
     *
     * @return array
     */
    public function getTrace();

    /**
     * Returns previously caught Exception or NULL if this is the root exception.
     *
     * @return \Exception
     */
    public function getPrevious();

    /**
     * Returns stack-trace as a string.
     *
     * @return string
     */
    public function getTraceAsString();

}

?>