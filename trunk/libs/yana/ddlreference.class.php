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
 * Settings for columns of type "reference".
 *
 * This reprents cummulated information of 
 *
 * @access      public
 * @package     yana
 * @subpackage  database
 */
class DDLReference extends Object
{

    /**
     * Target table.
     *
     * @access  private
     * @var     string
     */
    private $_table = "";

    /**
     * Key column in target table.
     *
     * @access  private
     * @var     string
     */
    private $_column = "";

    /**
     * Label column in target table.
     *
     * @access  private
     * @var     string
     */
    private $_label = "";

    /**
     * Initialize instance
     *
     * @access  public
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
     * @access  public
     * @return  string
     */
    public function getTable()
    {
        return $this->_table;
    }

    /**
     * Get name of key column in target table.
     *
     * @access  public
     * @return  string
     */
    public function getColumn()
    {
        return $this->_column;
    }

    /**
     * Get name of label column in target table.
     *
     * @access  public
     * @return  string
     */
    public function getLabel()
    {
        return $this->_label;
    }

    /**
     * Set table.
     *
     * @access  public
     * @param   string  $table  name of target table
     * @return  DDLReference
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
     * @access  public
     * @param   string  $column  name of key column in target table
     * @return  DDLReference
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
     * @access  public
     * @param   string  $label  name of label column in target table
     * @return  DDLReference
     */
    public function setLabel($label)
    {
        assert('is_string($label); // Invalid argument $label: string expected');
        $this->_label = (string) $label;
        return $this;
    }

}

?>