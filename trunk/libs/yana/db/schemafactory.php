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

namespace Yana\Db;

/**
 * <<factory>> Loads schema information and caches the results.
 *
 * @package     yana
 * @subpackage  db
 */
class SchemaFactory extends \Yana\Core\StdObject implements \Yana\Db\IsSchemaFactory, \Yana\Data\Adapters\IsCacheable
{
    use \Yana\Data\Adapters\HasCache;

    /**
     * <<constructor>> Initialize instance.
     *
     * If none is provided, this will use an array adapter for caching by default.
     *
     * @param   \Yana\Data\Adapters\IsDataAdapter  $cache  ooptional cache adapter
     */
    public function __construct(\Yana\Data\Adapters\IsDataAdapter $cache = null)
    {
        if (!is_null($cache)) {
            $this->setCache($cache);
        }
    }

    /**
     * <<factory>> Resolves given name to an object and returns it.
     *
     * Tries to find the schema in cache. If it is not available it will try to load it from disk.
     * Throws an exception if the name could not be properly resolved.
     *
     * @param   string  $schemaName  name of the database schema file (see config/db/*.xml)
     * @return  \Yana\Db\Ddl\Database
     * @throws  \Yana\Core\Exceptions\NotFoundException       when the name could not be resolved
     * @throws  \Yana\Core\Exceptions\InvalidSyntaxException  when the name was resolved, but the target isn't a valid schema
     */
    public function createSchema($schemaName)
    {
        assert(is_string($schemaName), 'Wrong type for argument 1. String expected');
        $schema = null;

        $lowerCaseSchemaName = mb_strtolower($schemaName);
        $cacheId = 'ddl_' . $lowerCaseSchemaName;

        $cache = $this->_getCache();

        if (isset($cache[$cacheId])) {
            $schema = $cache[$cacheId];
        } else {
            $schema = \Yana\Files\XDDL::getDatabase($lowerCaseSchemaName); // may throw exception
            $cache[$cacheId] = $schema;
        }

        assert($schema instanceof \Yana\Db\Ddl\Database);
        return $schema;
    }

}

?>