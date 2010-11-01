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
 * counter wrapper
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
 * @access      public
 * @package     yana
 * @subpackage  database
 */
class Counter extends Sequence
{
    /**#@+
     * @access protected
     * @ignore
     */
    /** @var boolean  */ protected $useIp = true;
    /** @var string   */ protected $info = "";
    /** @var array    */ protected $ip = array();

    /**#@-*/

    /**
     * @access protected
     * @static
     * @var array
     * @ignore
     */
    protected static $instances = array();

    /**
     * create a new instance
     *
     * This function reads all counter information from the database and initializes a new instance.
     *
     * A NotFoundException is thrown, if the counter does not exist.
     *
     * @param   string    $name  counter name
     * @throws  NotFoundException  if the counter does not exist
     */
    public function __construct($name)
    {
        assert('is_string($name); // Invalid argument type argument 1. String expected.');

        // establish datbase connection
        if (empty(parent::$db)) {
            parent::_connect();
        }

        parent::__construct($name);
        $row = parent::$db->select("counter.$name");
        if (empty($row)) {
            throw new NotFoundException("No such counter '$name'.", E_USER_WARNING);
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
     * @access  public
     * @ignore
     */
    public function __destruct()
    {
        parent::__destruct();
        $row = array(
            'name' => $this->name,
            'useip' => $this->useIp,
            'ip' => $this->ip,
            'info' => $this->info
        );
        if (parent::$db->update("counter.{$this->name}", $row)) {
            parent::$db->commit();
        }
    }

    /**
     * create new counter
     *
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
     * @access  public
     * @static
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
    public static function create($name, $increment = 1, $start = null, $min = null, $max = null, $cycle = false, $useIP = true)
    {
        assert('is_string($name); // Invalid argument type argument 1. String expected.');
        assert('is_bool($useIP); // Invalid argument type argument 2. Boolean expected.');

        // establish datbase connection
        if (empty(parent::$db)) {
            parent::_connect();
        }

        if (parent::create($name, $increment, $start, $min, $max, $cycle)) {

            // create datbase entry
            $row = array(
                'name' => (string) $name,
                'useip' => (bool) $useIP,
                'ip' => array()

            );

            if (parent::$db->insert("counter.$name", $row) && parent::$db->commit()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }

    }

    /**
     * check if counter exists
     *
     * Returns bool(true) if a counter with the given name exists and bool(false) otherwise.
     *
     * @access  public
     * @static
     * @param   string  $name  counter name
     * @return  bool
     */
    public static function exists($name)
    {
        assert('is_string($name); // Invalid argument type argument 1. String expected.');

        // establish datbase connection
        if (empty(parent::$db)) {
            parent::_connect();
        }

        return (parent::$db->exists("counter.$name") === true);
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
     * @param   string  $name  counter name
     * @return  bool
     */
    public static function drop($name)
    {
        assert('is_string($name); // Invalid argument type argument 1. String expected.');

        // establish database connection
        if (empty(parent::$db)) {
            parent::_connect();
        }

        // remove database entry
        if (!parent::$db->remove("counter.$name")) {
            return false;
        }
        if (!parent::$db->write()) {
            return false;
        }
        if (!parent::drop($name)) {
            return false;
        }
        return true;
    }

    /**
     * check if counter uses IP
     *
     * @access  public
     * @return  bool
     */
    public function hasIp()
    {
        if ($this->useIp) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * set if counter should use IP
     *
     * @access  public
     * @param   bool  $useIp  true: check for IP, false: ignore IP
     */
    public function useIp($useIp = true)
    {
        assert('is_bool($useIp); // Invalid argument type argument 1. Boolean expected.');
        if (!empty($useIp)) {
            $this->useIp = true;
        } else {
            $this->useIp = false;
        }
    }

    /**
     * get counter info
     *
     * The 'info' field is an optional text value, that describes the counter.
     *
     * This function returns the current description of counter.
     *
     * @access  public
     * @return  string
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * set counter info
     *
     * The 'info' field is an optional text value, that describes the counter.
     *
     * This function returns the current description of counter.
     *
     * @access  public
     * @param   string  $info  optional text value, that describes the counter
     * @return  string
     */
    public function setInfo($info)
    {
        assert('is_string($info); // Invalid argument type argument 1. String expected.');
        $this->info = $info;
    }

    /**
     * get counter ips
     *
     * The 'ip' field is a mandatory array of text values, that represents the IPs.
     *
     * This function returns the actual ips.
     *
     * @access  public
     * @return  array
     */
    public function getIps()
    {
        return array_keys($this->ip);
    }

    /**
     * Increment/Decrement counter
     *
     * Adds $ammount (defaults to +1) to the counter $id and sets
     * the counter description (the 'info' field)
     * to $info (defaults to "").
     *
     * Note: as you might already have guessed, using a negative
     * value for $ammount decrements the counter.
     *
     * @access  public
     * @return  int
     */
    public function getNextValue()
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
     * get instance
     *
     * This function reads all counter information from the database and initializes a new instance.
     *
     * If the counter does not exist, it get's created automatically.
     *
     * @access  public
     * @static
     * @param   string    $name  counter name
     * @return  Counter
     */
    public static function getInstance($name)
    {
        assert('is_string($name); // Invalid argument type argument 1. String expected.');
        if (!isset(self::$instances[$name])) {
            if (!self::exists($name)) {
                self::create($name);
            }
            self::$instances[$name] = new Counter($name);
        }
        assert('isset(self::$instances[$name]);');
        return self::$instances[$name];
    }

}

?>