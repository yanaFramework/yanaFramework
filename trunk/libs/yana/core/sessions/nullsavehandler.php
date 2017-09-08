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

namespace Yana\Core\Sessions;

/**
 * <<adapter>> For unit tests only.
 *
 * @package     yana
 * @subpackage  core
 * @link        http://www.php.net/manual/en/class.sessionhandlerinterface.php
 */
class NullSaveHandler extends \Yana\Core\Object implements \Yana\Core\Sessions\IsSessionSaveHandler
{

    /**
     * @var  array
     */
    private $_data = array();

    /**
     * Does nothing.
     *
     * @param   string  $savePath   The path where to store/retrieve the session
     * @param   string  $sessionId  A unique identifier
     * @return  bool
     * @ignore
     */
    public function open($savePath, $sessionId)
    {
        return true;
    }

    /**
     * Does nothing.
     *
     * @return  bool
     * @ignore
     */
    public function close()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @param   string  $id  A unique identifier
     * @return  string
     */
    public function read($id)
    {
        assert('is_string($id); // $id expected to be String');
        $value = "";
        if (isset($this->_data[(string) $id])) {
            $value = (string) $this->_data[(string) $id];
        }
        return $value;
    }

    /**
     * {@inheritdoc}
     * 
     * @param   string  $id    A unique identifier
     * @param   string  $data  the encoded session data
     * @return  bool
     */
    public function write($id, $data)
    {
        assert('is_string($id); // $id expected to be String');
        assert('is_string($data); // $data expected to be String');
        $this->_data[(string) $id] = (string) $data;
        return true;
    }

    /**
     * {@inheritdoc}
     * 
     * @param   string  $id    A unique identifier
     * @return  bool
     */
    public function destroy($id)
    {
        assert('is_string($id); // $id expected to be String');
        unset($this->_data[(string) $id]);
        return true;
    }

    /**
     * Does nothing.
     *
     * The Memcache server has it's own garbage collector.
     * 
     * @param   int  $maxlifetime  Sessions not updated for maxlifetime seconds will be removed
     * @return  bool
     * @ignore
     */
    public function gc($maxlifetime)
    {
        return true;
    }

}

?>