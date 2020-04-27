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
    public function __construct(string $table, string $column, string $label)
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
    public function getTable(): string
    {
        return $this->_table;
    }

    /**
     * Get name of key column in target table.
     *
     * @return  string
     */
    public function getColumn(): string
    {
        return $this->_column;
    }

    /**
     * Get name of label column in target table.
     *
     * @return  string
     */
    public function getLabel(): string
    {
        return $this->_label;
    }

    /**
     * Set table name.
     *
     * @param   string  $table  name of target table
     * @return  $this
     */
    public function setTable(string $table)
    {
        $this->_table = $table;
        return $this;
    }

    /**
     * Set column name.
     *
     * @param   string  $column  name of key column in target table
     * @return  $this
     */
    public function setColumn(string $column)
    {
        $this->_column = $column;
        return $this;
    }

    /**
     * Set label.
     *
     * @param   string  $label  name of label column in target table
     * @return  $this
     */
    public function setLabel(string $label)
    {
        $this->_label = $label;
        return $this;
    }

}

?>