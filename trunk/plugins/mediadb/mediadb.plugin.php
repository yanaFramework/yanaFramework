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
class plugin_mediadb extends StdClass implements IsPlugin
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
     * Form definition media
     *
     * @access  private
     * @static
     * @var     DDLDefaultForm
     */
    private static $mediaForm = null;

    /**
     * Form definition mediafolder
     *
     * @access  private
     * @static
     * @var     DDLDefaultForm
     */
    private static $mediafolderForm = null;

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
            array('user_created', '=', YanaUser::getUserName()),
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
            self::$database = Yana::connect("mediadb");
        }
        return self::$database;
    }

    /**
     * get form definition
     *
     * @access  protected
     * @static
     * @return  DDLDefaultForm
     */
    protected static function getMediaForm()
    {
        if (!isset(self::$mediaForm)) {
            $folderForm = self::getMediafolderForm();
            self::$mediaForm = $folderForm->getForm("media");
            $query = self::$mediaForm->getQuery();
            $query->setWhere(self::_getWhere());
        }
        return self::$mediaForm;
    }

    /**
     * get form definition
     *
     * @access  protected
     * @static
     * @return  DDLDefaultForm
     */
    protected static function getMediafolderForm()
    {
        if (!isset(self::$mediafolderForm)) {
            $database = self::getDatabase();
            self::$mediafolderForm = $database->getSchema()->getForm("mediafolder");
            $query = self::$mediafolderForm->getQuery();
            $query->setWhere(self::_getWhere());
        }
        return self::$mediafolderForm;
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
     */
    function catchAll($event, array $ARGS)
    {
        $event = strtolower("$event");

        /* global variables */
        global $YANA;

        /* do something */

        /* NOTE: event handlers should always return a boolean value.
         * Return "true" on success or "false" on error.
         */
        return true;
    }

    /* NOTE:
     * All member-functions stated here act as action handlers (event handlers) and may be called
     * directly in a browser by typing: index.php?action=function_name
     *
     * You may exclude a single function from this behaviour by either making it non-public, or by
     * adding @ignore to the function description.
     */

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
        $updatedEntries = $this->getMediaForm()->getUpdateValues();

        if (empty($updatedEntries)) {
            throw new InvalidInputWarning(); // no data has been provided
        }

        $database = self::getDatabase();
        foreach ($updatedEntries as $id => $entry)
        {
            $id = mb_strtolower($id);

            // before doing anything, check if entry exists
            if (!$database->exists("media.$id")) {
                throw new InvalidInputWarning(); // error - no such entry
            }

            // update the row
            if (!$database->update("media.$id", $entry)) {
                // error - unable to perform update - possibly readonly
                return false;
            }
        } // end foreach
        return $database->write(); // commit changes
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
        // check if user forgot to mark at least 1 row
        if (empty($selected_entries)) {
            throw new InvalidInputWarning();
        }
        $database = self::getDatabase();
        // remove entry from database
        foreach ($selected_entries as $id)
        {
            if (!$database->remove("media.$id")) {
                throw new InvalidInputWarning(); // entry does not exist
            }
        } // end foreach
        return $database->commit(); // commit changes
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
        $newEntry = $this->getMediaForm()->getInsertValues();

        if (empty($newEntry)) {
            throw new InvalidInputWarning(); // no data has been provided
        }

        $database = self::getDatabase();
        // insert new entry into table
        if (!$database->insert("media.*", $newEntry)) {
            throw new InvalidInputWarning();
        }
        return $database->commit(); // commit changes
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
        Yana::getInstance()->getView()->setFunction('folderList', 'plugin_mediadb::smartyFolderList');
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
        $form = self::getMediafolderForm();
        return $form->toString();
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
     * @return    bool
     */
    public function mediadb_search_mediafolder()
    {
        $form = $this->getMediafolderForm();
        $having = $form->getSearchValuesAsWhereClause();
        if (!is_null($having)) {
            $form->getQuery()->setHaving($having);
        }
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
    public function mediadb_update_mediafolder()
    {
        $updatedEntries = $this->getMediafolderForm()->getUpdateValues();

        if (empty($updatedEntries)) {
            throw new InvalidInputWarning(); // no data has been provided
        }

        $database = self::getDatabase();
        foreach ($updatedEntries as $id => $entry)
        {
            $id = mb_strtolower($id);

            // before doing anything, check if entry exists
            if (!$database->exists("mediafolder.$id")) {
                throw new InvalidInputWarning(); // error - no such entry
            }

            // update the row
            if (!$database->update("mediafolder.$id", $entry)) {
                // error - unable to perform update - possibly readonly
                return false;
            }
        } // end foreach
        return $database->write(); // commit changes
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
        // check if user forgot to mark at least 1 row
        if (empty($selected_entries)) {
            throw new InvalidInputWarning();
        }
        $database = self::getDatabase();
        // remove entry from database
        foreach ($selected_entries as $id)
        {
            if (!$database->remove("mediafolder.$id")) {
                throw new InvalidInputWarning(); // entry does not exist
            }
        } // end foreach
        return $database->commit(); // commit changes
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
        $newEntry = $this->getMediafolderForm()->getInsertValues();

        if (empty($newEntry)) {
            throw new InvalidInputWarning(); // no data has been provided
        }

        $database = self::getDatabase();
        // insert new entry into table
        if (!$database->insert("mediafolder.*", $newEntry)) {
            throw new InvalidInputWarning();
        }
        return $database->commit(); // commit changes
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
        $query = $this->getMediafolderForm()->getQuery();
        return $query->toCSV();
    }
}
?>
