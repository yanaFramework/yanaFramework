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

namespace Yana\Files;

/**
 * Database structure.
 *
 * This wrapper class represents the structure of a database
 *
 * @package     yana
 * @subpackage  files
 */
class XDDL extends \Yana\Files\File
{

    /**
     * Data Description Language object
     *
     * @var  \Yana\Db\Ddl\Database  database defintion
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
     * @return  string
     */
    public function toXML(): string
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
     * Get XML content as simple XML Element.
     *
     * Note: If the file is empty or does not exist, the returned element will be empty as well.
     *
     * @return  \SimpleXMLElement
     */
    public function toSimpleXML(): \SimpleXMLElement
    {
        $xml = $this->toXML();
        assert('is_string($xml);');
        return simplexml_load_string($xml);
    }

    /**
     * Get database definition.
     *
     * @return  \Yana\Db\Ddl\Database
     * @throws  \Yana\Core\Exceptions\NotFoundException       when file does not exist
     * @throws  \Yana\Core\Exceptions\InvalidSyntaxException  when file is invalid
     */
    public function toDatabase(): \Yana\Db\Ddl\Database
    {
        if (!isset($this->ddl)) {
            $this->ddl = self::_getDatabaseFromPath($this->getPath());
        }
        return $this->ddl;
    }

    /**
     * Get database definition by database name.
     *
     * Resolves the database name to a file path and retrieves the file.
     *
     * @param   string  $databaseName  database name
     * @return  \Yana\Db\Ddl\Database
     * @throws  \Yana\Core\Exceptions\NotFoundException       when file does not exist
     * @throws  \Yana\Core\Exceptions\InvalidSyntaxException  when file is invalid
     */
    public static function getDatabase(string $databaseName): \Yana\Db\Ddl\Database
    {
        $path = \Yana\Db\Ddl\DDL::getPath($databaseName);
        return self::_getDatabaseFromPath($path);
    }

    /**
     * Get database definition from file path.
     *
     * @param   string  $path  file path
     * @return  \Yana\Db\Ddl\Database
     * @throws  \Yana\Core\Exceptions\NotFoundException       when file does not exist
     * @throws  \Yana\Core\Exceptions\InvalidSyntaxException  when file is invalid
     */
    private static function _getDatabaseFromPath(string $path): \Yana\Db\Ddl\Database
    {
        if (!is_file($path)) {
            throw new \Yana\Core\Exceptions\NotFoundException("No such database definition '$path'.");
        }

        $ddl = null;

        assert('!isset($simpleXml); // Cannot redeclare var $simpleXml');
        try {

            $simpleXml = simplexml_load_file($path);
            $ddl = \Yana\Db\Ddl\Database::unserializeFromXDDL($simpleXml, null, $path);

        } catch (\Exception $e) {
            throw new \Yana\Core\Exceptions\InvalidSyntaxException("Error in XDDL-file: " . $path, \Yana\Log\TypeEnumeration::WARNING, $e);
        }
        assert('$ddl instanceof \Yana\Db\Ddl\Database; // Invalid return value. \Yana\Db\Ddl\Database expected');
        return $ddl;
    }

}

?>