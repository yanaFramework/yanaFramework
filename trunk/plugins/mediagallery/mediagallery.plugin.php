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
            self::$database = Yana::connect("mediadb");
        }
        return self::$database;
    }

    /**
     * Default event handler
     *
     * The default event handler catches all events, whatever they might be.
     * If you don't need it, you may deactive it by adding an @ignore to the annotations below.
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
     * @template templates/gallery.html.tpl
     * @style    templates/css/jquery-ui-1.8.custom.css
     * @style    templates/gallery.css
     * @script   templates/jquery-1.4.2.min.js
     * @script   templates/jquery-ui-1.8.custom.min.js
     * @script   templates/jquery.galleriffic.js
     * @access   public
     */
    public function mediagallery()
    {
        /** @ignore */
        include_once 'gallerydatabaseadapter.class.php';
        /** @ignore */
        include_once 'gallery.class.php';
        /** @ignore */
        include_once 'galleryview.class.php';
        $db = self::getDatabase();
        Gallery::registerDataAdapter(new GalleryDatabaseAdapter($db, 'mediafolder'));
        GalleryItem::registerDataAdapter(new GalleryDatabaseAdapter($db, 'media'));
        $yana = Yana::getInstance();
        $view = new GalleryView();
        $yana->setVar('gallery', $view);
    }
}
?>
