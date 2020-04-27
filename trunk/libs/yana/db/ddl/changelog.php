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

namespace Yana\Db\Ddl;

/**
 * database structure: change-log definition
 *
 * The change-log element of a database definition is meant to store documentation about updates.
 * Each update is stored as a sorted list, with the most recent update first.
 *
 * The updates contain the type of the change, a version number and what has changed.
 * A description may be added to describe the changes as well.
 *
 * @package     yana
 * @subpackage  db
 */
class ChangeLog extends \Yana\Db\Ddl\DDL
{
    /**#@+
     * @ignore
     */

    /**
     * tag name for persistance mapping: object <-> XDDL
     * @var  string
     */
    protected $xddlTag = "changelog";

    /**
     * tags for persistance mapping: object <-> XDDL
     * @var  array
     */
    protected $xddlTags = array(
        'create' => array('changes', 'array', 'Yana\Db\Ddl\Logs\Create'),
        'rename' => array('changes', 'array', 'Yana\Db\Ddl\Logs\Rename'),
        'drop'   => array('changes', 'array', 'Yana\Db\Ddl\Logs\Drop'),
        'update' => array('changes', 'array', 'Yana\Db\Ddl\Logs\Update'),
        'sql'    => array('changes', 'array', 'Yana\Db\Ddl\Logs\Sql'),
        'change' => array('changes', 'array', 'Yana\Db\Ddl\Logs\Change')
    );

    /**
     * @var \Yana\Db\Ddl\Logs\AbstractLog[]
     */
    protected $changes = array();

    /**
     * @var DLLDatabase
     */
    protected $parent = null;

    /**#@-*/

    /**
     * Initialize instance.
     *
     * @param  \Yana\Db\Ddl\Database  $parent  parent database
     */
    public function __construct(\Yana\Db\Ddl\Database $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * Get parent database.
     *
     * Returns the declaration of the Datebase the object is defined in.
     *
     * @return  \Yana\Db\Ddl\Database
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add a new change-log entry.
     *
     * This function prepends a new entry at the beginning of the change-log.
     * The change-log is sorting by version-number in descending order.
     *
     * @param   \Yana\Db\Ddl\Logs\AbstractLog  $log  new entry
     */
    public function addEntry(\Yana\Db\Ddl\Logs\AbstractLog $log)
    {
        array_unshift($this->changes, $log);
    }

    /**
     * Delete any change-log entries.
     *
     * @return ChangeLog 
     */
    public function dropEntries()
    {
        $this->changes = array();
        return $this;
    }

    /**
     * List of changes.
     *
     * Returns a list of change-log entries as a numeric array, each of which are instances of
     * \Yana\Db\Ddl\Logs\AbstractLog.
     *
     * The list may be empty. If so, the function returns an empty array.
     *
     * The change-logs are expected to be sorted by version-numbers in descending order, where the
     * top-most entry has the latest version. You may provide a $startVersion. If so, the function
     * will return only entries which are newer. It uses the method version_compare() for comparison
     * of version-strings. If you don't provide a version, all entries will be returned.
     *
     * Change-logs may be limited to a specific DBMS, esp. SQL-statements. Each unrestricted logs
     * are marked as "generic". You may provide a target-DBMS of your choice. If so, all returned
     * logs must either be intended for the given target-DBMS, or be "generic" entries.
     * If you don't provide a target-DBMS, only generic log-entries will be returned.
     *
     * @param   string  $startVersion   start version
     * @param   string  $dbms           name of the database which is used
     * @return  array
     */
    public function getEntries($startVersion = null, $dbms = \Yana\Db\DriverEnumeration::GENERIC)
    {
        $dbms = strtolower($dbms);
        assert(is_array($this->changes), 'Member "changes" has unexpected type. Array expected.');
        $log = array();

        assert(!isset($entry), 'Cannot redeclare var $entry');
        foreach ($this->changes as $entry)
        {
            /* @var $entry \Yana\Db\Ddl\Logs\Sql */
            // target DBMS does not match
            if ($entry instanceof \Yana\Db\Ddl\Logs\Sql && $entry->getDBMS() !== \Yana\Db\DriverEnumeration::GENERIC && $entry->getDBMS() !== $dbms) {
                continue;
            }

            // no version given
            if (is_null($startVersion)) {
                $log[] = $entry;

            // current version is smaller than or equals start version
            } elseif ($entry->getVersion() !== null && version_compare($entry->getVersion(), $startVersion) < 1) {
                // abort and return log-entries
                return $log;

            // version- and DBMS-informations do match the filter arguments
            } else {
                $log[] = $entry;

            }
        } // end foreach
        unset($entry);

        return $log;
    }

    /**
     * unserialize a XDDL-node to an object
     *
     * Returns the unserialized object.
     *
     * @param   \SimpleXMLElement  $node    XML node
     * @param   mixed              $parent  parent node (if any)
     * @return  \Yana\Db\Ddl\ChangeLog
     */
    public static function unserializeFromXDDL(\SimpleXMLElement $node, $parent = null)
    {
        $ddl = new self();
        $ddl->_unserializeFromXDDL($node);
        return $ddl;
    }

}

?>