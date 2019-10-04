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

namespace Yana\Db\Ddl\Factories;

/**
 * Doctrine to database mapper.
 *
 * Maps Doctrine table info objects to a database object.
 *
 * @package     yana
 * @subpackage  db
 */
class DoctrineMapper extends \Yana\Core\StdObject implements \Yana\Db\Ddl\Factories\IsDoctrineMapper
{

    /**
     * Add a sequence to database.
     *
     * @param   \Yana\Db\Ddl\Database           $database  database to add sequence to
     * @param   \Doctrine\DBAL\Schema\Sequence  $info      sequence information
     * @param   string                          $name      sequence name
     * @return  $this
     */
    public function createSequence(\Yana\Db\Ddl\Database $database, \Doctrine\DBAL\Schema\Sequence $info, $name)
    {
        $sequence = $database->addSequence($name);
        $sequence->setStart($info->getInitialValue());
        // That's all Doctrine supports at the moment. Min/max, cycle, and step are apparently unsupported :-(
        return $this;
    }

    /**
     * Add an index to table.
     *
     * @param   \Yana\Db\Ddl\Table           $table  table to add index to
     * @param   \Doctrine\DBAL\Schema\Index  $info   index information
     * @param   string                       $name   index name
     * @return  $this
     * @throws  \Yana\Core\Exceptions\NotImplementedException   when trying to use a compound primary key
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when no "fields" entry is given in index information
     */
    public function createIndex(\Yana\Db\Ddl\Table $table, \Doctrine\DBAL\Schema\Index $info, $name)
    {
        if (count($info->getColumns()) === 0) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException("Index must contain at least one column", \Yana\Log\TypeEnumeration::WARNING);
        }
        if ($info->isPrimary()) {
            $this->_createPrimaryKey($table, $info); // may throw exception
            return $this;
        }
        assert('!isset($index); // Cannot redeclare var $index');
        $index = $table->addIndex($name);
        $index->setUnique($info->isUnique());

        assert('!isset($flags); // Cannot redeclare var $flags');
        $flags = $info->getFlags();
        if (array_search('fulltext', $flags) !== false) {
            $index->setFulltext(true);
        }

        assert('!isset($fieldName); // Cannot redeclare var $fieldName');
        foreach ($info->getColumns() as $fieldName)
        {
            $index->addColumn($fieldName);
            // Sort order is not supported by Doctrine
        }
        unset($fieldName);

        return $this;
    }

    /**
     * Map Doctrine primary index to primary key.
     *
     * @param   \Yana\Db\Ddl\Table           $table  table to add index to
     * @param   \Doctrine\DBAL\Schema\Index  $info   index information
     * @throws  \Yana\Core\Exceptions\NotImplementedException   when trying to use a compound primary key
     */
    private function _createPrimaryKey(\Yana\Db\Ddl\Table $table, \Doctrine\DBAL\Schema\Index $info)
    {
        $columns = $info->getColumns();
        if (count($columns) !== 1) {
            throw new \Yana\Core\Exceptions\NotImplementedException("Compound primary keys are not supported.");
        }
        $table->setPrimaryKey(current($columns));
    }

    /**
     * Add a foreign key constraint to table.
     *
     * Check constraints seem to be unsupported by Doctrine at this time.
     *
     * @param   \Yana\Db\Ddl\Table                          $table  table to add constraint to
     * @param   \Doctrine\DBAL\Schema\ForeignKeyConstraint  $info   constraint information
     * @param   string                                      $name   constraint name
     * @throws  \Yana\Core\Exceptions\InvalidSyntaxException   when number of source and target columns in constraint is different
     * @throws  \Yana\Core\Exceptions\NotFoundException        when target database/table/column not found
     * @return  $this
     */
    public function createConstraint(\Yana\Db\Ddl\Table $table, \Doctrine\DBAL\Schema\ForeignKeyConstraint $info, $name)
    {
        if ($info->getForeignTableName() == "") {
            $message = "The foreign key must reference a foreign table.";
            throw new \Yana\Core\Exceptions\InvalidSyntaxException($message);
        }
        assert('!isset($targetTable); // Cannot redeclare var $targetTable');
        $targetTable = $info->getForeignTableName();
        assert('!isset($foreign); // Cannot redeclare var $foreign');
        $foreign = $table->addForeignKey($targetTable, $name);

        $strategy = $this->_mapKeyUpdateStrategy((string) $info->onUpdate());
        if ($strategy > "") {
           $foreign->setOnUpdate($strategy);
        }
        if ($info->onDelete() > "") {
            $strategy = $this->_mapKeyUpdateStrategy((string) $info->onDelete());
            if ($strategy > "") {
               $foreign->setOnDelete($strategy);
            }
        }

        assert('!isset($sourceFieldNames); // Cannot redeclare var $sourceFieldNames');
        assert('!isset($targetFieldNames); // Cannot redeclare var $targetFieldNames');
        $sourceFieldNames = $info->getColumns();
        $targetFieldNames = $info->getForeignColumns();

        if (empty($sourceFieldNames) || empty($targetFieldNames)) {
            $message = "Field list in foreign key constraint must not be empty.";
            throw new \Yana\Core\Exceptions\InvalidSyntaxException($message);
        }
        if (count($sourceFieldNames) !== count($targetFieldNames)) {
            $message = "Number of source fields in foreign key constraint " .
                "must match number of target fields.";
            throw new \Yana\Core\Exceptions\InvalidSyntaxException($message);
        }

        assert('!isset($i); // Cannot redeclare var $i');
        assert('!isset($sourceFieldName); // Cannot redeclare var $sourceFieldName');
        assert('!isset($targetFieldName); // Cannot redeclare var $targetFieldName');
        for ($i = 0; $i < count($sourceFieldNames); $i++)
        {
            $sourceFieldName = $sourceFieldNames[$i];
            assert('is_string($sourceFieldName);');
            $targetFieldName = $targetFieldNames[$i];
            assert('is_string($targetFieldName);');
            $foreign->setColumn((string) $sourceFieldName, (string) $targetFieldName); // may throw exception
        }
        unset($i, $sourceFieldNames, $sourceFieldName, $targetFieldNames, $targetFieldName);

        return $this;
    }

    /**
     * Maps MDB2 trigger update strategy to internal constant.
     *
     * @param   string  $mdb2Strategy  as given by MDB2 reverse module
     * @return  int
     */
    private function _mapKeyUpdateStrategy($mdb2Strategy)
    {
        assert('is_string($mdb2Strategy); // Invalid argument type: $mdb2Strategy. String expected.');
        $strategy =  \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::NOACTION;
        switch ($mdb2Strategy)
        {
            case 'CASCADE':
                $strategy = \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::CASCADE;
            break;
            case 'SET NULL':
                $strategy = \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::SETNULL;
            break;
            case 'SET DEFAULT':
                $strategy = \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::SETDEFAULT;
            break;
        }
        return $strategy;
    }

    /**
     * Add a column to table.
     *
     * @param   \Yana\Db\Ddl\Table            $table  table to add column to
     * @param   \Doctrine\DBAL\Schema\Column  $info   column information
     * @param   string                        $name   column name
     * @throws  \Yana\Core\Exceptions\NotImplementedException   when the given 'type' of column is missing or unknwon
     * @return  $this
     */
    public function createColumn(\Yana\Db\Ddl\Table $table, \Doctrine\DBAL\Schema\Column $info, $name)
    {
        assert('!isset($type); // Cannot redeclare var $type');
        $type = $this->_mapColumnType($info->getType()->getName()); // may throw \Yana\Core\Exceptions\NotImplementedException
        /*
         * set type
         */
        switch ($type)
        {
            case 'text':
            case 'string':
                assert('!isset($lowerCaseName); // Cannot redeclare var $lowerCaseName');
                $lowerCaseName = \Yana\Util\Strings::toLowerCase($name);
                if (\Yana\Util\Strings::startsWith($lowerCaseName, 'array') || \Yana\Util\Strings::endsWith($lowerCaseName, 'array')) {
                    $type = "array";

                } elseif (\Yana\Util\Strings::startsWith($lowerCaseName, 'html') || \Yana\Util\Strings::endsWith($lowerCaseName, 'html')) {
                    $type = "html";

                } elseif ($info->getLength() > 256) {
                    $type = "text";
                }
                unset($lowerCaseName);
            break;

            case 'integer':
                if ($info->getLength() == 1) {
                    $type = "bool";
                }
            break;
        } // end switch

        $column = $table->addColumn($name, $type);

        // Set column properties
        if (!is_null($info->getLength()) && $info->getLength() > 0) {

            if ($column->getType() === 'float' && !is_null($info->getPrecision()) && $info->getPrecision() > 0) {
                $column->setLength((int) $info->getLength(), (int) $info->getPrecision());

            } else {
                $column->setLength((int) $info->getLength());
            }
        }

        // These properties are universally applicable
        if ($info->getNotnull() != false) {
            $column->setNullable(false);
        }
        if (!is_null($info->getDefault())) {
            $column->setDefault($info->getDefault());
        }
        // Applicable to both texts and numbers with a given length, has no effect on date values.
        if ($info->getFixed() != false) {
            $column->setFixed(true);
        }
        if (is_string($info->getComment())) {
            $column->setDescription($info->getComment());
        }

        if ($column->isNumber()) {
            // These properties apply to numeric values only
            if ($info->getUnsigned() != false) {
                $column->setUnsigned(true);
            }
            if ($info->getAutoincrement() != false) {
                $column->setAutoIncrement(true);
            }
        }

        return $this;
    }

    /**
     * Maps MDB2 column type to internal column type.
     *
     * @param   string  $mdb2Type  as given by MDB2 reverse module
     * @return  string
     * @throws  \Yana\Core\Exceptions\NotImplementedException  when the type cannot be matched
     */
    private function _mapColumnType($mdb2Type)
    {
        assert('is_string($mdb2Type); // Invalid argument type: $mdb2Type. String expected.');
        switch ($mdb2Type)
        {
            case 'binary':
            case 'blob':
            case 'text':
                $type = "text";
            break;

            case 'guid':
            case 'string':
                $type = "string";
            break;

            case 'boolean':
                $type = "bool";
            break;

            case 'bigint':
            case 'smallint':
            case 'integer':
                $type = "integer";
            break;

            case 'decimal':
            case 'float':
                $type = "float";
            break;

            case 'date':
            case 'date_immutable':
                $type = "date";
            break;

            case 'datetime_immutable':
            case 'datetime':
            case 'datetimetz':
            case 'datetimetz_immutable':
                $type = "time";
            break;

            case 'time_immutable':
            case 'time':
                $type = "string";
            break;

            case 'array':
            case 'object':
                $type = "string";
            break;

            case 'simple_array':
                $type = "list";
            break;

            case 'json':
                $type = "array";
            break;

            /* more ? */
            case 'dateinterval':
            default:
                throw new \Yana\Core\Exceptions\NotImplementedException();
        } // end switch

        return $type;
    }

}

?>