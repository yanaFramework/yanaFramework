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
 * database index column
 *
 * The column list of an index specifies which columns of a table are indexed and how these values
 * are stored.
 *
 * @package     yana
 * @subpackage  db
 */
class IndexColumn extends \Yana\Db\Ddl\AbstractNamedObject
{

    /**
     * tag name for persistance mapping: object <-> XDDL
     *
     * @var string
     * @ignore
     */
    protected $xddlTag = "column";

    /**
     * attributes for persistance mapping: object <-> XDDL
     *
     * @var  array
     * @ignore
     */
    protected $xddlAttributes = array(
        'name'    => array('name',    'nmtoken'),
        'sorting' => array('sorting', 'string'),
        'length'  => array('length',  'int')
    );

    /**
     * @var  string
     * @ignore
     */
    protected $sorting = null;

    /**
     * @var  bool
     * @ignore
     */
    protected $isAscending = true;

    /**
     * @var  int
     * @ignore
     */
    protected $length = null;

    /**
     * Check if column is sorted in ascending order.
     *
     * In an index, each column may be sorted separately for performance reasons.
     * This is especially used for indexes with multiple columns.
     *
     * The default is true.
     *
     * @return  bool
     * @name    \Yana\Db\Ddl\IndexColumn::isAscendingOrder()
     * @see     \Yana\Db\Ddl\IndexColumn::isDescendingOrder()
     */
    public function isAscendingOrder()
    {
        return (bool) $this->isAscending;
    }

    /**
     * Check if column is sorted in descending order.
     *
     * This is the opposite of {@link \Yana\Db\Ddl\Index::isAscendingOrder()}.
     *
     * The default is false.
     *
     * @return  bool
     * @name    \Yana\Db\Ddl\IndexColumn::isDescendingOrder()
     * @see     \Yana\Db\Ddl\IndexColumn::isAscendingOrder()
     */
    public function isDescendingOrder()
    {
        return ! (bool) $this->isAscending;
    }

    /**
     * Set sorting order of a column.
     *
     * In an index, each column may be sorted separately for performance reasons.
     * This is especially used for indexes with multiple columns.
     *
     * The default is true.
     *
     * @param   bool  $isAscending  true: sort ascending, false: sort descending
     * @name    \Yana\Db\Ddl\IndexColumn::isDescendingOrder()
     * @see     \Yana\Db\Ddl\IndexColumn::isAscendingOrder()
     * @return  \Yana\Db\Ddl\Index
     */
    public function setSorting($isAscending = true)
    {
        assert(is_bool($isAscending), 'Wrong type for argument 1. Boolean expected');
        $this->isAscending = (bool) $isAscending;
        return $this;
    }

    /**
     * Get maximum length of index values (MySQL).
     *
     * This is only used for full-text indexes in MySQL.
     *
     * It applies to columns of type blob and text only, this is since both
     * contain full-text which is not constrained in length.
     * Other DBMS either don't support indexes on such column types, or use
     * different implementations that have no need for such an argument.
     *
     * This function returns the maximum number of characters to be indexed or
     * NULL if not set.
     *
     * For MySQL, if this value is not set, it should default to the length
     * attribute of the field definition. Note, that even if the technical type
     * itself is not constrained in length, the logical type defined in the
     * schema may have a length.
     *
     * However, be aware that full-text indexes may grow rapidly. Possibly too
     * large for the DBS to keep it in memory and thus ignoring it.
     * Also they will not be used for non-anchored text-searches with LIKE
     * '%foo%'. Only searches for prefixes will use the index: LIKE 'foo%'.
     * So you are best adviced to check in detail if you really need a full-text
     * index or not.
     *
     * @return  int
     * @name    \Yana\Db\Ddl\IndexColumn::getLength()
     * @see     \Yana\Db\Ddl\IndexColumn::setLength()
     */
    public function getLength()
    {
        if (is_int($this->length)) {
            return $this->length;
        } else {
            return null;
        }
    }

    /**
     * Set maximum length of index values (MySQL).
     *
     * This applies to full-text indexes in MySQL only.
     *
     * @param   int  $length    maximum length of index values
     * @name    \Yana\Db\Ddl\IndexColumn::setLength()
     * @see     \Yana\Db\Ddl\IndexColumn::getLength()
     * @return  \Yana\Db\Ddl\Index
     */
    public function setLength($length)
    {
        assert(is_int($length), 'Wrong type for argument 1. Integer expected');
        if (empty($length)) {
            $this->length = null;
        } else {
            $this->length = $length;
        }
        return $this;
    }

    /**
     * Returns the serialized object as a string in XML-DDL format.
     *
     * @param   \SimpleXMLElement $parentNode  parent node
     * @return  \SimpleXMLElement
     */
    public function serializeToXDDL(\SimpleXMLElement $parentNode = null): \SimpleXMLElement
    {
        if ($this->isAscendingOrder()) {
            $this->sorting = 'ascending';
        } else {
            $this->sorting = 'descending';
        }
        return parent::serializeToXDDL($parentNode);
    }

    /**
     * Unserializes a XDDL-node to an instance of this class and returns it.
     *
     * @param   \SimpleXMLElement  $node    XML node
     * @param   mixed              $parent  parent node (if any)
     * @return  \Yana\Db\Ddl\IndexColumn
     * @throws   \Yana\Core\Exceptions\InvalidArgumentException  when the name attribute is missing
     */
    public static function unserializeFromXDDL(\SimpleXMLElement $node, $parent = null)
    {
        $attributes = $node->attributes();
        if (!isset($attributes['name'])) {
            $message = "Missing name attribute.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, $level);
        }
        $ddl = new self((string) $attributes['name'], $parent);
        $ddl->_unserializeFromXDDL($node);
        $ddl->isAscending = ($ddl->sorting !== 'descending');
        return $ddl;
    }

}

?>