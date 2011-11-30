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

namespace Yana\Db\FileDb;

/**
 * Sequence wrapper
 *
 * This class implements persistent counters for various purposes.
 * Sequences as a concept have been introduced in SQL 2003 standard.
 *
 * Yet most database vendors have (except for PostgreSQL) haven't implemented them.
 * This class is meant to emulate that feature, what it is not supported by default.
 *
 * Note: the optional data type argument as defined in SQL 2003 is not supported by any vendor yet,
 * so we won't support it here either. It defaults to 'integer'.
 *
 * @access      public
 * @package     yana
 * @subpackage  db
 */
class Sequence extends \Yana\Core\Object
{
    /**#@+
     * @ignore
     */

    /**
     * @var string
     */
    protected $name = "";

    /**
     * @var int
     */
    protected $value = 1;

    /**
     * @var int
     */
    protected $increment = 1;

    /**
     * @var int
     */
    protected $min = 1;

    /**
     * @var int
     */
    protected $max = PHP_INT_MAX;

    /**
     * @var bool
     */
    protected $cycle = false;

    /**
     * @var \Yana\Db\IsConnection
     */
    protected static $db = null;

    /**#@-*/

    /**
     * Reads all sequence information from the database and initializes a new instance.
     *
     * @param   string  $name  name of sequence
     * @throws  \Yana\Core\Exceptions\NotFoundException  if the sequence does not exist
     */
    public function __construct($name)
    {
        assert('is_string($name); // Invalid argument type argument 1. String expected.');

        // establish datbase connection
        if (empty(self::$db)) {
            self::_connect();
        }
        $row = self::$db->select("sequences.$name");
        if (empty($row)) {
            throw new \Yana\Core\Exceptions\NotFoundException("No such sequence '$name'.", E_USER_WARNING);
        }

        $this->name = (string) $name;
        if (isset($row['VALUE'])) {
            $this->value = (int) $row['VALUE'];
        }
        if (isset($row['INCREMENT'])) {
            $this->increment = (int) $row['INCREMENT'];
        }
        if (isset($row['MIN'])) {
            $this->min = (int) $row['MIN'];
        }
        if (isset($row['MAX'])) {
            $this->max = (int) $row['MAX'];
        }
        if (isset($row['CYCLE'])) {
            $this->cycle = (bool) $row['CYCLE'];
        }
    }

    /**
     * establish database connection
     *
     * @access  protected
     * @param   \Yana\Db\IsConnection $db database connection
     * @ignore
     */
    protected static function _connect(\Yana\Db\IsConnection $db = null)
    {
        if (!is_null($db)) {
            self::$db = $db;
        } else {
            self::$db = \Yana::connect('sequences');
        }
    }

    /**
     * persist object properties to database
     *
     * @access  protected
     * @ignore
     */
    public function __destruct()
    {
        $row = array(
            'name' => $this->name,
            'value' => $this->value,
            'increment' => $this->increment,
            'min' => $this->min,
            'max' => $this->max,
            'cycle' => $this->cycle
        );
        if (self::$db->update("sequences.{$this->name}", $row)) {
            self::$db->commit();
        }
    }

    /**
     * get increment
     *
     * @access  public
     * @return  int
     */
    public function getIncrement()
    {
        return $this->increment;
    }

    /**
     * set increment
     *
     * @access  public
     * @param   int  $increment  new value of this property
     */
    public function setIncrement($increment)
    {
        assert('is_int($increment); // Invalid argument type argument 1. Integer expected.');
        $this->increment = (int) $increment;
    }

    /**
     * get maximum
     *
     * @access  public
     * @return  int
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * set maximum
     *
     * @access  public
     * @param   int  $max   maximal value
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when $max is smaller then minimum value
     */
    public function setMax($max)
    {
        assert('is_int($max); // Invalid argument type argument 1. Integer expected.');
        if ($max >= $this->min) {
            $this->max = (int) $max;
        } else {
            $message = "Maximum value '{$max}' must be bigger then minimum value '{$this->min}' in sequence '".
                "{$this->name}'.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, E_USER_WARNING);
        }
    }

    /**
     * Get minimum value.
     *
     * @access  public
     * @return  int
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * Set minimum value.
     *
     * @access  public
     * @param   int  $min   minimal value
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when $min is bigger then maximum value
     */
    public function setMin($min)
    {
        assert('is_int($min); // Invalid argument type argument 1. Integer expected.');
        if ($min <= $this->max) {
            $this->min = (int) $min;
        } else {
            $message = "Minimum value '{$min}' must be smaller then maximum value '{$this->max}' in sequence '".
                "{$this->name}'.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, E_USER_WARNING);
        }
    }

    /**
     * Is cyclic.
     *
     * @access  public
     * @return  bool
     */
    public function isCycle()
    {
        return (bool) $this->cycle;
    }

    /**
     * set cyclic
     *
     * @access  public
     * @param   bool  $cycle    new value of this property
     */
    public function setCycle($cycle)
    {
        assert('is_bool($cycle); // Invalid argument type argument 1. Boolean expected.');
        $this->cycle = (bool) $cycle;
    }

    /**
     * create new sequence
     *
     * Create a sequence with the given name and arguments.
     *
     * An Error is reported if a sequence with the same name already exists.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @access  public
     * @static
     * @param   string  $name       unique name for this sequence
     * @param   int     $increment  must not be 0
     * @param   int     $start      must be within range [$min, $max]
     * @param   int     $min        must be < $max
     * @param   int     $max        must be > $min
     * @param   bool    $cycle      true: wrap values around, false: report an error when max/min reached
     * @return  bool
     */
    public static function create($name, $increment = 1, $start = null, $min = null, $max = null, $cycle = false)
    {
        assert('is_string($name); // Invalid argument type argument 1. String expected.');
        assert('is_int($increment); // Invalid argument type argument 2. Integer expected.');
        assert('is_null($start) || is_int($start); // Invalid argument type argument 3. Integer expected.');
        assert('is_null($min) || is_int($min); // Invalid argument type argument 4. Integer expected.');
        assert('is_null($max) || is_int($max); // Invalid argument type argument 5. Integer expected.');
        assert('is_bool($cycle); // Invalid argument type argument 6. Boolean expected.');
        
        // establish datbase connection
        if (empty(self::$db)) {
            self::_connect();
        }
        
        // ascending sequence
        if ($increment > 0) {
            if (is_null($min)) {
                $min = 1;
            }
            if (is_null($max)) {
                $max = PHP_INT_MAX;
            }
            if (is_null($start)) {
                $start = $min;
            }

        // descending sequence
        } else {
            if (is_null($min)) {
                $min = PHP_INT_MIN;
            }
            if (is_null($max)) {
                $max = -1;
            }
            if (is_null($start)) {
                $start = $max;
            }
        }

        // create datbase entry
        $row = array(
            'name' => (string) $name,
            'increment' => (int) $increment,
            'value' => (int) $start,
            'min' => (int) $min,
            'max' => (int) $max,
            'cycle' => (bool) $cycle
        );
    
        if (self::$db->insert("sequences.$name", $row) && self::$db->commit()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * drop sequence
     *
     * Drop an existing sequence with the given name.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @access  public
     * @static
     * @param   string  $name   sequence name
     * @return  bool
     */
    public static function drop($name)
    {
        assert('is_string($name); // Invalid argument type argument 1. String expected.');

        // establish datbase connection
        if (empty(self::$db)) {
            self::_connect();
        }

        // remove datbase entry
        if (self::$db->remove("sequences.$name") && self::$db->write()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * get next sequence value
     *
     * @access  public
     * @return  int
     * @throws  \Yana\Core\Exceptions\OutOfBoundsException  when new value is < minimum or > maximum
     */
    public function getNextValue()
    {
        $value = $this->value + $this->increment;
        // value is within range
        if ($value >= $this->min && $value <= $this->max) {
            $this->value = $value;

        // outside range for clyclic sequence (wrap around)
        } elseif ($this->cycle) {
            // ascending sequence (reset to min)
            if ($this->increment > 0) {
                $this->value = $this->min;

            // descending sequence (reset to max)
            } else {
                $this->value = $this->max;
                
            }

        // outside range for non-cyclic sequence
        } else {
            $message = "Sequence '{$this->name}' has reached it's boundary.";
            throw new \Yana\Core\Exceptions\OutOfBoundsException($message, E_USER_WARNING);
        }
        return $this->value;
    }

    /**
     * get current sequence value
     *
     * @access  public
     * @return  int
     */
    public function getCurrentValue()
    {
        return $this->value;
    }

    /**
     * check if sequence exists
     *
     * Returns bool(true) if a sequence with the given name exists and bool(false) otherwise.
     *
     * @access  public
     * @static
     * @param   string  $name  sequence name
     * @return  bool
     */
    public static function exists($name)
    {
        assert('is_string($name); // Invalid argument type argument 1. String expected.');

        // establish datbase connection
        if (empty(self::$db)) {
            self::_connect();
        }

        return (self::$db->exists("sequence.$name") === true);
    }

    /**
     * set current sequence value
     *
     * @access  public
     * @param   int  $value     current sequence value
     * @throws  \Yana\Core\Exceptions\OutOfBoundsException  when $value < minimum or $value > maximum
     */
    public function setCurrentValue($value)
    {
        assert('is_int($value); // Invalid argument type argument 1. Integer expected.');
        if ($value >= $this->min && $value <= $this->max) {
            $this->value = $value;
        } else {
            $message = "Value '{$value}' must be within range [{$this->min},{$this->max}] in sequence '{$this->name}'.";
            throw new \Yana\Core\Exceptions\OutOfBoundsException($message, E_USER_WARNING);
        }
    }

    /**
     * compare with another object
     *
     * Returns bool(true) if this object and $anotherObject
     * are equal and bool(false) otherwise.
     *
     * Two instances are considered equal if and only if
     * they are both objects of the same class, they
     * both refer to the same filesystem resource and use
     * the same IP settings.
     *
     * @access   public
     * @param    \Yana\Core\IsObject  $anotherObject  any object or var you want to compare
     * @return   string
     */
    public function equals(\Yana\Core\IsObject $anotherObject)
    {
        if ($anotherObject instanceof $this) {
            if ($this->name === $anotherObject->name) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}

?>