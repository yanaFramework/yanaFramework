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
 *
 * @ignore
 */

namespace Yana\Files;

/**
 * Class to keep try of forum flooding attempts.
 *
 * This file is intended to be used as a counter.
 *
 * @package     yana
 * @subpackage  files
 *
 * @ignore
 */
class Flood extends \Yana\Files\File
{

    /**
     * @var  int
     */
    private $_max = 0;

    /**
     * Increment counter and save changes.
     *
     * @param   string  $ip  IP to be used (defaults to current user IP)
     * @return  bool
     */
    public function set($ip = null)
    {
        if (!$this->exists()) {
            $this->create();
        }
        if (empty($ip) || !is_string($ip)) {
            if (isset($_SERVER['REMOTE_ADDR'])) {
                $REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
            } else {
                $REMOTE_ADDR = '0.0.0.0';
            }
        } else {
            $REMOTE_ADDR = $ip;
        }

        $preg = preg_replace("/\s/", "", $this->content[0]);
        if (!$this->isEmpty() && $REMOTE_ADDR != $preg || ($this->content[1] +20000) < time()) {
            $this->content[0] = $REMOTE_ADDR;
            $this->content[1] = time();
            $this->content[2] = 1;
        } else {
            $this->content[1] = time();
            $this->content[2] += 1;
        }
        $test = $this->write();
        assert('is_bool($test);');
        return $test;
    }

    /**
     * Alias of set().
     *
     * @param   scalar  $ip IP to be used (defaults to current user IP)
     * @return  bool
     */
    public function insert($ip)
    {
        $this->set($ip);
    }

    /**
     * Check if user is blocked.
     *
     * Returns bool(true) if the maximum number of entries
     * for this user have been exceeded and bool(false)
     * otherwise.
     *
     * @return  bool
     * @throws  \Yana\Core\Exceptions\NotReadableException  if the source file is not readable
     */
    public function isBlocked()
    {
        if (!$this->exists()) {
            return false;
        }
        $this->read();
        switch (true)
        {
            /*
             * 1) is deactivated
             */
            case $this->_max <= 0:
            /*
             * 2) no data found (yet) - occurs during first run
             */
            case $this->isEmpty():
            /*
             * 3) dataset is invalid (unexpected)
             */
            case count($this->content) < 3:
            /*
             * 4) user is other than the last
             */
            case strpos($this->content[0], $_SERVER['REMOTE_ADDR']) === false:
            /*
             * 5) maximum number of entries not reached yet
             */
            case $this->content[2] < $this->_max:
                return false;
            /*
             * 6) user has exceeded maximum number of entries
             */
            default:
                return true;
        } /* end switch */    
    }

    /**
     * Set maxmimum number of entries.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @param   int  $max  a positive number
     * @return  bool
     */
    public function setMax($max)
    {
        assert('is_int($max); // Wrong argument type argument 1. Integer expected');

        if ($max >= 0) {
            $this->_max = (int) $max;
            return true;
        } else {
            $message = "Expected maximum number to be a positive integer, found '{$max}' instead.";
            \Yana\Log\LogManager::getLogger()->addLog($message, \Yana\Log\TypeEnumeration::INFO);
            return false;
        }
    }

    /**
     * Get maximum number of entries.
     *
     * @return  int
     */
    public function getMax()
    {
        assert('is_int($this->_max);');
        return $this->_max;
    }

}

?>