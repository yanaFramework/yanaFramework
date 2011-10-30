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
 * Database adapter, that stores and restores the given object from a database connection.
 *
 * @access      public
 * @package     yana
 * @subpackage  core
 */
class DatabaseAdapter extends \Yana\Core\Object implements IsDataAdapter
{
    /**
     * database connection
     *
     * @access  protected
     * @var     DbStream
     */
    protected $db = null;

    /**
     * selected database table
     *
     * @access  protected
     * @var     string
     */
    protected $table = "";

    /**
     * constructor
     *
     * @access  public
     * @param   string  $index  where to store session data $_SESSION[$index]
     * @throws  \Yana\Core\Exceptions\NotFoundException  when the table is not registered in the database
     */
    public function __construct(DbStream $db, $table)
    {
        assert('is_string($table); // Wrong argument type argument 1. String expected');
        if (!$db->getSchema()->isTable($table)) {
            $message = "Table not found: '$table' in database '{$db->schema->getName()}'.";
            throw new \Yana\Core\Exceptions\NotFoundException($message);
        }
        $this->db = $db;
        $this->table = strtolower("$table");
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
        assert('is_string($id); // Wrong argument type argument 1. String expected');
        return $this->db->exists($this->table . ".$id");
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
     * @throws  \Yana\Core\Exceptions\NotFoundException  if the instance does not exist
     */
    public function getInstance($id)
    {
        $data = $this->db->select($this->table . ".$id");
        if (empty($data)) {
            throw new \Yana\Core\Exceptions\NotFoundException("There is no session data on instance: '$id'.");
        }
        return $data;
    }

    /**
     * return array of ids in use
     *
     * @access  public
     * @return  array
     */
    public function getIds()
    {
        $column = $this->db->getSchema()->getTable($this->table)->getPrimaryKey();
        return $this->db->select($this->table . ".*." . $column);
    }

    /**
     * update instance
     *
     * Takes an instance, checks it's state and updates the data respondingly.
     *
     * @access  public
     * @param   DataContainerAbstract $container  instance that should be updated
     */
    public function updateInstance(DataContainerAbstract $container)
    {
        $data = get_object_vars($container);
        $id = $container->id;
        switch (true)
        {
            case $container->isNew():
                $this->db->insert($this->table . ".$id", $data);
            break;
            case $container->isModified():
                $this->db->update($this->table . ".$id", $data);
            break;
            case $container->isDropped():
                $this->db->remove($this->table . ".$id");
            break;
            default:
                return;
            break;
        }
        $this->db->commit();
    }

}

?>