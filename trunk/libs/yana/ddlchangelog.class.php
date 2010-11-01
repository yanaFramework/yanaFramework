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
 * database structure: change-log definition
 *
 * The change-log element of a database definition is meant to store documentation about updates.
 * Each update is stored as a sorted list, with the most recent update first.
 *
 * The updates contain the type of the change, a version number and what has changed.
 * A description may be added to describe the changes as well.
 *
 * @access      public
 * @package     yana
 * @subpackage  database
 */
class DDLChangeLog extends DDL
{
    /**#@+
     * @ignore
     * @access  protected
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
        'create' => array('changes', 'array', 'DDLLogCreate'),
        'rename' => array('changes', 'array', 'DDLLogRename'),
        'drop'   => array('changes', 'array', 'DDLLogDrop'),
        'update' => array('changes', 'array', 'DDLLogUpdate'),
        'sql'    => array('changes', 'array', 'DDLLogSql'),
        'change' => array('changes', 'array', 'DDLLogChange')
    );

    /** @var DDLLog[]    */ protected $changes = array();
    /** @var DLLDatabase */ protected $parent = null;

    /**#@-*/

    /**
     * constructor
     *
     * @param  DDLDatabase  $parent  parent database
     */
    public function __construct(DDLDatabase $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * get parent
     *
     * Returns the declaration of the Datebase the object is defined in.
     *
     * @return  DDLDatabase
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * add a new change-log entry
     *
     * This function prepends a new entry at the beginning of the change-log.
     * The change-log is sorting by version-number in descending order.
     *
     * @access  public
     * @param   DDLLog  $log  expected an DDLLog object
     */
    public function addEntry(DDLLog $log)
    {
        array_unshift($this->changes, $log);
    }

    /**
     * flush the change-log
     *
     * This function completly deletes any chang-log entries, if there are any.
     *
     * @access  public
     */
    public function dropEntries()
    {
        $this->changes = array();
    }

    /**
     * list of changes
     *
     * Returns a list of change-log entries as a numeric array, each of which are instances of
     * DDLLog.
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
     * @access  public
     * @param   string  $startVersion   start version
     * @param   string  $dbms           name of the database which is used
     * @return  array
     */
    public function getEntries($startVersion = null, $dbms = 'generic')
    {
        $dbms = strtolower($dbms);
        assert('is_array($this->changes); // Member "changes" has unexpected type. Array expected.');
        $log = array();
        foreach ($this->changes as $entry)
        {
            // target DBMS does not match
            if ($entry instanceof DDLLogSql && $entry->getDBMS() !== 'generic' && $entry->getDBMS() !== $dbms) {
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
        return $log;
    }

    /**
     * unserialize a XDDL-node to an object
     *
     * Returns the unserialized object.
     *
     * @access  public
     * @static
     * @param   SimpleXMLElement  $node    XML node
     * @param   mixed             $parent  parent node (if any)
     * @return  DDLChangeLog
     */
    public static function unserializeFromXDDL(SimpleXMLElement $node, $parent = null)
    {
        $ddl = new self();
        $ddl->_unserializeFromXDDL($node);
        return $ddl;
    }
}

?>