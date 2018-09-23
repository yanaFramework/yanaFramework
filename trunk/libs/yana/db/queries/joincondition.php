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

namespace Yana\Db\Queries;

/**
 * This class represents a join condition in the form of JoinedTable.TargetKey = SourceTable.ForeignKey.
 *
 * @package     yana
 * @subpackage  db
 */
class JoinCondition extends \Yana\Core\Object implements \Yana\Db\Queries\IsJoinCondition
{

    /**
     * @var string
     */
    private $_joinedTableName = "";

    /**
     * @var string
     */
    private $_targetKey = "";

    /**
     * @var string
     */
    private $_sourceTableName = "";

    /**
     * @var string
     */
    private $_foreignKey = "";

    /**
     * @var int
     */
    private $_joinType = \Yana\Db\Queries\JoinTypeEnumeration::INNER_JOIN;

    /**
     * <<constructor>> Initialize values.
     *
     * @param string  $joinedTableName  name of the joined table
     * @param string  $targetKey        name of the column in the joined table
     * @param string  $sourceTableName  name of the source table
     * @param string  $foreignKey       name of the column in the source table
     * @param int     $joinType         a integer constant identifying the type of join used
     */
    public function __construct($joinedTableName, $targetKey, $sourceTableName, $foreignKey, $joinType)
    {
        $this->_joinedTableName = $joinedTableName;
        $this->_targetKey = $targetKey;
        $this->_sourceTableName = $sourceTableName;
        $this->_foreignKey = $foreignKey;
        $this->_joinType = $joinType;
    }

    /**
     * Returns the name of the joined table.
     *
     * @return  string
     */
    public function getJoinedTableName()
    {
        return $this->_joinedTableName;
    }

    /**
     * Returns the name of the column in the joined table.
     *
     * @return  string
     */
    public function getTargetKey()
    {
        return $this->_targetKey;
    }

    /**
     * Returns the name of the source table.
     *
     * @return  string
     */
    public function getSourceTableName()
    {
        return $this->_sourceTableName;
    }

    /**
     * Returns the name of the column in the source table.
     *
     * @return  string
     */
    public function getForeignKey()
    {
        return $this->_foreignKey;
    }

    /**
     * Returns bool(true) if this is an INNER join.
     *
     * @return  bool
     */
    public function isInnerJoin()
    {
        return $this->_joinType === \Yana\Db\Queries\JoinTypeEnumeration::INNER_JOIN;
    }

    /**
     * Returns bool(true) if this is a LEFT join.
     *
     * @return  bool
     */
    public function isLeftJoin()
    {
        return $this->_joinType === \Yana\Db\Queries\JoinTypeEnumeration::LEFT_JOIN;
    }

    /**
     * Returns bool(true) if this is a NATURAL join.
     *
     * @return  bool
     */
    public function isNaturalJoin()
    {
        return $this->_joinType === \Yana\Db\Queries\JoinTypeEnumeration::NATURAL_JOIN;
    }

}

?>