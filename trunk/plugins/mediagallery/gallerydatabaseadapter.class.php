<?php
/**
 * Gallery Manager
 *
 * This class is meant to be used to read gallery items from the database.
 *
 * @author     Thomas Meyer
 * @license    http://www.gnu.org/licenses/gpl.txt
 *
 * @package    yana
 * @subpackage plugins
 */

/**
 * Gallery
 *
 * @package     yana
 * @subpackage  plugins
 */
class GalleryDatabaseAdapter extends DatabaseAdapter
{
    /**
     * get where clause as array
     *
     * @access  private
     * @return  array
     */
    private function _getWhere()
    {
        return array(
            array('user_created', '=', YanaUser::getUserName()),
            'or',
            array('public', '=', true)
        );
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
        return $this->db->exists($this->table . ".$id", $this->_getWhere());
    }

    /**
     * get ids of galleries
     *
     * Returns the gallery ids as an unordered list.
     * If there are no galleries, an empty array is returned.
     *
     * @access  public
     * @param   string $id
     * @return  array
     */
    public function getIds()
    {
        return $this->db->select($this->table . ".*.mediafolder_id", $this->_getWhere());
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
        $where = array(
            array('user_created', '=', YanaUser::getUserName()),
            'or',
            array('public', '=', true)
        );
        $data = $this->db->select($this->table . ".$id", $where);
        if (empty($data)) {
            throw new NotFoundException("There is no session data on instance: '$id'.");
        }
        $data = array();
        foreach ($raw as $name => $value)
        {
            $name = mb_strtolower($name);
            $name = str_replace($this->table . '_', '', $name);
            $data[$name] = $value;
        }
        return $data;
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
        $data = array();
        $table = $this->db->schema->getTable($this->table);
        foreach ($table->getColumnNames() as $name)
        {
            $name = mb_strtolower($name);
            $key = str_replace($this->table . '_', '', $name);
            if (isset($container->$key)) {
                $data[$name] = $container->$key;
            }
        }
        $id = $container->id;
        switch (true)
        {
            case $container->isNew():
                $data['user_created'] = YanaUser::getUserName();
                $data['time_created'] = time();
                $this->db->insert($this->table . ".$id", $data);
            break;
            case $container->isModified():
                $data['user_modified'] = YanaUser::getUserName();
                $data['time_modified'] = time();
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