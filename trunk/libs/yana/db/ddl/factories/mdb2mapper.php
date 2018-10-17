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
 * MDB2 to database mapper.
 *
 * Maps MDB2 table info arrays to a database object.
 *
 * @package     yana
 * @subpackage  db
 */
class Mdb2Mapper extends \Yana\Core\Object implements \Yana\Db\Ddl\Factories\IsMdb2Mapper
{

    /**
     * Add a sequence to database.
     *
     * Info contains these elements:
     * <code>
     * array(
     *   [start] => int
     * );
     * </code>
     *
     * @param   \Yana\Db\Ddl\Database  $database  database to add sequence to
     * @param   array        $info      sequence information
     * @param   string       $name      sequence name
     * @return  $this
     */
    public function createSequence(\Yana\Db\Ddl\Database $database, array $info, $name)
    {
        $sequence = $database->addSequence($name);
        if (isset($info['start'])) {
            $sequence->setStart($info['start']);
        }
        // These seem to be currently not supported by MDB2:
        // @codeCoverageIgnoreStart
        if (isset($info['min'])) {
            $sequence->setMin((int) $info['min']);
        }
        if (isset($info['max'])) {
            $sequence->setMax((int) $info['max']);
        }
        if (isset($info['step'])) {
            $sequence->setIncrement((int) $info['step']);
        }
        if (!empty($info['cycle'])) {
            $sequence->setCycle(true);
        }
        // @codeCoverageIgnoreEnd
        return $this;
    }

    /**
     * Add a index to table.
     *
     * Info contains these elements:
     * <code>
     * array(
     *   [fields] => array(
     *     [fieldname] => array( [sorting] => ascending )
     *     // more fields
     *   )
     * );
     * </code>
     *
     * @param   \Yana\Db\Ddl\Table  $table  table to add index to
     * @param   array     $info   index information
     * @param   string    $name   index name
     * @return  $this
     */
    public function createIndex(\Yana\Db\Ddl\Table $table, array $info, $name)
    {
        $index = $table->addIndex($name);
        foreach ($info['fields'] as $fieldName => $sorting)
        {
            if (!empty($sorting['sorting'])) {
                $isAscending = $sorting['sorting'] === 'ascending';
            } else {
                $isAscending = true;
            }
            $index->addColumn($fieldName, $isAscending);
        }
        return $this;
    }

    /**
     * Add a constraint to table.
     *
     * Info contains these elements:
     * <code>
     *  array(
     *      [primary] => 0
     *      [unique]  => 0
     *      [foreign] => 1
     *      [check]   => 0
     *      [fields] => array(
     *          [field1name] => array() // one entry per each field covered
     *          [field2name] => array() // by the index
     *          [field3name] => array(
     *              [sorting]  => ascending
     *              [position] => 3
     *          )
     *      )
     *      [references] => array(
     *          [table] => name
     *          [fields] => array(
     *              [fieldname] => array( [position] => 1 )
     *              // more fields
     *          )
     *      )
     *      [deferrable] => 0
     *      [initiallydeferred] => 0
     *      [onupdate] => CASCADE|RESTRICT|SET NULL|SET DEFAULT|NO ACTION
     *      [ondelete] => CASCADE|RESTRICT|SET NULL|SET DEFAULT|NO ACTION
     *      [match] => SIMPLE|PARTIAL|FULL
     *  );
     * </code>
     *
     * @param   \Yana\Db\Ddl\Table  $table  table to add constraint to
     * @param   array     $info   constraint information
     * @param   string    $name   constraint name
     * @throws  \Yana\Core\Exceptions\NotImplementedException  when trying to use a compound primary key
     * @throws  \Yana\Core\Exceptions\InvalidSyntaxException   when number of source and target columns in constraint is different
     * @return  $this
     */
    public function createConstraint(\Yana\Db\Ddl\Table $table, array $info, $name)
    {
        switch (true)
        {
            // add primary key
            case !empty($info['primary']):
                if (count($info['fields']) > 1) {
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
                    $column = $table->getColumn($field);
                    $column->setUnique(true);
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
                /* While this case is documented it does not really seem to be implemented just yet.
                 */
                if (count($info['fields']) !== count($info['references']['fields'])) {
                    $message = "Number of source fields in foreign key constraint " .
                        "must match number of target fields.";
                    throw new \Yana\Core\Exceptions\InvalidSyntaxException($message);
                }
                $targetTable = $info['references']['table'];
                $foreign = $table->addForeignKey($targetTable, $name);
                if (!empty($info['deferrable'])) {
                    $foreign->setDeferrable(true);
                }
                if (!empty($info['onupdate'])) {
                    switch ($info['onupdate'])
                    {
                        case 'CASCADE':
                            $foreign->setOnUpdate(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::CASCADE);
                        break;
                        case 'RESTRICT':
                            $foreign->setOnUpdate(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::RESTRICT);
                        break;
                        case 'SET NULL':
                            $foreign->setOnUpdate(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::SETNULL);
                        break;
                        case 'SET DEFAULT':
                            $foreign->setOnUpdate(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::SETDEFAULT);
                        break;
                        case 'NO ACTION':
                            $foreign->setOnUpdate(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::NOACTION);
                        break;
                    }
                }
                if (!empty($info['ondelete'])) {
                    switch ($info['ondelete'])
                    {
                        case 'CASCADE':
                            $foreign->setOnDelete(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::CASCADE);
                        break;
                        case 'RESTRICT':
                            $foreign->setOnDelete(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::RESTRICT);
                        break;
                        case 'SET NULL':
                            $foreign->setOnDelete(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::SETNULL);
                        break;
                        case 'SET DEFAULT':
                            $foreign->setOnDelete(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::SETDEFAULT);
                        break;
                        case 'NO ACTION':
                            $foreign->setOnDelete(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::NOACTION);
                        break;
                    }
                }
                if (!empty($info['match'])) {
                    switch ($info['match'])
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
                foreach (array_keys($info['fields']) as $i => $source)
                {
                    assert('is_string($source);');
                    $target = $info['references']['fields'][$i]['fieldname'];
                    assert('is_string($target);');
                    $foreign->setColumn($source, $target);
                }
            break;
            default:
                throw new \Yana\Core\Exceptions\NotImplementedException();
            break;
        } // end switch(true)
        return $this;
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
     * @throws  \Yana\Core\Exceptions\NotImplementedException  when the given 'type' of column is unknwon
     * @return  $this
     */
    public function createColumn(\Yana\Db\Ddl\Table $table, array $info, $name)
    {
        /*
         * set type
         */
        switch ($info['type'])
        {
            case 'blob':
            case 'clob':
                $type = "text";
            break;

            case 'text':
                if (stripos($name, 'html') !== false) {
                    $type = "html";
                } elseif (!empty($info['length']) && $info['length'] > 256) {
                    $type = "text";
                } else {
                    $type = "string";
                }
            break;

            case 'boolean':
                $type = "bool";
            break;

            case 'integer':
                if ($info['length'] == 1) {
                    $type = "bool";
                } else {
                    $type = "integer";
                }
            break;

            case 'decimal':
            case 'float':
                $type = "float";
            break;

            case 'timestamp':
                if ($info['nativetype'] === "datetime") {
                    $type = "time";
                } else {
                    $type = "timestamp";
                }
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
}

?>