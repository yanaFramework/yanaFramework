<?php
/**
 * Media Database
 *
 * Allows a user to upload, media files to the database and group them in
 * public or private folders or galleries.
 *
 * {@translation
 *
 *    de: Mediendatenbank
 *
 *        Erlaubt es Nutzern Mediendateien in die Datenbank einzustellen und
 *        in öffentlichen oder privaten Ordnern oder Galerien zu gruppieren.
 * }
 *
 * @type       primary
 * @group      media
 * @priority   2
 * @author     Thomas Meyer
 * @url        http://www.yanaframework.net
 * @package    yana
 * @subpackage plugins
 */

/**
 * <<plugin>> class "plugin_mediadb"
 *
 * @package     yana
 * @subpackage  plugins
 */
class plugin_mediadb extends StdClass implements \Yana\IsPlugin
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
     * get where clause as array
     *
     * @access  private
     * @static
     * @return  array
     */
    private static function _getWhere()
    {
        return array(
            array('user_created', '=', \Yana\User::getUserName()),
            'or',
            array('public', '=', true)
        );
    }

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
            self::$database = \Yana\Application::connect("mediadb");
        }
        return self::$database;
    }

    /**
     * get form definition
     *
     * @access  protected
     * @static
     * @return  FormFacade
     */
    protected static function getMediaForm()
    {
        $form = self::getMediafolderForm();
        return $form->getForm('media');
    }

    /**
     * get form definition
     *
     * @access  protected
     * @static
     * @return  FormFacade
     */
    protected static function getMediafolderForm()
    {
        $builder = new \Yana\Forms\Builder('mediadb');
        return $builder->setId('mediafolder')->setWhere(self::_getWhere())->__invoke();
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
     * save changes made in edit-form
     *
     * @type       write
     * @user       group: mediadb, role: moderator
     * @user       group: admin, level: 75
     * @template   MESSAGE
     * @language   mediadb
     * @onsuccess  goto: mediadb
     * @onerror    goto: mediadb
     * @access     public
     * @return     bool
     */
    public function mediadb_edit_media()
    {
        $form = self::getMediaForm();
        $worker = new \Yana\Forms\Worker(self::getDatabase(), $form);
        return $worker->update();
    }

    /**
     * delete an entry
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @type       write
     * @user       group: mediadb, role: moderator
     * @user       group: admin, level: 75
     * @template   MESSAGE
     * @language   mediadb
     * @onsuccess  goto: mediadb
     * @onerror    goto: mediadb
     * @access     public
     * @param      array  $selected_entries  array of entries to delete
     * @return     bool
     */
    public function mediadb_delete_media(array $selected_entries)
    {
        $form = self::getMediaForm();
        $worker = new \Yana\Forms\Worker(self::getDatabase(), $form);
        return $worker->delete($selected_entries);
    }

    /**
     * write new entry to database
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @type       write
     * @user       group: mediadb, role: moderator
     * @user       group: admin, level: 30
     * @template   MESSAGE
     * @language   mediadb
     * @onsuccess  goto: mediadb
     * @onerror    goto: mediadb
     * @access     public
     * @return     bool
     */
    public function mediadb_new_media()
    {
        $form = self::getMediaForm();
        $worker = new \Yana\Forms\Worker(self::getDatabase(), $form);
        return $worker->create();
    }

    /**
     * provide edit-form
     *
     * @type      read
     * @user      group: mediadb
     * @user      group: admin, level: 1
     * @menu      group: start
     * @template  templates/mediafolder.html.tpl
     * @language  mediadb
     * @access    public
     */
    public function mediadb()
    {
        \Yana\Application::getInstance()->getView()->setFunction('folderList', 'plugin_mediadb::smartyFolderList');
    }

    /**
     * <<smarty function>> Create a folder list from a data table.
     *
     * @access    public
     * @static
     * @return  string
     */
    public static function smartyFolderList()
    {
        return (string) self::getMediafolderForm();
    }

    /**
     * process search query
     *
     * @type      read
     * @user      group: mediadb
     * @user      group: admin, level: 1
     * @template  templates/mediafolder.html.tpl
     * @language  mediadb
     * @access    public
     */
    public function mediadb_search_mediafolder()
    {
        return $this->mediadb();
    }

    /**
     * save changes made in edit-form
     *
     * @type       write
     * @user       group: mediadb, role: moderator
     * @user       group: admin, level: 75
     * @template   MESSAGE
     * @language   mediadb
     * @onsuccess  goto: mediadb
     * @onerror    goto: mediadb
     * @access     public
     * @return     bool
     */
    public function mediadb_update_mediafolder()
    {
        $form = self::getMediafolderForm();
        $worker = new \Yana\Forms\Worker(self::getDatabase(), $form);
        return $worker->update();
    }

    /**
     * delete an entry
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @type       write
     * @user       group: mediadb, role: moderator
     * @user       group: admin, level: 75
     * @template   MESSAGE
     * @language   mediadb
     * @onsuccess  goto: mediadb
     * @onerror    goto: mediadb
     * @access     public
     * @param      array  $selected_entries  array of entries to delete
     * @return     bool
     */
    public function mediadb_delete_mediafolder(array $selected_entries)
    {
        $form = self::getMediafolderForm();
        $worker = new \Yana\Forms\Worker(self::getDatabase(), $form);
        return $worker->delete($selected_entries);
    }

    /**
     * write new entry to database
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @type       write
     * @user       group: mediadb, role: moderator
     * @user       group: admin, level: 30
     * @template   MESSAGE
     * @language   mediadb
     * @onsuccess  goto: mediadb
     * @onerror    goto: mediadb
     * @access     public
     * @return     bool
     */
    public function mediadb_insert_mediafolder()
    {
        $form = self::getMediafolderForm();
        $worker = new \Yana\Forms\Worker(self::getDatabase(), $form);
        return $worker->create();
    }

    /**
     * write new entry to database
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @type       read
     * @user       group: mediadb, role: moderator
     * @user       group: admin, level: 75
     * @template   NULL
     * @access     public
     * @return     string
     */
    public function mediadb_export_mediafolder()
    {
        $query = $this->getMediafolderForm()->getQuery(); // @todo Fixme!
        return $query->toCSV();
    }
}
?>
