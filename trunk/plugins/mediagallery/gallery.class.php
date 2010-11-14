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

/** @ignore */
require_once 'galleryitem.class.php';

/**
 * Gallery
 *
 * @package     yana
 * @subpackage  plugins
 */
class Gallery extends DataContainerAbstract
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
     * get data adapter
     *
     * @access  protected
     * @static
     * @return  IsDataAdapter
     * @throws  NotImplementedException  if no data adapter has been registered
     */
    protected static function getDataAdapter()
    {
        if (!isset(self::$dataAdapter)) {
            throw new NotImplementedException("No data adapter registered.");
        }
        return self::$dataAdapter;
    }

    /**
     * register a new data adapter
     *
     * The DataAdapter is an interface.
     * We use it to inject a dependency into the AbstractDataContainer.
     * The AbstractDataContainer uses the DataAdapter to read and write data
     * from and to an arbitrary data source.
     *
     * @access  public
     * @static
     * @param   IsDataAdapter  $adapter  data adapter implementation
     */
    public static function registerDataAdapter(IsDataAdapter $adapter)
    {
        self::$dataAdapter = $adapter;
    }

    /**
     * get gallery item
     *
     * @access  public
     * @param   string  $id  item id
     * @return  GalleryItem
     * @throws  NotFoundException
     */
    public function getItem($id)
    {
        return GalleryItem::getInstance($id, $this->id);
    }

    /**
     * return array of ids in use
     *
     * @access  public
     * @return  array
     */
    public static function getIds()
    {
        return self::getDataAdapter()->getIds();
    }
}
?>