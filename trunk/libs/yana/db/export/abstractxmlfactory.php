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

namespace Yana\Db\Export;

/**
 * <<abstract>> Database extractor.
 *
 * @package     yana
 * @subpackage  db
 */
abstract class AbstractXmlFactory extends \Yana\Core\StdObject
{

    /**
     * @var bool
     */
    private $_usingForeignKeys = false;

    /**
     * @var array
     */
    private $_databaseNames = array();

    /**
     * Check whether to use foreign keys.
     *
     * Returns bool(false) to export "flat" structure or bool(true) to use foreign keys to create recursive containers.
     *
     * @return  bool
     */
    public function isUsingForeignKeys()
    {
        return $this->_usingForeignKeys;
    }

    /**
     * Check whether to limit output to certain schema file(s).
     *
     * @return  array
     */
    public function getDatabaseNames()
    {
        return $this->_databaseNames;
    }

    /**
     * Trigger whether to use foreign keys.
     *
     * Toggles wether to export "flat" structure or use foreign keys to create recursive containers.
     *
     * @param   bool  $useForeignKeys  turn containers on/off
     * @return  $this
     */
    public function setUsingForeignKeys($useForeignKeys)
    {
        assert(is_bool($useForeignKeys), 'Invalid argument type: $useForeignKeys. Bool expected');
        $this->_usingForeignKeys = (bool) $useForeignKeys;
        return $this;
    }

    /**
     * Include this schema file in output.
     *
     * @param   string  $filterDatabaseName  schema file
     * @return  $this
     */
    public function addDatabaseName($filterDatabaseName)
    {
        assert(is_string($filterDatabaseName), 'Invalid argument type: $filterDatabaseName. String expected');
        $this->_databaseNames[] = (string) $filterDatabaseName;
        return $this;
    }

    /**
     * Limit output to these schema files.
     *
     * @param   array  $filterDatabaseNames  schema files
     * @return  $this
     */
    public function setDatabaseNames(array $filterDatabaseNames)
    {
        $this->_databaseNames = array();
        foreach ($filterDatabaseNames as $filterDatabaseName)
        {
            $this->addDatabaseName($filterDatabaseName);
        }
        return $this;
    }

}

?>