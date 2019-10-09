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
 * <<decorator>> This class provides XML exports of a database.
 *
 * Why you may want to do that? Because it allows you to use simple, run-off-the-mill
 * XML stylesheets to instant-brew your own reporting engine with virtually no programming.
 *
 * Caveat emptor: I say "virtually" because you still need to write your own stylesheets.
 * But, if all you need is a handful of standard reports, this is the straight forward way to get them.
 *
 * All you need is a query, this class, and a XSL processor of your choice to run your stylesheets
 * and output either CSV, PDF (via XSL:FO), or HTML reports to view in your browser.
 * (Note that HTML that contains table-elements can be opened in Excel et al just like any other spreadsheet.)
 *
 * Plus, the format returned by this class very much resembles the XMLA hierarchical rowset type
 * you might already be familiar with if you are used to working with XMLA interfaces.
 * (And even if you are not: it is really simple to get started, just see the examples below.)
 *
 * @package     yana
 * @subpackage  db
 */
class XmlFactory extends \Yana\Db\Export\AbstractXmlFactory
{

    /**
     * Create XML.
     *
     * This function will export the content of the database to a xml string.
     *
     * You may limit the output to a certain table or structure file, by setting
     * the arguments $structure and $table. Otherwise the whole database is exported,
     * which is also the default behavior. You may set the argument $structure to NULL,
     * if you just need $table.
     *
     * Note that both arguments may also be provided as a list of files or tables.
     *
     * You may set the argument $useForeignKeys to bool(true), if you want references
     * (foreign keys) between tables to be respected. This way tables may be
     * containers for other tables, where there is a relation between both.
     *
     * To "resolve a foreign key relation" in this case actually means, each foreign
     * key is interpreted as a parent-child relation between two tables.
     * Each row of the child table (the referencing table) is then
     * copied to it's parent row (the row in the referenced table,
     * as identified by the value of the foreign key column in the
     * current row of the child table).
     *
     * Note, that a table may have multiple parents.
     * This will result in multiple copies of the same row.
     *
     * Also note, that this function does not detect circular
     * references. However, this is not much of a restriction, as
     * such references are not a legal construct in RDBMSs.
     * Although some RDBMS allow such constructs, it would be
     * "practically" impossible to add any data, without breaking
     * referential integrity, because a row should not contain a
     * checked reference to itself, while it does not exist.
     *
     * Note: this may result in an error for DBMS that ignore
     * referential integrity, like MyISAM tables in MySQL.
     *
     * Here is an example to illustrate the behavior of this function.
     * May "foo" and "bar" be tables, with "foo" having a property "bar_id",
     * that is a foreign key, referencing "bar".
     *
     * The following function call will output the XML representation of both tables:
     * <code>
     * print \Yana\Db\Export\DataExporter::createXML(true, null, array('foo', 'bar'));
     * </code>
     *
     * The result would look something like this:
     * <code>
     * ... XML-head ...
     * <bar>
     *   <row id="1">
     *     <bar_id>1</bar_id>
     *     <bar_value>0.0</bar_value>
     *     <!-- here come some entries of table foo -->
     *     <bar.foo>
     *       <row id="2">
     *         <foo_id>2</foo_id>
     *         <bar_id>1</bar_id>
     *         <!-- other values -->
     *       </row>
     *       <row id="5">
     *         <foo_id>5</foo_id>
     *         <bar_id>1</bar_id>
     *         <!-- other values -->
     *       </row>
     *     </bar.foo>
     *   </row>
     * </bar>
     * </code>
     *
     * @param   \Yana\Db\IsConnectionFactory  $factory  contains database connection credentials needed to make a connection
     * @return  string
     */
    public function createXML(\Yana\Db\IsConnectionFactory $factory)
    {
        $data = array(); // declare output variable of type array
        @set_time_limit(500); // This may take a while. Raise limit to avoid time-out.

        /*
         * loop through files
         */
        assert(!isset($databaseName), 'Cannot redeclare var $databaseName');
        /* @var $databaseName string */
        foreach ($this->getDatabaseNames() as $databaseName)
        {
            $db = $factory->createConnection($databaseName);
            $dbSchema = $db->getSchema();
            $nodes = array();
            $tables = $dbSchema->getTableNames();

            /**
             * loop through tables
             */
            foreach ($tables as $table)
            {
                $data[$table] = $db->select($table);
                /* Note: $nodes is a "flat" list of references */
                $nodes[$table] =& $data[$table];
            }
            unset($table);

            /**
             * resolve foreign keys on demand
             */
            if ($this->isUsingForeignKeys()) {
                $this->resolveForeignKeys($dbSchema, $nodes, $data);
            } // end if ($useForeignKeys)

        } // end foreach (structure)
        unset($databaseName);

        /*
         * encode data array to xml string
         */
        return \Yana\Db\Export\XmlFactoryExporter::convertArrayToXml($data);
    }

    /**
     * Resolve foreign keys on demand.
     *
     * @param  \Yana\Db\Ddl\Database  $dbSchema  database schema
     * @param  array                  $nodes     
     * @param  array                  $data      
     */
    private function resolveForeignKeys($dbSchema, array &$nodes, array &$data)
    {
        /**
         * loop through tables
         */
        assert(!isset($tableName), 'Cannot redeclare var $tableName');
        assert(!isset($table), 'Cannot redeclare var $table');
        assert(!isset($hasFKey), 'Cannot redeclare var $hasFKey');
        /* declare temporary variables */
        assert(!isset($_attr), 'Cannot redeclare var $_attr');
        assert(!isset($_fKey), 'Cannot redeclare var $_fKey');

        foreach (array_keys($nodes) as $tableName)
        {
            $_attr = "@$tableName";
            $table = $dbSchema->getTable($tableName);
            $_fKey = null;
            $hasFKey = false;

            /**
             * loop through foreign keys
             */
            assert(!isset($fCol), 'Cannot redeclare var $fCol');
            assert(!isset($fTableName), 'Cannot redeclare var $fTable');
            assert(!isset($column), 'Cannot redeclare var $column');
            foreach ($table->getForeignKeys() as $column)
            {
                /* @var $column \Yana\Db\Ddl\ForeignKey */
                assert(!isset($_fKeys), 'Cannot redeclare var $_fKeys');
                $_fKeys = $column->getColumns();
                $fTableName = $column->getTargetTable();
                if (count($_fKeys) > 1) {
                    // @codeCoverageIgnoreStart
                    continue; // ignore compound foreign keys
                    // @codeCoverageIgnoreEnd
                }
                $hasFKey = true;
                $fCol = mb_strtoupper((string) current($_fKeys)); // value = target column of fkey constraint
                if (empty($fCol)) {
                    $fCol = mb_strtoupper((string) key($_fKeys)); // fall back to key = source column
                }
                unset($_fKeys);
                /**
                 * {@internal
                 *
                 * Keep in mind that foreign key references
                 * are reversed compared to arrays:
                 *   $nodes[$fTableName] is reference to the target table,
                 *   - NOT the source table!
                 *
                 * }}
                 *
                 * loop through rows in foreign table
                 */
                assert(!isset($pKey), 'Cannot redeclare var $pKey');
                foreach (array_keys($nodes[$tableName]) as $pKey)
                {
                    if (isset($nodes[$tableName][$pKey][$fCol])) {
                        $_fKey = $nodes[$tableName][$pKey][$fCol];
                        /* skip value if referenced row does not exist */
                        if (isset($nodes[$fTableName][$_fKey])) {
                            if (!isset($nodes[$fTableName][$_fKey][$_attr])) {
                                $nodes[$fTableName][$_fKey][$_attr] = array();
                            }
                            $nodes[$fTableName][$_fKey][$_attr][$pKey] = $nodes[$tableName][$pKey];
                        }
                    }
                }
                unset($pKey);
            } // end foreach (foreign key)
            unset($fCol, $fTableName, $column);
            if ($hasFKey && isset($data[$tableName])) {
                //unset($data[$tableName]);
            }
        }
        // clean up temporary variables
        unset($_attr, $_fKey, $table, $tableName, $hasFKey);
    }

}

?>