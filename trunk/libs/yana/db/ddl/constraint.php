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
 * Database constraint definition.
 *
 * A constraint is a boolean expression that must evaluate to true at all times for the row to
 * be valid. The database should ensure that. For databases that don't have that feature, you
 * may use the vendor-independent type "generic" to simluate it.
 *
 * @package     yana
 * @subpackage  db
 */
class Constraint extends \Yana\Db\Ddl\AbstractUnnamedObject
{
    /**#@+
     * @ignore
     */

    /**
     * tag name for persistance mapping: object <-> XDDL
     *
     * @var  string
     */
    protected $xddlTag = "constraint";

    /**
     * attributes for persistance mapping: object <-> XDDL
     *
     * @var  array
     */
    protected $xddlAttributes = array(
        'name'    => array('name',       'nmtoken'),
        'dbms'    => array('dbms',       'string'),
        '#pcdata' => array('constraint', 'string')
    );

    /** @var string */ protected $dbms = "generic";
    /** @var string */ protected $constraint = null;

    /**#@-*/

    /**
     * Get target DBMS.
     *
     * Returns the name of the target DBMS for this definition as a lower-cased string.
     * The default is "generic".
     *
     * @return  string
     */
    public function getDBMS()
    {
        return $this->dbms;
    }

    /**
     * Set target DBMS.
     *
     * While you may settle for any target DBMS you want and provide it in any kind of writing you
     * choose, you should remind, that not every DBMS is supported by the database API provided
     * here.
     *
     * The special "generic" DBMS-value means that the constraint is suitable for any DBMS.
     * Usually this is used as a fall-back option for DBMS you haven't thought of when creating the
     * database structure or for those that simply doesn't have the feature in question.
     *
     * Generic values are usually simulated using PHP-code.
     *
     * @param   string  $dbms  target DBMS, defaults to "generic"
     * @return  \Yana\Db\Ddl\Constraint
     */
    public function setDBMS($dbms = "generic")
    {
        assert('is_string($dbms); // Wrong type for argument 1. String expected');
        $dbms = strtolower($dbms);
        assert('empty($dbms) || in_array($dbms, \Yana\Db\Ddl\Database::getSupportedDBMS()); // Unsupported DBMS');
        if (empty($dbms)) {
            $this->dbms = null;
        } else {
            $this->dbms = "$dbms";
        }
        return $this;
    }

    /**
     * Get constraint.
     *
     * Returns the code of the constraint or NULL if it has not been set.
     * The syntax depends on the target DBMS. For type "generic" the feature is emulated using PHP
     * code.
     *
     * @return  string
     */
    public function getConstraint()
    {
        if (is_string($this->constraint)) {
            return $this->constraint;
        } else {
            return null;
        }
    }

    /**
     * Set constraint.
     *
     * Note: This function can't ensure that your codes makes sense.
     * So keep in mind that it is your job in the first place to ensure the constraint is valid!
     * The syntax depends on the target DBMS. For type "generic" the feature is emulated using PHP
     * code.
     *
     * BE WARNED: As always - do NOT use this function with any unchecked user input.
     *
     * @param   string  $constraint  evaluation rule
     * @return  \Yana\Db\Ddl\Constraint
     */
    public function setConstraint($constraint = "")
    {
        assert('is_string($constraint); // Wrong type for argument 1. String expected');
        if (empty($constraint)) {
            $this->constraint = null;
        } else {
            $this->constraint = "$constraint";
        }
        return $this;
    }

    /**
     * unserialize a XDDL-node to an object
     *
     * Returns the unserialized object.
     *
     * @param   \SimpleXMLElement  $node    XML node
     * @param   mixed              $parent  parent node (if any)
     * @return  \Yana\Db\Ddl\Constraint
     */
    public static function unserializeFromXDDL(\SimpleXMLElement $node, $parent = null)
    {
        $attributes = $node->attributes();
        $name = "";
        if (isset($attributes['name'])) {
            $name = (string) $attributes['name'];
        }
        $ddl = new self($name);
        $ddl->_unserializeFromXDDL($node);
        return $ddl;
    }

}

?>