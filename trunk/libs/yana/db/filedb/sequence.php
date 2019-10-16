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
 * @package     yana
 * @subpackage  db
 */
class Sequence extends \Yana\Core\StdObject
{

    /**
     * @var string
     * @ignore
     */
    protected $name = "";

    /**
     * @var int
     * @ignore
     */
    protected $value = 1;

    /**
     * @var int
     * @ignore
     */
    protected $increment = 1;

    /**
     * @var int
     * @ignore
     */
    protected $min = 1;

    /**
     * @var int
     * @ignore
     */
    protected $max = PHP_INT_MAX;

    /**
     * @var bool
     * @ignore
     */
    protected $cycle = false;

    /**
     * @var \Yana\Db\IsConnection
     */
    private static $db = null;

    /**
     * Reads all sequence information from the database and initializes a new instance.
     *
     * @param   string  $name  name of sequence
     * @throws  \Yana\Db\Queries\Exceptions\NotFoundException  if the sequence does not exist
     */
    public function __construct(string $name)
    {
        $query = new \Yana\Db\Queries\Select(self::_getDb());
        $query->setTable("sequences")
            ->setRow($name);
        $row = $query->getResults();
        if (empty($row)) {
            throw new \Yana\Db\Queries\Exceptions\NotFoundException("No such sequence '$name'.", \Yana\Log\TypeEnumeration::WARNING);
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
     * Establish database connection.
     *
     * @param   \Yana\Db\IsConnection $db database connection
     * @ignore
     */
    protected static function _connect(\Yana\Db\IsConnection $db = null)
    {
        if (is_null($db)) {
            $builder = new \Yana\ApplicationBuilder();
            $db = $builder->buildApplication()->connect("sequences");
        }
        return $db;
    }

    /**
     * @return  \Yana\Db\IsConnection
     */
    protected static function _getDb(): \Yana\Db\IsConnection
    {
        if (!isset(self::$db)) {
            self::$db = self::_connect();
        }
        return self::$db;
    }

    /**
     * Persist object properties to database.
     *
     * @ignore
     */
    public function __destruct()
    {
        try {
            $row = array(
                'name' => $this->name,
                'value' => $this->value,
                'increment' => $this->increment,
                'min' => $this->min,
                'max' => $this->max,
                'cycle' => $this->cycle
            );
            $db = self::_getDb();
            $query = new \Yana\Db\Queries\Update($db);
            $query->setTable("sequences")->setRow($this->name)->setValues($row)->sendQuery();
            $db->commit(); // may throw exception
        } catch (\Exception $e) { // Destructor may not throw exceptions
            unset($e);
        }
    }

    /**
     * Get increment value.
     *
     * @return  int
     */
    public function getIncrement(): int
    {
        return $this->increment;
    }

    /**
     * Set increment value.
     *
     * @param   int  $increment  new value of this property
     * @return  $this
     */
    public function setIncrement(int $increment)
    {
        $this->increment = (int) $increment;
    }

    /**
     * Get maximum sequence value.
     *
     * @return  int
     */
    public function getMax(): int
    {
        return $this->max;
    }

    /**
     * Set maximum sequence value.
     *
     * @param   int  $max   maximal value
     * @return  $this
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when $max is smaller then minimum value
     */
    public function setMax($max)
    {
        assert(is_int($max), 'Invalid argument type argument 1. Integer expected.');
        if ($max >= $this->min) {
            $this->max = (int) $max;
            return $this;
        } else {
            $message = "Maximum value '{$max}' must be bigger then minimum value '{$this->min}' in sequence '".
                "{$this->name}'.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, \Yana\Log\TypeEnumeration::WARNING);
        }
    }

    /**
     * Get minimum value.
     *
     * @return  int
     */
    public function getMin(): int
    {
        return $this->min;
    }

    /**
     * Set minimum value.
     *
     * @param   int  $min   minimal value
     * @return  $this
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when $min is bigger then maximum value
     */
    public function setMin(int $min)
    {
        assert(is_int($min), 'Invalid argument type argument 1. Integer expected.');
        if ($min <= $this->max) {
            $this->min = (int) $min;
            return  $this;
        } else {
            $message = "Minimum value '{$min}' must be smaller then maximum value '{$this->max}' in sequence '".
                "{$this->name}'.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, \Yana\Log\TypeEnumeration::WARNING);
        }
    }

    /**
     * Is cyclic.
     *
     * @return  bool
     */
    public function isCycle(): bool
    {
        return (bool) $this->cycle;
    }

    /**
     * Set cyclic.
     *
     * @param   bool  $cycle  new value of this property
     * @return  $this
     */
    public function setCycle(bool $cycle)
    {
        $this->cycle = (bool) $cycle;
        return $this;
    }

    /**
     * Create new sequence.
     *
     * Create a sequence with the given name and arguments.
     *
     * An Error is reported if a sequence with the same name already exists.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @param   string  $name       unique name for this sequence
     * @param   int     $increment  must not be 0
     * @param   int     $start      must be within range [$min, $max]
     * @param   int     $min        must be < $max
     * @param   int     $max        must be > $min
     * @param   bool    $cycle      true: wrap values around, false: report an error when max/min reached
     * @return  bool
     */
    public static function create(
        string $name, int $increment = 1, ?int $start = null, ?int $min = null, ?int $max = null, bool $cycle = false, bool $useIP = true): bool
    {
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

        try {
            $db = self::_getDb();
            $query = new \Yana\Db\Queries\Insert($db);
            $query->setTable("sequences");
            $query->setRow($name);
            $query->setValues($row);
            $query->sendQuery();
            $db->commit(); // may throw exception
            return true;
        } catch (\Yana\Db\DatabaseException $e) {
            return false;
        }
    }

    /**
     * Drop an existing sequence with the given name.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @param   string  $name   sequence name
     * @return  bool
     */
    public static function drop(string $name): bool
    {
        // remove datbase entry
        try {
            $db = self::_getDb();
            $query = new \Yana\Db\Queries\Delete(self::_getDb());
            $query->setTable("sequences");
            $query->setRow($name);
            $query->sendQuery();
            $db->commit(); // may throw exception
            $success = true;
        } catch (\Exception $e) {
            unset($e);
            $success = false;
        }
        return (bool) $success;
    }

    /**
     * Get next sequence value.
     *
     * @return  int
     * @throws  \Yana\Core\Exceptions\OutOfBoundsException  when new value is < minimum or > maximum
     */
    public function getNextValue(): int
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
            throw new \Yana\Core\Exceptions\OutOfBoundsException($message, \Yana\Log\TypeEnumeration::WARNING);
        }
        return $this->value;
    }

    /**
     * Get current sequence value.
     *
     * @return  int
     */
    public function getCurrentValue(): int
    {
        return $this->value;
    }

    /**
     * Check if sequence exists.
     *
     * Returns bool(true) if a sequence with the given name exists and bool(false) otherwise.
     *
     * @param   string  $name  sequence name
     * @return  bool
     */
    public static function exists(string $name): bool
    {
        $query = new \Yana\Db\Queries\SelectExist(self::_getDb());
        return $query->setTable("sequences")->setRow($name)->doesExist();
    }

    /**
     * Set current sequence value.
     *
     * @param   int  $value     current sequence value
     * @return  $this
     * @throws  \Yana\Core\Exceptions\OutOfBoundsException  when $value < minimum or $value > maximum
     */
    public function setCurrentValue(int $value)
    {
        if ($value >= $this->min && $value <= $this->max) {
            $this->value = $value;
            return $this;
        } else {
            $message = "Value '{$value}' must be within range [{$this->min},{$this->max}] in sequence '{$this->name}'.";
            throw new \Yana\Core\Exceptions\OutOfBoundsException($message, \Yana\Log\TypeEnumeration::WARNING);
        }
    }

    /**
     * Compare with another object.
     *
     * Returns bool(true) if this object and $anotherObject
     * are equal and bool(false) otherwise.
     *
     * Two instances are considered equal if and only if
     * they are both objects of the same class, they
     * both refer to the same filesystem resource and use
     * the same IP settings.
     *
     * @param    \Yana\Core\IsObject  $anotherObject  any object or var you want to compare
     * @return   bool
     */
    public function equals(\Yana\Core\IsObject $anotherObject)
    {
        return ($anotherObject instanceof $this) && ($this->name === $anotherObject->name);
    }

}

?>