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

namespace Yana\Db\Ddl\Logs;

/**
 * database change-log
 *
 * This wrapper class represents the structure of a database
 *
 * @package     yana
 * @subpackage  db
 */
abstract class AbstractLog extends \Yana\Db\Ddl\DDL
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
     * @var  string
     * @ignore
     */
    protected $version = null;

    /**
     * ignore errors
     *
     * @var  bool
     * @ignore
     */
    protected $ignoreError = false;

    /**
     * name of function to apply changes to the database structure
     *
     * Note: the implementation, number and type of arguments depend on the
     * type of changes that have to be carried out.
     *
     * @var  string
     * @ignore
     */
    protected static $handler = null;

    /**
     * Parent element
     *
     * @var  \Yana\Db\Ddl\ChangeLog
     * @ignore
     */
    protected $parent = null;

    /**
     * tags for persistance mapping: object <-> XDDL
     *
     * @var  array
     */
    protected $xddlTags = array(
        'description' => array('description', 'string')
    );

    /** 
     * @var  string
     * @ignore
     */
    protected $description = null;

    /**
     * Get parent database.
     *
     * @return  \Yana\Db\Ddl\Database
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
     * @return  string|NULL
     */
    public function getVersion(): ?string
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
     * @param   string  $version  new value of this property
     * @return  $this
     */
    public function setVersion(string $version = "")
    {
        if (empty($version)) {
            $this->version = null;
        } else {
            $this->version = $version;
        }
        return $this;
    }

    /**
     * Returns a custom log-message.
     *
     * Note that this is free-text that may contain any format.
     *
     * @return  string|NULL
     */
    public function getDescription(): ?string
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
     * @param   string  $description  a log-message of your choice
     * @return  $this
     */
    public function setDescription(string $description)
    {
        if (empty($description)) {
            $this->description = null;
        } else {
            $this->description = $description;
        }
        return $this;
    }

    /**
     * Check wether to ignore errors.
     *
     * @return  bool
     */
    public function ignoreError(): bool
    {
        return !empty($this->ignoreError);
    }

    /**
     * Set wether to ignore errors.
     *
     * @param   bool  $ignoreError  ignore errors
     * @return  $this
     */
    public function setIgnoreError(bool $ignoreError)
    {
        $this->ignoreError = $ignoreError;
        return $this;
    }

    /**
     * Set function to handle updates.
     *
     * @param   callable  $functionName   name of the function which is called
     */
    public static function setHandler(callable $functionName)
    {
        self::$handler = $functionName;
    }

    /**
     * Reset handler function.
     */
    public static function dropHandler()
    {
        self::$handler = null;
    }

    /**
     * Carry out the update.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @return  bool
     */
    abstract public function commitUpdate(): bool;

    /**
     * Get type of update.
     *
     * Returns a string that is equivalent to the XDDL tag of this object.
     *
     * @return  string|NULL
     */
    public function getType(): ?string
    {
        return $this->xddlTag;
    }

}

?>