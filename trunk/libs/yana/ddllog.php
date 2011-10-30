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
     * tags for persistance mapping: object <-> XDDL
     * @var  array
     */
    protected $xddlTags = array(
        'description' => array('description', 'string')
    );

    /** 
     * @access  protected
     * @var     string
     * @ignore
     */
    protected $description = null;

    /**
     * Get parent database.
     *
     * @return  DDLDatabase
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Get version string.
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
     * Set version string.
     *
     * The version this log-entry applies to.
     *
     * To reset this option, call the function with an empty parameter.
     *
     * @access  public
     * @param   string  $version  new value of this property
     * @return  DDLLog
     */
    public function setVersion($version = "")
    {
        assert('is_string($version); // Wrong type for argument 1. String expected');
        if (empty($version)) {
            $this->version = null;
        } else {
            $this->version = "$version";
        }
        return $this;
    }

    /**
     * Returns a custom log-message.
     *
     * Note that this is free-text that may contain any format.
     *
     * @access  public
     * @return  string
     */
    public function getDescription()
    {
        if (is_string($this->description)) {
            return $this->description;
        } else {
            return null;
        }
    }

    /**
     * Set description.
     *
     * @access  public
     * @param   string  $description  a log-message of your choice
     * @return  DDLLog
     */
    public function setDescription($description)
    {
        assert('is_string($description); // Wrong type for argument 1. String expected');
        if (empty($description)) {
            $this->description = null;
        } else {
            $this->description = "$description";
        }
        return $this;
    }

    /**
     * Check wether to ignore errors.
     *
     * @access  public
     * @return  bool
     */
    public function ignoreError()
    {
        return !empty($this->ignoreError);
    }

    /**
     * Set wether to ignore errors.
     *
     * @access  public
     * @param   bool  $ignoreError  ignore errors
     * @return  DDLLog
     */
    public function setIgnoreError($ignoreError)
    {
        assert('is_bool($ignoreError); // Wrong argument type for argument 1. Boolean expected');
        $this->ignoreError = (bool) $ignoreError;
        return $this;
    }

    /**
     * Set function to handle updates.
     *
     * @access  public
     * @param   string|array  $functionName   name of the function which is called
     * @throws   \Yana\Core\Exceptions\InvalidArgumentException  when the given function is not callable
     */
    public static function setHandler($functionName)
    {
        if (is_callable($functionName)) {
            self::$handler = $functionName;
        } else {
            $message = "The function name '$functionName' is not callable.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, E_USER_WARNING);
        }
    }

    /**
     * Carry out the update.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @access  public
     * @abstract
     * @return  bool
     */
    abstract public function commitUpdate();

    /**
     * Get type of update.
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