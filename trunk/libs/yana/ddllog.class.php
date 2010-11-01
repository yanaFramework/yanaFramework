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

/**
 * database change-log
 *
 * This wrapper class represents the structure of a database
 *
 * @access      public
 * @abstract
 * @package     yana
 * @subpackage  database
 */
abstract class DDLLog extends DDL
{
    /**
     * class name must match exactly
     *
     * @var  bool
     */
    protected $exactMatch = true;

    /**
     * version string
     *
     * @access  protected
     * @var     string
     * @ignore
     */
    protected $version = null;

    /**
     * ignore errors
     *
     * @access  protected
     * @var     bool
     * @ignore
     */
    protected $ignoreError = false;

    /**
     * name of function to apply changes to the database structure
     *
     * Note: the implementation, number and type of arguments depend on the
     * type of changes that have to be carried out.
     *
     * @access  protected
     * @static
     * @var string
     * @ignore
     */
    protected static $handler = null;

    /**
     * Parent element
     *
     * @access  protected
     * @var     DDLChangeLog
     * @ignore
     */
    protected $parent = null;

    /**
     * get parent
     *
     * @return  DDLDatabase
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * get version string
     *
     * The data-type returned by the function.
     * Will return NULL if no version is set.
     *
     * @access  public
     * @return  string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * set version string
     *
     * The version this log-entry applies to.
     *
     * To reset this option, call the function with an empty parameter.
     *
     * @access  public
     * @param   string  $version    new value of this property
     */
    public function setVersion($version = "")
    {
        assert('is_string($version); // Wrong type for argument 1. String expected');
        if (empty($version)) {
            $this->version = null;
        } else {
            $this->version = "$version";
        }
    }

    /**
     * check wether to ignore errors
     *
     * @access  public
     * @return  bool
     */
    public function ignoreError()
    {
        if (!empty($this->ignoreError)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * set wether to ignore errors
     *
     * @access  public
     * @param   bool  $ignoreError  ignore errors
     */
    public function setIgnoreError($ignoreError)
    {
        assert('is_bool($ignoreError); // Wrong argument type for argument 1. Boolean expected');
        if (!empty($ignoreError)) {
            $this->ignoreError = true;
        } else {
            $this->ignoreError = false;
        }
    }

    /**
     * set function to handle updates
     *
     * @access  public
     * @param   string|array  $functionName   name of the function which is called
     * @throws  InvalidArgumentException
     */
    public static function setHandler($functionName)
    {
        if (is_callable($functionName)) {
            self::$handler = $functionName;
        } else {
            throw new InvalidArgumentException("The function name '$functionName' is not callable.", E_USER_WARNING);
        }
    }

    /**
     * carry out the update
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @access  public
     * @abstract
     * @return  bool
     */
    abstract public function commitUpdate();

    /**
     * get type of update
     *
     * Returns a string that is equivalent to the XDDL tag of this object.
     *
     * @access  public
     * @return  string
     */
    public function getType()
    {
        return $this->xddlTag;
    }

}

?>