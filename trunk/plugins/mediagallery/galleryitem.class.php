<?php
/**
 * Gallery
 *
 * This class is represents a single (picture) gallery.
 *
 * @author     Thomas Meyer
 * @license    http://www.gnu.org/licenses/gpl.txt
 *
 * @package    yana
 * @subpackage plugins
 */

/**
 * Gallery item
 *
 * @package     yana
 * @subpackage  plugins
 */
class GalleryItem extends AbstractDataContainer
{

    /**
     * data adapter
     *
     * The DataAdapter is an interface. We use it to inject a dependency into the AbstractDataContainer.
     * The AbstractDataContainer uses the DataAdapter to read and write data from and to an arbitrary data source.
     *
     * @access  protected
     * @static
     * @var     IsDataAdapter
     * @ignore
     */
    protected static $dataAdapter = null;

    /**
     * instances
     *
     * List of instances by id.
     * It is used to ensure every call to getInstance returns the same instance, so changes are not lost.
     *
     * @access  protected
     * @static
     * @var     array
     * @ignore
     */
    protected static $instances = array();

    /**
     * get instance
     *
     * This factory method calls the datasource to retrieve and initialize the instance.
     *
     * @param   string  $id             instance id
     * @param   mixed   $mediaFolderId  must match column 'mediafolder_id'
     * @throws  NotFoundException  if the instance does not exist
     * @return  AbstractDataContainer
     */
    public static function getInstance($id, $mediaFolderId = null)
    {
        assert('is_string($name); // Invalid argument type argument 1. String expected.');
        if (!isset(self::$instances[$id])) {
            $adapter = self::getDataAdapter();
            $data = $adapter->getInstance($id);
            if (!is_null($mediaFolderId) && $data['mediafolder_id'] != $mediaFolderId) {
                throw new NotFoundException("The item '$id' is register in another gallery.");
            }
            self::$instances[$id] = new self();
            foreach ($data as $key => $value)
            {
                self::$instances[$id]->$key = $value;
            }
            self::$instances[$id]->id = $id;
            self::$instances[$id]->modified = false;
        }
        return self::$instances[$id];
    }
}
?>