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

namespace Plugins\MediaGallery;

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
class MediaGalleryPlugin extends \Yana\Plugins\AbstractPlugin
{

    /**
     * mediagallery
     * 
     * @type     primary
     * @menu     group: start
     * @language mediagallery
     * @template templates/gallery.html.tpl
     * @access   public
     */
    public function mediagallery()
    {
        $builder = $this->_getApplication()->buildForm('mediagallery');
        $builder->setEntries(25);
        $builder->setLayout(6);
        $where = array(
            array('user_created', '=', $this->_getSession()->getCurrentUserName()),
            'or',
            array('public', '=', true)
        );
        $builder->setWhere($where);
        $gallery = $builder->__invoke();
        $this->_getApplication()->setVar('gallery', $gallery);
    }

}

?>