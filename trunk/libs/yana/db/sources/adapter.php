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
 *
 * @ignore
 */
declare(strict_types=1);

namespace Yana\Db\Sources;

/**
 * Data source connection settings data-adapter.
 *
 * This loads and stores connection information to data sources.
 *
 * @package     yana
 * @subpackage  db
 *
 * @ignore
 * @codeCoverageIgnore
 */
class Adapter extends \Yana\Db\Sources\AbstractAdapter
{

    /**
     * Returns the name of the target table.
     *
     * @return  string
     */
    protected function _getTableName()
    {
        return \Yana\Db\Sources\TableEnumeration::TABLE;
    }

    /**
     * Serializes the entity object to a table-row.
     *
     * @param   \Yana\Data\Adapters\IsEntity  $entity  object to convert
     * @return  array
     */
    protected function _serializeEntity(\Yana\Data\Adapters\IsEntity $entity)
    {
        return $this->_getEntityMapper()->toDatabaseRow($entity);
    }

    /**
     * Unserializes the table-row to an entity object.
     *
     * @param   array  $dataSet  table row to convert
     * @return  \Yana\Data\Adapters\IsEntity
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the given data is invalid
     */
    protected function _unserializeEntity(array $dataSet)
    {
        $entity = $this->_getEntityMapper()->toEntity($dataSet);
        $entity->setDataAdapter($this);
        return $entity;
    }

    /**
     * Load a data source by its name.
     *
     * @param   string  $name  unique name of the data set
     * @return  \Yana\Db\Sources\IsEntity
     * @throws  \Yana\Core\Exceptions\NotFoundException  when no unique data source with that name was found
     */
    public function getFromDataSourceName(string $name): \Yana\Db\Sources\IsEntity
    {
        $where = array(
            \Yana\Db\Sources\TableEnumeration::NAME,
            \Yana\Db\Queries\OperatorEnumeration::EQUAL,
            $name
        );

        $select = new \Yana\Db\Queries\Select($this->_getDatabaseConnection());
        $select
                ->setTable($this->_getTableName())
                ->setWhere(
                );
        $rows = $select->getResults();
        if (!is_array($rows) || count($rows) != 1) {
            throw new \Yana\Core\Exceptions\NotFoundException("No such data source.", \Yana\Log\TypeEnumeration::WARNING);
        }
        $entity = $this->_unserializeEntity(current($rows));
        return $entity;
    }

}

?>