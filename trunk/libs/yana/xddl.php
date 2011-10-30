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
 * database structure
 *
 * This wrapper class represents the structure of a database
 *
 * @access      public
 * @package     yana
 * @subpackage  database
 */
class XDDL extends File
{
    /**
     * Data Description Language object
     *
     * @var  DDLDatabase  database defintion
     * @ignore
     */
    protected $ddl = null;

    /**
     * Create a SML string from a scalar variable, an object, or an array of data.
     *
     * If the file is empty or does not exist, the returned string will be empty.
     *
     * If the database definition has been modified, the changed are applied (but not yet saved) to
     * the file content.
     *
     * Note: Reserved column names are reverted to macros. Changes to these columns will be lost.
     *
     * @access  public
     * @return  string
     */
    public function toXML()
    {
        if (isset($this->ddl) && $this->ddl->isModified()) {
            assert('!isset($xddl); // Cannot redeclare var $xddl');
            $xddl = $this->ddl->serializeToXDDL();
            assert('$xddl instanceof \SimpleXMLElement; // Expecting serializeToXDDL() to return a \SimpleXMLElement.');
            assert('!isset($xml); // Cannot redeclare var $xml');
            $xml = $xddl->asXML();
            assert('is_string($xml); // Expecting function toXML() to return a string.');
            unset($xddl);
            // store content
            $this->content = explode("\n", $xml);
            assert('is_array($this->content); // Property "content" is expected to be an array.');
            // reset database definition to "unmodified"
            $this->ddl->setModified(false);
            // return result
            return $xml;

        } else {
            assert('is_array($this->content); // Property "content" is expected to be an array.');
            return implode("\n", $this->content);
        }
    }

    /**
     * get XML content as simple XML Element
     *
     * Returns an instance of \SimpleXMLElement.
     * If the file is empty or does not exist, the returned element will be empty as well.
     *
     * @access  public
     * @return  \SimpleXMLElement
     */
    public function toSimpleXML()
    {
        $xml = $this->toXML();
        assert('is_string($xml);');
        return simplexml_load_string($xml);
    }

    /**
     * get database definition
     *
     * @access  public
     * @return  DDLDatabase
     * @throws  \Yana\Core\Exceptions\NotFoundException       when file does not exist
     * @throws  \Yana\Core\Exceptions\InvalidSyntaxException  when file is invalid
     */
    public function toDatabase()
    {
        if (!isset($this->ddl)) {
            $this->ddl = self::_getDatabaseFromPath($this->getPath());
        }
        return $this->ddl;
    }

    /**
     * get database definition by database name
     *
     * Resolves the database name to a file path and retrieves the file.
     *
     * @access  public
     * @static
     * @param   string  $databaseName  database name
     * @return  DDLDatabase
     * @throws  \Yana\Core\Exceptions\NotFoundException       when file does not exist
     * @throws  \Yana\Core\Exceptions\InvalidSyntaxException  when file is invalid
     */
    public static function getDatabase($databaseName)
    {
        assert('is_string($databaseName); // Wrong type for argument 1. String expected');
        $path = DDL::getPath($databaseName);
        return self::_getDatabaseFromPath($path);
    }

    /**
     * get database definition from file path
     *
     * @param   string  $path  file path
     * @return  DDLDatabase
     * @throws  \Yana\Core\Exceptions\NotFoundException       when file does not exist
     * @throws  \Yana\Core\Exceptions\InvalidSyntaxException  when file is invalid
     */
    private static function _getDatabaseFromPath($path)
    {
        if (!is_file($path)) {
            throw new \Yana\Core\Exceptions\NotFoundException("No such database definition '$path'.");
        }

        $ddl = null;

        assert('!isset($simpleXml); // Cannot redeclare var $simpleXml');
        try {

            $simpleXml = simplexml_load_file($path);
            $ddl = DDLDatabase::unserializeFromXDDL($simpleXml, null, $path);

        } catch (\Exception $e) {
            \Yana\Log\LogManager::getLogger()->addLog("Error in XDDL-file: '$path'.", E_USER_WARNING, $e->getMessage());
            throw new \Yana\Core\Exceptions\InvalidSyntaxException("Error in XDDL-file.", E_USER_WARNING, $e);
        }
        assert('$ddl instanceof DDLDatabase; // Invalid return value. DDLDatabase expected');
        return $ddl;
    }
}

?>