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
 * Counter wrapper.
 *
 * This class implements persistent counters - optional with IP checking -
 * for various purposes: e.g. visitor counts, statistics, polls.
 * with useIp on it does count different ip-accesses
 * i.e. it supresses mutiple accesses to one counter in a certain time.
 *
 * As a convention to naming your counters, it is recommended to use the class name as a prefix.
 * You are encouraged to use a single backslash as a namespace delimiter.
 * Example: MyQuestionnaireClass\FavouritePets\Dogs
 *
 * @package     yana
 * @subpackage  db
 */
class Counter extends \Yana\Db\FileDb\Sequence
{

    /**
     * @var  bool
     * @ignore
     */
    protected $useIp = true;

    /**
     * @var  string
     * @ignore
     */
    protected $info = "";

    /**
     * @var  array
     * @ignore
     */
    protected $ip = array();

    /**
     * @var array
     * @ignore
     */
    protected static $instances = array();

    /**
     * Reads all counter information from the database and initializes a new instance.
     *
     * @param   string    $name  counter name
     * @throws  \Yana\Core\Exceptions\NotFoundException  if the counter does not exist
     */
    public function __construct(string $name)
    {
        // establish datbase connection
        $db = self::_getDb();

        parent::__construct($name);
        $query = new \Yana\Db\Queries\Select($db);
        $row = $query->setTable("counter")->setRow($name)->getResults();
        $row2 = $db->select("counter.$name");
        if (empty($row)) {
            throw new \Yana\Core\Exceptions\NotFoundException("No such counter '$name'.", \Yana\Log\TypeEnumeration::WARNING);
        }

        if (isset($row['USEIP'])) {
            $this->useIp = (bool) $row['USEIP'];
        }
        if (isset($row['IP'])) {
            $this->ip = $row['IP'];
        }
        if (isset($row['INFO'])) {
            $this->info = (string) $row['INFO'];
        }
    }

    /**
     * persist object properties to database
     *
     * Important note: this function requires the database connection to be stable and not closed
     * during shutdown sequence until the destructor is finished.
     *
     * @ignore
     */
    public function __destruct()
    {
        try {
            parent::__destruct();
            $row = array(
                'name' => $this->name,
                'useip' => $this->useIp,
                'ip' => $this->ip,
                'info' => $this->info
            );
            $db = self::_getDb();
            $query = new \Yana\Db\Queries\Update($db);
            $query->setTable("counter")->setRow($this->name)->setValues($row)->sendQuery();
            $db->commit(); // may throw exception
        } catch (\Exception $e) {
            unset($e); // Destructor may not throw exceptions
        }
    }

    /**
     * Create a counter with the given name and arguments.
     *
     * Use the $useIp parameter with the setting bool(true) to create a counter with IP checking
     * (does'nt change on reload) or bool(false) to create a counter without IP checking
     * (always changes when an update is called).
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
     * @param   bool    $cycle      true: wrap values around,
     *                              false: report an error when max/min reached
     * @param   bool    $useIP      true: don't increment on reloads,
     *                              false: increment always
     * @return  bool
     */
    public static function create(
        string $name, int $increment = 1, ?int $start = null, ?int $min = null, ?int $max = null, bool $cycle = false, bool $useIP = true): bool
    {

        if (parent::exists($name) || parent::create($name, $increment, $start, $min, $max, $cycle)) {

            // create datbase entry
            $row = array(
                'name' => (string) $name,
                'useip' => (bool) $useIP,
                'ip' => array()

            );

            try {
                $db = self::_getDb();
                $query = new \Yana\Db\Queries\Insert($db);
                self::_counterExists($name) ||
                    ($query->setTable("counter")->setRow($name)->setValues($row)->sendQuery() && $db->commit());
                return true;

            } catch (\Exception $e) {
                unset($e);
            }
        }
        return false;

    }

    /**
     * Check if counter exists.
     *
     * Returns bool(true) if a counter with the given name exists and bool(false) otherwise.
     * Does NOT check the parent.
     *
     * @param   string  $name  counter name
     * @return  bool
     */
    private static function _counterExists(string $name): bool
    {
        $query = new \Yana\Db\Queries\SelectExist(self::_getDb());
        return $query->setTable("counter")->setRow($name)->doesExist();
    }

    /**
     * Check if counter exists.
     *
     * Returns bool(true) if a counter with the given name exists and bool(false) otherwise.
     *
     * @param   string  $name  counter name
     * @return  bool
     */
    public static function exists(string $name): bool
    {
        return self::_counterExists($name) && parent::exists($name);
    }

    /**
     * Drop an existing sequence with the given name.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @param   string  $name  counter name
     * @return  bool
     */
    public static function drop(string $name): bool
    {
        // remove database entry
        try {
            $db = self::_getDb();
            $query = new \Yana\Db\Queries\Delete(self::_getDb());
            $query->setTable("counter");
            $query->setRow($name);
            $query->sendQuery();
            $db->commit(); // may throw exception
            return parent::drop($name);
        } catch (\Exception $e) {
            unset($e);
        }
        return false;
    }

    /**
     * Check if counter uses IP.
     *
     * @return  bool
     */
    public function hasIp(): bool
    {
        return (bool) $this->useIp;
    }

    /**
     * Set if counter should use IP.
     *
     * @param   bool   $useIp  true: check for IP, false: ignore IP
     * @return  $this
     */
    public function useIp(bool $useIp = true)
    {
        $this->useIp = (bool) $useIp;
        return $this;
    }

    /**
     * Get counter info.
     *
     * The 'info' field is an optional text value, that describes the counter.
     *
     * This function returns the current description of counter.
     *
     * @return  string
     */
    public function getInfo(): string
    {
        return $this->info;
    }

    /**
     * Set counter info.
     *
     * The 'info' field is an optional text value, that describes the counter.
     *
     * This function returns the current description of counter.
     *
     * @param   string  $info  optional text value, that describes the counter
     * @return  $this
     */
    public function setInfo(string $info)
    {
        $this->info = $info;
        return $this;
    }

    /**
     * Get counter IPs.
     *
     * The 'ip' field is a mandatory array of text values, that represents the IPs.
     *
     * This function returns the actual IPs.
     *
     * @return  array
     */
    public function getIps(): array
    {
        return array_keys($this->ip);
    }

    /**
     * Increment/Decrement counter.
     *
     * Adds $ammount (defaults to +1) to the counter $id and sets
     * the counter description (the 'info' field)
     * to $info (defaults to "").
     *
     * Note: as you might already have guessed, using a negative
     * value for $ammount decrements the counter.
     *
     * @return  int
     */
    public function getNextValue(): int
    {
        /**
         * reload detection
         *
         * The option "use_ip" triggers wether to
         * use a counter with IP logging to detect
         * page reloads (true) or not (false).
         */
        if ($this->useIp) {
            /* iterate over IPs and remove those that have expired */
            foreach ($this->ip as $ip => $expires)
            {
                if ($expires < time()) {
                    unset($this->ip[$ip]);
                }
            }
            unset($ip);

            if (isset($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            } else {
                /* This will certainly happen when running the script via the CLI,
                 * e.g. by running a cron-job.
                 *
                 * Note: this is not set to 127.0.0.1 since the remote address
                 * is really undefined - we just guess that it MIGHT be localhost,
                 * but we can't be sure about it.
                 */
                $ip = '0.0.0.0';
            }

            /* page reload detected - file remains unchanged */
            if (isset($this->ip[$ip])) {
                return $this->getCurrentValue();
            }

            /* save remote address for later review */
            $this->ip[$ip] = time() + 10000;
        } else {
            /* continue without checking the remote address for page-reloads */
        }

        return parent::getNextValue();
    }

    /**
     * Get instance.
     *
     * This function reads all counter information from the database and initializes a new instance.
     *
     * If the counter does not exist, it get's created automatically.
     *
     * @param   string    $name  counter name
     * @return  self
     */
    public static function getInstance(string $name)
    {
        if (!isset(self::$instances[$name])) {
            if (!self::exists($name)) {
                self::create($name);
            }
            self::$instances[$name] = new self($name);
        }
        assert(isset(self::$instances[$name]), 'isset(self::$instances[$name])');
        return self::$instances[$name];
    }

}

?>