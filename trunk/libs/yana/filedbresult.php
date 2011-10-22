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

/**
 * FileDbResult
 *
 * This class represents a FileDB resultset.
 *
 * @access      public
 * @package     yana
 * @subpackage  database
 *
 * @ignore
 */
class FileDbResult extends Object
{

    /**#@+
     * @ignore
     * @access  private
     */

    /** @var array */  private $result = array();
    /** @var string */ private $message = '';

    /**#@-*/

    /**
     * constructor
     *
     * Create a new resultset.
     *
     * @param  mixed   $result   resultset (set "null" for error)
     * @param  string  $message  error message
     */
    public function __construct($result, $message = '')
    {
        /* settype to STRING */
        $message = (string) $message;
        if (is_null($result)) {
            $this->result = null;
        } else {
            assert('is_array($result);');
            $this->result = \Yana\Util\Hashtable::changeCase($result, CASE_LOWER);
        }
        $this->message  = trim($message);
    }

    /**
     * get number of rows in resultset
     *
     * returns the number of rows in the result set
     *
     * @access  public
     * @return  int
     */
    public function numRows()
    {
        return count($this->result);
    }

    /**
     * fetch a row from the resultset
     *
     * returns an associatvie array of the row at index $i in the result set
     *
     * @access  public
     * @param   mixed  $dummy  (ignored) this is here for compatibility reasons
     * @param   int    $i      row number
     * @return  array
     */
    public function fetchRow($dummy, $i)
    {
        /* settype to INTEGER */
        $i = (int) $i;
        if (isset($this->result[$i])) {
            return $this->result[$i];
        } else {
            return array();
        }
    }

    /**
     * get error message
     *
     * This returns an error message (if any)
     * if the resultset is in error-state.
     *
     * If there is none, an empty string is
     * returned instead.
     *
     * Use FileDbResult::isError() to check,
     * if the result is in an error state.
     *
     * @access  public
     * @return  string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * check wether the result is an error
     *
     * Returns bool(true) if the request resulted
     * in an error state and bool(false) otherwise.
     *
     * {@internal
     * If something went wrong, the property "result"
     * is not set. This means, $this->result is NULL.
     * }}
     *
     * @access  public
     * @return  bool
     */
    public function isError()
    {
        return is_null($this->result);
    }

    /**
     * compare with another object
     *
     * Returns bool(true) if this object and $anotherObject
     * are equal and bool(false) otherwise.
     *
     * Two instances are considered equal if and only if
     * they are both objects of the same class and their
     * attributes are equal.
     *
     * @access   public
     * @param    object  $anotherObject     another object too compare
     * @return   string
     */
    public function equals(object $anotherObject)
    {
        if ($anotherObject == $this) {
            return true;
        } else {
            return false;
        }
    }

}

?>