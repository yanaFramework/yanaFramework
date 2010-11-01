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
 * <<adapter>> data adapter
 *
 * Session adapter, that stores and restores the given object from the session settings.
 *
 * @access      public
 * @package     yana
 * @subpackage  core
 */
class SessionAdapter extends Object implements IsDataAdapter
{
    /**
     * session index
     *
     * Used to identify where in the session to store the retrieved data.
     * Will be used as follows: $_SESSION[$index].
     *
     * @access  protected
     * @var     string
     */
    protected $index = __CLASS__;

    /**
     * constructor
     *
     * @access  public
     * @param   string  $index  where to store session data $_SESSION[$index]
     */
    public function __construct($index = __CLASS__)
    {
        assert('is_string($index); // Wrong argument type argument 1. String expected');
        $this->index = "$index";
    }

    /**
     * check if a instance of the given id exists
     *
     * Returns bool(true) if there is an instance with the given id and
     * bool(false) otherwise.
     *
     * @access  public
     * @param   string  $id  instance id
     * @return  bool
     */
    public function isValid($id)
    {
        return isset($_SESSION[$this->index][$id]);
    }

    /**
     * get instance
     *
     * Return an associative array of data associated with the requested instance.
     * Throws an exception if the given id is not valid.
     *
     * @access  public
     * @param   string  $id  instance id
     * @return  array
     * @throws  NotFoundException  if the instance does not exist
     */
    public function getInstance($id)
    {
        if (!$this->isValid($id)) {
            throw new NotFoundException("There is no session data on instance: '$id'.");
        }
        return unserialize($_SESSION[$this->index][$id]);
    }

    /**
     * return array of ids in use
     *
     * @access  public
     * @return  array
     */
    public function getIds()
    {
        if (isset($_SESSION[$this->index])) {
            return array_keys($_SESSION[$this->index]);
        } else {
            return array();
        }
    }

    /**
     * update instance
     *
     * Takes an instance, checks it's state and updates the data respondingly.
     *
     * @access  public
     * @param   AbstractDataContainer $container  instance that should be updated
     */
    public function updateInstance(AbstractDataContainer $container)
    {
        switch (true)
        {
            case $container->isModified():
            case $container->isNew():
                $data = get_object_vars($container);
                $_SESSION[$this->index][$container->id] = serialize($data);
            break;
            case $container->isDropped():
                unset($_SESSION[$this->index][$container->id]);
            break;
            default:
                // intentionally left blank
            break;
        }
    }

}

?>