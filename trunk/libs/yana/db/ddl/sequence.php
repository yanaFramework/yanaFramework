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
 * database sequence structure
 *
 * This wrapper class represents the structure of a database
 *
 * While sequences are part of the SQL-2003 standard, they are not widely supported by most vendors.
 * Except for PostgreSQL, where they are well known feature. They may be simulated for other DBMS
 * though, by using the {@link Sequence} class.
 *
 * Note that there are implicit and explicit sequences. E.g. an implicit sequence is created when
 * you create an auto-increment column.
 * This class is meant to be used for explicit, named sequences only.
 * You must not specify implicit sequences, as these are created and maintained by the DBS itself.
 *
 * Also note that due to the fact that some DBMS interpret the integer 0 to be equal to NULL, you
 * are encouraged NOT to create sequences that may containg the value 0 at any time.
 * In addition, some applications may reserve index 0 for default values (as in data-warehousing).
 *
 * @access      public
 * @package     yana
 * @subpackage  db
 */
class Sequence extends \Yana\Db\Ddl\AbstractNamedObject implements \Yana\Db\Ddl\IsIncludableDDL
{
    /**#@+
     * @ignore
     * @access  protected
     */

    /**
     * tag name for persistance mapping: object <-> XDDL
     * @var  string
     */
    protected $xddlTag = "sequence";

    /**
     * attributes for persistance mapping: object <-> XDDL
     * @var  array
     */
    protected $xddlAttributes = array(
        'name'      => array('name',      'nmtoken'),
        'start'     => array('start',     'int'),
        'increment' => array('increment', 'int'),
        'min'       => array('min',       'int'),
        'max'       => array('max',       'int'),
        'cycle'     => array('cycle',     'bool')
    );

    /**
     * tags for persistance mapping: object <-> XDDL
     * @var  array
     */
    protected $xddlTags = array(
        'description' => array('description', 'string')
    );

    /** @var string      */ protected $description = null;
    /** @var int         */ protected $increment = 1;
    /** @var bool        */ protected $cycle = null;
    /** @var int         */ protected $start = null;
    /** @var int         */ protected $min = null;
    /** @var int         */ protected $max = null;
    /** @var \Yana\Db\Ddl\Database */ protected $parent = null;

    /**#@-*/

    /**
     * Initialize instance.
     *
     * @param  string       $name    sequence name
     * @param  \Yana\Db\Ddl\Database  $parent  parent database
     */
    public function __construct($name, \Yana\Db\Ddl\Database $parent = null)
    {
        parent::__construct($name);
        $this->parent = $parent;
    }

    /**
     * Fet parent database.
     *
     * @return  \Yana\Db\Ddl\Database
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Get the description.
     *
     * The description is used for your documentation purposes only.
     *
     * @access  public
     * @return  string
     */
    public function getDescription()
    {
        if (is_string($this->description)) {
            return $this->description;
        } else {
            return null;
        }
    }

    /**
     * Set the description.
     *
     * The description is used for your documentation purposes only.
     *
     * @access  public
     * @param   string  $description  new value of this property
     * @return  \Yana\Db\Ddl\Sequence
     */
    public function setDescription($description)
    {
        assert('is_string($description)', ' Wrong type for argument 1. String expected');
        if (empty($description)) {
            $this->description = null;
        } else {
            $this->description = "$description";
        }
        return $this;
    }

    /**
     * Get start value.
     *
     * A sequence always starts with an initial value.
     *
     * The value defaults to the minimal value for ascending sequences and to the maximal value for
     * descending sequences.
     *
     * @access  public
     * @return  int
     */
    public function getStart()
    {
        if (is_int($this->start)) {
            return $this->start;

        // return a default value
        } else {
            return null;
        }
    }

    /**
     * Set start value.
     *
     * Set the start value of the sequence to a custom value.
     * The default value is the minimal value for ascending sequences and the maximal value for
     * descending sequences.
     *
     * Note: the start value must lay within range of the minimal and maximal sequence number.
     * To reset the value, leave the argument $start empty.
     *
     * @access  public
     * @param   int  $start  start value
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the start value is not within the min and max values
     */
    public function setStart($start = null)
    {
        assert('is_null($start) || is_int($start)', ' Wrong type for argument 1. Integer expected');
        if (empty($start)) {
            $this->start = null;

        } elseif ((!is_null($this->min) && $start < $this->min) || (!is_null($this->max) && $start > $this->max)) {
            $message = "Start value '{$start}' must be within range [{$this->min},{$this->max}] " .
                "in sequence '{$this->name}'.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, E_USER_WARNING);

        } else {
            $this->start = (int) $start;
        }
        return $this;
    }

    /**
     * Get increment value.
     *
     * An increment-value (or step-value) specifies the number that is added to
     * the sequence each time it is incremented.
     *
     * The default value is 1.
     *
     * @access  public
     * @return  int
     */
    public function getIncrement()
    {
        if (is_int($this->increment)) {
            return $this->increment;
        } else {
            return 1;
        }
    }

    /**
     * Set increment value.
     *
     * An increment-value (or step-value) specifies the number that is added to
     * the sequence each time it is incremented.
     *
     * The argument $increment must not be 0.
     *
     * The default value is 1.
     *
     * To reset the value, leave the argument $increment empty.
     *
     * @access  public
     * @param   int  $increment  increment value
     * @return  \Yana\Db\Ddl\Sequence
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the increment value equals 0.
     */
    public function setIncrement($increment = 1)
    {
        assert('is_int($increment)', ' Wrong type for argument 1. Integer expected');
        if (!empty($increment)) {
            $this->increment = (int) $increment;

        } else {
            $message = "Increment value must not be 0 in sequence '{$this->name}'.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, E_USER_WARNING);
        }
        return $this;
    }

    /**
     * Get minimum value.
     *
     * The minimal value is a lower boundary for a sequence.
     * All sequence values must be larger or equal the minimal value.
     *
     * @access  public
     * @return  int
     */
    public function getMin()
    {
        if (is_int($this->min)) {
            return $this->min;
        } else {
            return null;
        }
    }

    /**
     * Set minimum value.
     *
     * You may set a lower boundary for a sequence.
     *
     * Note: the start value may not be lower than the minimal value.
     * The minimal value may not be larger than the maximal value.
     *
     * @access  public
     * @param   int  $min  minimum value
     * @return  \Yana\Db\Ddl\Sequence
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the min value is greater than the current max value
     */
    public function setMin($min = null)
    {
        assert('is_null($min) || is_int($min)', ' Wrong type for argument 1. Integer expected');
        if (is_null($min)) {
            $this->min = null;

        } elseif ((!is_null($this->start) && $this->start < $min) || (!is_null($this->max) && $min > $this->max)) {
            $message = "Minimum value '{$min}' must be < {$this->start} and < {$this->max} " .
                "in sequence '{$this->name}'.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, E_USER_WARNING);

        } else {
            $this->min = (int) $min;
        }
        return $this;
    }

    /**
     * Get maximum value.
     *
     * The maximum value is an upper boundary for a sequence.
     * All sequence values must be smaller or equal the maximum value.
     *
     * @access  public
     * @return  int
     */
    public function getMax()
    {
        if (is_int($this->max)) {
            return $this->max;
        } else {
            return null;
        }
    }

    /**
     * Set maximum value.
     *
     * You may set an upper boundary for a sequence.
     *
     * Note: the start value may not be larger than the maximum value.
     * The maximum value may not be smaller or equal the minimum value.
     *
     * @access  public
     * @param   int  $max  maximum value
     * @return  \Yana\Db\Ddl\Sequence
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the max value is smaller than the current min value
     */
    public function setMax($max = null)
    {
        assert('is_null($max) || is_int($max)', ' Wrong type for argument 1. Integer expected');
        if (empty($max)) {
            $this->max = null;

        } elseif ((!is_null($this->start) && $this->start > $max) || (!is_null($this->min) && $max < $this->min)) {
            $message = "Maximum value '{$max}' must be > {$this->min} and > {$this->start} " .
                "in sequence '{$this->name}'.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, E_USER_WARNING);

        } else {
            $this->max = (int) $max;
        }
        return $this;
    }

    /**
     * Check wether sequence is number cycle.
     *
     * If a sequence is a number cycle and the value of the sequence reaches
     * an upper- or lower-boundary, it will be reset to the minimum value for
     * an ascenindg sequence or the maximum value for a descending sequence.
     *
     * @access  public
     * @return  bool
     */
    public function isCycle()
    {
        return !empty($this->cycle);
    }

    /**
     * set wether sequence is number cycle
     *
     * If a sequence is a number cycle, and the value of the sequence reaches
     * an upper- or lower-boundary, it will be reset to the minimum value for
     * an ascenindg sequence or the maximum value for a descending sequence.
     *
     * The default is false.
     *
     * @access  public
     * @param   bool  $isCycle  new value of this property
     * @return  \Yana\Db\Ddl\Sequence
     */
    public function setCycle($isCycle = false)
    {
        assert('is_bool($isCycle)', ' Wrong type for argument 1. Boolean expected');
        $this->cycle = (bool) $isCycle;
        return $this;
    }

    /**
     * Unserializes a XDDL-node to an instance of this class and returns it.
     *
     * @access  public
     * @static
     * @param   \SimpleXMLElement  $node    XML node
     * @param   mixed             $parent  parent node (if any)
     * @return  \Yana\Db\Ddl\Sequence
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the name attribute is missing
     */
    public static function unserializeFromXDDL(\SimpleXMLElement $node, $parent = null)
    {
        $attributes = $node->attributes();
        if (!isset($attributes['name'])) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException("Missing name attribute.", E_USER_WARNING);
        }
        $ddl = new self((string) $attributes['name'], $parent);
        $ddl->_unserializeFromXDDL($node);
        return $ddl;
    }

}

?>