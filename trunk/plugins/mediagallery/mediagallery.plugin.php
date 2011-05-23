<?php
/**
 * MediaGallery
 * Views uploaded files in the media database as galleries.
 * 
 * @type       primary
 * @group      media
 * @extends    mediadb
 * @priority   2
 * @author     Thomas Meyer
 * @url        http://www.yanaframework.net
 * @package    yana
 * @subpackage plugins
 */

/**
 * <<plugin>> class "plugin_mediagallery"
 *
 * This plugin is constructed as follows:
 * <pre>
 * AbstractDataContainer #-------- IsDataAdapter
 *   ^           ^                     ^
 *   |           |                     |
 *   |           |                     |
 * Gallery #---GalleryItem        DatabaseAdapter
 *                                     ^
 *                                     |
 *                           GalleryDatabaseAdapter
 * </pre>
 *
 * Gallery and GalleryItem are <<entities>> (Data containers).
 * That means they are "passive" and don't do anything on themselves.
 *
 * The AbstractDataContainer class is an <<observer>>.
 * It keeps track of the state of a data object by intercepting any changes applied to it.
 *
 * The DataAdapter is an <<interface>>. We use it to inject a dependency into the AbstractDataContainer.
 * The GalleryDatabaseAdapter is the actual implementation.
 * The AbstractDataContainer uses the DataAdapter to read and write data from and to an arbitrary data source.
 *
 * When you wish to read the contents of a gallery the following happens:
 * <code>
 * $dataSource = new GalleryDatbaseAdapter();
 * Gallery::registerDataAdapter($dataSource);
 * // You inject the datasource into the Gallery class.
 * $gallery = Gallery::getInstance($id);
 * // The factory method calls the datasource to retrieve the gallery.
 * // It then uses the data to create a new Gallery instance.
 * $gallery->name = $newName;
 * // The AbstractDataContainer intercepts your attempt.
 * // It now sets the new state of the Gallery to "modified".
 * print $gallery->isModified(); // returns TRUE
 * unset($gallery);
 * // On destruction, the AbstractDataContainer checks if the instance was modified.
 * // If so, it calls the DataAdapter to write back the changes.
 * </code>
 *
 * @package     yana
 * @subpackage  plugins
 */
class plugin_mediagallery extends StdClass implements IsPlugin
{

    /**
     * Connection to data source (API)
     *
     * @access  private
     * @static
     * @var     DBStream  Database-API with Query-Builder (also works with text-files)
     */
    private static $database = null;

    /**
     * Returns the database connection
     *
     * @access  protected
     * @static
     * @return  DBStream
     * @ignore
     */
    protected static function getDatabase()
    {
        if (!isset(self::$database)) {
            self::$database = Yana::connect("mediagallery");
        }
        return self::$database;
    }

    /**
     * Default event handler.
     *
     * @param   string  $event  name of the called event in lower-case
     * @param   array   $ARGS   array of arguments passed to the function
     * @return  bool
     * @ignore
     */
    public function catchAll($event, array $ARGS)
    {
        return true;
    }

    /**
     * mediagallery
     * 
     * @type     primary
     * @menu     group: start
     * @user     level: 1
     * @user     group: gallery
     * @language mediagallery
     * @template templates/gallery.html.tpl
     * @access   public
     */
    public function mediagallery()
    {
        $builder = new FormBuilder('mediagallery');
        $builder->setId('mediagallery');
        $builder->setWhere(
            array(
                array('user_created', '=', YanaUser::getUserName()), 'or', array('public', '=', true)
            )
        );
        $gallery = $builder->__invoke();
        Yana::getInstance()->setVar('gallery', $gallery);
    }

}

?>