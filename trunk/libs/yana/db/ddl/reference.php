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

namespace Yana\Db\Ddl;

/**
 * Settings for columns of type "reference".
 *
 * @package     yana
 * @subpackage  db
 */
class Reference extends \Yana\Core\StdObject
{

    /**
     * Target table.
     *
     * @var  string
     */
    private $_table = "";

    /**
     * Key column in target table.
     *
     * @var  string
     */
    private $_column = "";

    /**
     * Label column in target table.
     *
     * @var  string
     */
    private $_label = "";

    /**
     * Initialize instance.
     *
     * @param   string  $table   name of target table
     * @param   string  $column  name of key column in target table
     * @param   string  $label   name of label column in target table
     */
    public function __construct($table, $column, $label)
    {
        $this->setTable($table)
            ->setColumn($column)
            ->setLabel($label);
    }

    /**
     * Get name of target table.
     *
     * @return  string
     */
    public function getTable()
    {
        return $this->_table;
    }

    /**
     * Get name of key column in target table.
     *
     * @return  string
     */
    public function getColumn()
    {
        return $this->_column;
    }

    /**
     * Get name of label column in target table.
     *
     * @return  string
     */
    public function getLabel()
    {
        return $this->_label;
    }

    /**
     * Set table.
     *
     * @param   string  $table  name of target table
     * @return  \Yana\Db\Ddl\Reference
     */
    public function setTable($table)
    {
        assert('is_string($table); // Invalid argument $table: string expected');
        $this->_table = (string) $table;
        return $this;
    }

    /**
     * Set column.
     *
     * @param   string  $column  name of key column in target table
     * @return  \Yana\Db\Ddl\Reference
     */
    public function setColumn($column)
    {
        assert('is_string($column); // Invalid argument $column: string expected');
        $this->_column = (string) $column;
        return $this;
    }

    /**
     * Set label.
     *
     * @param   string  $label  name of label column in target table
     * @return  \Yana\Db\Ddl\Reference
     */
    public function setLabel($label)
    {
        assert('is_string($label); // Invalid argument $label: string expected');
        $this->_label = (string) $label;
        return $this;
    }

}

?>