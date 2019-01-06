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
class DoctrineMapper extends \Yana\Core\Object
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
        if ($info->isPrimary()) {
            $this->_createPrimaryKey($table, $info); // may throw exception
            return $this;
        }
        assert('!isset($index); // Cannot redeclare var $index');
        $index = $table->addIndex($name);
        $index->setUnique($info->isUnique());
        foreach ($info->getColumns() as $fieldName)
        {
            $index->addColumn($fieldName);
            // Sort order is not supported by Doctrine
        }
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
     * Add a constraint to table.
     *
     * @param   \Yana\Db\Ddl\Table                          $table  table to add constraint to
     * @param   \Doctrine\DBAL\Schema\ForeignKeyConstraint  $info   constraint information
     * @param   string                                      $name   constraint name
     * @throws  \Yana\Core\Exceptions\NotImplementedException  when trying to use a compound primary key
     * @throws  \Yana\Core\Exceptions\InvalidSyntaxException   when number of source and target columns in constraint is different
     * @throws  \Yana\Core\Exceptions\NotFoundException        when target database/table/column not found
     * @return  $this
     */
    public function createConstraint(\Yana\Db\Ddl\Table $table, \Doctrine\DBAL\Schema\ForeignKeyConstraint $info, $name)
    {
        switch (true)
        {
            // add primary key
            case !empty($info['primary']):
                if (!isset($info['fields']) || count($info['fields']) > 1) {
                    throw new \Yana\Core\Exceptions\NotImplementedException("Compound primary keys are not supported.");
                }
                reset($info['fields']);
                $field = key($info['fields']);
                $table->setPrimaryKey($field);
            break;
            // add unique constraint
            case !empty($info['unique']):
                foreach ($info['fields'] as $field)
                {
                    if (is_string($field) && $table->isColumn($field)) {
                        $table->getColumn($field)->setUnique(true);
                    }
                }
            break;
            // add check constraint
            case !empty($info['check']):
                /* This is not really implemented by MDB2.
                 * It just delivers the name but not the contents!
                 * Except for MS-SQL: where it reports enumerations as check constraints.
                 * Thus we have to ignore it for now.
                 */
            break;
            // add foreign key constraint
            case !empty($info['foreign']):
                // While this case is documented it does not really seem to be implemented just yet.
                if (empty($info['fields']) || empty($info['references']['fields'])) {
                    $message = "Field list in foreign key constraint must not be empty.";
                    throw new \Yana\Core\Exceptions\InvalidSyntaxException($message);
                }
                if (empty($info['fields']) || empty($info['references']['fields']) || count($info['fields']) !== count($info['references']['fields'])) {
                    $message = "Number of source fields in foreign key constraint " .
                        "must match number of target fields.";
                    throw new \Yana\Core\Exceptions\InvalidSyntaxException($message);
                }
                if (empty($info['references']['table'])) {
                    $message = "The foreign key must reference a foreign table.";
                    throw new \Yana\Core\Exceptions\InvalidSyntaxException($message);
                }
                assert('!isset($targetTable); // Cannot redeclare var $targetTable');
                $targetTable = $info['references']['table'];
                assert('!isset($foreign); // Cannot redeclare var $foreign');
                $foreign = $table->addForeignKey($targetTable, $name);
                $this->_mapForeignKey($foreign, $info);
                
            break;
            default:
                throw new \Yana\Core\Exceptions\NotImplementedException();
        } // end switch(true)
        return $this;
    }

    /**
     * Maps MDB2 foreign key to internal foreign key object.
     *
     * @param   \Yana\Db\Ddl\ForeignKey  $foreign             instance to be mapped
     * @param   array                    $mdb2ForeignKeyInfo  as given by MDB2 reverse module
     * @throws  \Yana\Core\Exceptions\NotFoundException  when target database/table/column not found
     */
    private function _mapForeignKey(\Yana\Db\Ddl\ForeignKey $foreign, array $mdb2ForeignKeyInfo)
    {
        if (!empty($mdb2ForeignKeyInfo['onupdate'])) {
            $strategy = $this->_mapKeyUpdateStrategy((string) $mdb2ForeignKeyInfo['onupdate']);
            if ($strategy > "") {
               $foreign->setOnUpdate($strategy);
            }
        }
        if (!empty($mdb2ForeignKeyInfo['ondelete'])) {
            $strategy = $this->_mapKeyUpdateStrategy((string) $mdb2ForeignKeyInfo['ondelete']);
            if ($strategy > "") {
               $foreign->setOnDelete($strategy);
            }
        }
        if (!empty($mdb2ForeignKeyInfo['match'])) {
            switch ($mdb2ForeignKeyInfo['match'])
            {
                case 'SIMPLE':
                    $foreign->setMatch(\Yana\Db\Ddl\KeyMatchStrategyEnumeration::SIMPLE);
                break;
                case 'PARTIAL':
                    $foreign->setMatch(\Yana\Db\Ddl\KeyMatchStrategyEnumeration::PARTIAL);
                break;
                case 'FULL':
                    $foreign->setMatch(\Yana\Db\Ddl\KeyMatchStrategyEnumeration::FULL);
                break;
            }
        }
        // Deferrable means that the checks of the foreign keys can be opportunistic and thus "deferred" to a later time.
        // In layman's terms it means: "Insert values first, ask questions later."
        if (!empty($mdb2ForeignKeyInfo['deferrable'])) {
            $foreign->setDeferrable(true);
        }

        assert('!isset($sourceFieldNames); // Cannot redeclare var $sourceFieldNames');
        assert('!isset($targetFieldNames); // Cannot redeclare var $targetFieldNames');
        $sourceFieldNames = array_keys($mdb2ForeignKeyInfo['fields']);
        $targetFieldNames = array_keys($mdb2ForeignKeyInfo['references']['fields']);

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
    }

    /**
     * Maps MDB2 trigger update strategy to internal constant.
     *
     * @param   string  $mdb2Strategy  as given by MDB2 reverse module
     * @return  string
     */
    private function _mapKeyUpdateStrategy($mdb2Strategy)
    {
        assert('is_string($mdb2Strategy); // Invalid argument type: $mdb2Strategy. String expected.');
        $strategy = "";
        switch ($mdb2Strategy)
        {
            case 'CASCADE':
                $strategy = \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::CASCADE;
            break;
            case 'RESTRICT':
                $strategy = \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::RESTRICT;
            break;
            case 'SET NULL':
                $strategy = \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::SETNULL;
            break;
            case 'SET DEFAULT':
                $strategy = \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::SETDEFAULT;
            break;
            case 'NO ACTION':
                $strategy = \Yana\Db\Ddl\KeyUpdateStrategyEnumeration::NOACTION;
            break;
        }
        return $strategy;
    }

    /**
     * Add a column to table.
     *
     * Info contains these elements:
     * <code>
     *  array(
     *      [notnull] => 1
     *      [nativetype] => int
     *      [length] => 10
     *      [fixed] => 0
     *      [default] => 0
     *      [type] =>
     *      [mdb2type] => integer
     *  );
     * </code>
     *
     * @param   \Yana\Db\Ddl\Table  $table  table to add column to
     * @param   array     $info   column information
     * @param   string    $name   column name
     * @throws  \Yana\Core\Exceptions\NotImplementedException   when the given 'type' of column is missing or unknwon
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when no "type" entry is given in column information
     * @return  $this
     */
    public function createColumn(\Yana\Db\Ddl\Table $table, array $info, $name)
    {
        if (!isset($info['type'])) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException();
        }
        $type = $this->_mapColumnType((string) $info['type']); // may throw \Yana\Core\Exceptions\NotImplementedException
        /*
         * set type
         */
        switch ($type)
        {
            case 'string':
                if (stripos($name, 'html') !== false) {
                    $type = "html";
                } elseif (!empty($info['length']) && $info['length'] > 256) {
                    $type = "text";
                }
            break;

            case 'integer':
                if (isset($info['length']) && $info['length'] == 1) {
                    $type = "bool";
                }
            break;

            case 'timestamp':
                if (isset($info['nativetype']) && $info['nativetype'] === "datetime") {
                    $type = "time";
                } else {
                    $type = "timestamp";
                }
            break;

        } // end switch

        $column = $table->addColumn($name, $type);

        /*
         * set visibility
         * /
        if ($info['primarykey'] && $info['auto']) {
            $this->setVisible(false, $table, $name);
        }

        /*
         * set length
         */
        if (!empty($info['length'])) {
            if (strpos($info['length'], ',') !== false) {
                $info['length'] = explode(',', $info['length']);
                if (count($info['length']) === 2) {
                    // 'length' => array( 0 => precision, 1 => length )
                    $column->setLength((int) $info['length'][1], (int) $info['length'][0]);
                }
            } elseif (is_numeric($info['length'])) {
                $column->setLength((int) $info['length']);
            }
        }

        /*
         * set nullable
         */
        if (!empty($info['notnull'])) {
            $column->setNullable(false);
        } else {
            $column->setNullable(true);
        }

        /*
         * set unsigned
         */
        if (!empty($info['unsigned'])) {
            $column->setUnsigned(true);
        }

        /*
         * set fixed
         */
        if (!empty($info['fixed'])) {
            $column->setFixed(true);
        }

        /*
         * set auto-increment
         */
        if (!empty($info['autoincrement'])) {
            $column->setAutoIncrement(true);
        }

        /*
         * set auto-increment
         */
        if (isset($info['default']) && $info['default'] != "") {
            $column->setDefault($info['default']);
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
            case 'blob':
            case 'clob':
                $type = "text";
            break;

            case 'text':
                $type = "string";
            break;

            case 'bool':
            case 'boolean':
                $type = "bool";
            break;

            case 'int':
            case 'integer':
                $type = "integer";
            break;

            case 'decimal':
            case 'float':
                $type = "float";
            break;

            case 'timestamp':
                $type = "timestamp";
            break;

            case 'date':
                $type = "date";
            break;

            case 'time':
                $type = "string";
            break;

            /* more ? */
            default:
                throw new \Yana\Core\Exceptions\NotImplementedException();
        } // end switch

        return $type;
    }

}

?>