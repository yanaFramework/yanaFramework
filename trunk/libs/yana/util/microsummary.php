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

namespace Yana\Util;

/**
 * Microsummary.
 *
 * This class provides methods to read and write persistent microsummaries.
 *
 * "Microsummaries" are a Firefox 2.0 feature that allows users to create dynamic bookmark
 * titles that automatically update when the content of the bookmarked page changes.
 *
 * Have a look at what microsummaries can be:
 * <ul>
 *   <li> the numbers of downloads of a file on a download site </li>
 *   <li> the latest news on a news page </li>
 *   <li> current number of unread e-mail in the inbox of a webmail service </li>
 *   <li> current total of donations to a project </li>
 *   <li> the date of latest updates on a database </li>
 *   <li> the latest submission to a guestbook or forum </li>
 *   <li> the number of visitors currently online in a chat room </li>
 *   <li> the latest stock values aso. </li>
 * </ul>
 *
 * Examples of usage:
 * <ol>
 *  <li> Creating a new instance from a plugin:
 *       <code>$microsummary = new Microsummary($this->_getConnection('microsummary'));</code>
 *  </li>
 *  <li> Setting a microsummary from a plugin:
 *       <code>$microsummary->setText(__CLASS__, 'Summary text');</code>
 *  </li>
 *  <li> Retrieving a microsummary in a plugin:
 *       <code>$text = $microsummary->getText(__CLASS__);</code>
 *  </li>
 *  <li> To indicate that a microsummary exists for your plugin
 *       add this to your output function
 *       <code>$microsummary->publishSummary(__CLASS__);</code>
 *  </li>
 *  <li> Calling a microsummary from a browser:
 *       <code>index.php?action=get_microsummary&target=guestbook</code>
 *       (where 'guestbook' is the name of the plugin)
 *  </li>
 * </ol>
 *
 * @package     yana
 * @subpackage  utilities
 */
class Microsummary extends \Yana\Core\Object implements \Yana\Util\IsMicrosummary
{

    /**
     * @var  \Yana\Db\IsConnection
     */
    private $_connection = null;

    /**
     * list of published microsummary
     *
     * @var  array
     */
    private static $_summaries = array();

    /**
     * <<constructor>> Inject database connection.
     *
     * @param  \Yana\Db\IsConnection  $connection  to "microsummary" database
     */
    public function __construct(\Yana\Db\IsConnection $connection)
    {
        $this->_connection = $connection;
    }

    /**
     * Returns the database connection
     *
     * @return  \Yana\Db\IsConnection
     */
    protected function _getConnection()
    {
        return $this->_connection;
    }

    /**
     * Get the text of a microsummary.
     *
     * Reads the microsummary string identified by $id and returns it.
     * This function returns bool(false) if no corresponding summary exists.
     *
     * Example of usage:
     *
     * Retrieving a microsummary in a plugin:
     * <code>$text = $microsummary->getText(__CLASS__);</code>
     *
     * @param   string  $id  identifies the summary to get
     * @return  string
     */
    public function getText($id)
    {
        assert('is_string($id); // Wrong type for argument 1. String expected');
        assert('mb_strlen($id) > 32; // Argument 1 must have at most 32 charaters');

        $id = mb_strtoupper("$id");
        $result = $this->_getConnection()->select("microsummary.$id.microsummary_text");
        return (string) $result;
    }

    /**
     * Set the text of a microsummary.
     *
     * Saves the microsummary string identified by $id for later use.
     * This function returns bool(false) if the input is invalid or
     * the entry could not be saved.
     *
     * Example of usage:
     *
     * Retrieving a microsummary in a plugin:
     * <code>$text = $microsummary->getText(__CLASS__);</code>
     *
     * @param   string  $id    identifies the summary to get
     * @param   string  $text  the text of the microsummary
     * @return  bool
     */
    public function setText($id, $text)
    {
        assert('is_string($id); // Wrong type for argument 1. String expected');
        assert('is_string($text); // Wrong type for argument 2. String expected');

        $id = mb_strtoupper("$id");
        $value = array('microsummary_id' => $id, 'microsummary_text' => $text);
        try {
            $this->_getConnection()
                ->insertOrUpdate("microsummary.$id", $value)
                ->commit(); // may throw exception
            return true;
        } catch (\Exception $e) {
            unset($e);
            return false;
        }
    }

    /**
     * Publish a microsummary.
     *
     * Adds the microsummary identified by $id to the list of
     * microsummaries to be printed to the browser.
     *
     * Example of usage:
     *
     * Add this to your output function
     * <code>$microsummary->publishSummary($this->name);</code>
     *
     * Returns bool(false) on error.
     *
     * @param   string  $id  identifies the summary to get
     * @return  bool
     */
    public function publishSummary($id)
    {
        assert('is_string($id); // Wrong argument type argument 1. String expected');

        $id = mb_strtoupper("$id");

        if (!$this->_getConnection()->exists("microsummary.$id")) {
            // microsummary not found
            return false;
        }
        self::$_summaries[] = $id;
        array_unique(self::$_summaries);
        return true;
    }

    /**
     * Returns a list of all previously published microsummaries.
     *
     * @return  array
     */
    public function getSummaries()
    {
        assert('is_array(self::$_summaries); // Member "microsummaries" should be an array.');
        return self::$_summaries;
    }

}

?>