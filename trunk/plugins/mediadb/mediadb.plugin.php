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

namespace Plugins\MediaDb;

/**
 * <<plugin>> class "plugin_mediadb"
 *
 * @package     yana
 * @subpackage  plugins
 */
class MediaDbPlugin extends \Yana\Plugins\AbstractPlugin
{

    /**
     * Get form definition.
     *
     * @return  \Yana\Forms\Facade
     */
    private function _getMediaForm()
    {
        $form = $this->_getMediafolderForm()->__invoke();
        return $form->getForm('media');
    }

    /**
     * Get form definition.
     *
     * @return  \Yana\Forms\IsBuilder
     */
    private function _getMediafolderForm()
    {
        $builder = $this->_getApplication()->buildForm('mediadb', 'mediafolder');
        $where = array(
            array('user_created', '=', $this->_getSession()->getCurrentUserName()),
            'or',
            array('public', '=', true)
        );
        return $builder->setWhere($where);
    }

    /**
     * Get form worker.
     *
     * @return  \Yana\Forms\Worker
     */
    private function _getMediafolderFormWorker()
    {
        $form = $this->_getMediafolderForm()->__invoke();
        $worker = new \Yana\Forms\Worker($this->_connectToDatabase('mediadb'), $form);
        return $worker;
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
     * @return     bool
     */
    public function mediadb_edit_media()
    {
        $form = $this->_getMediaForm();
        $worker = new \Yana\Forms\Worker($this->_connectToDatabase('mediadb'), $form);
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
     * @param      array  $selected_entries  array of entries to delete
     * @return     bool
     */
    public function mediadb_delete_media(array $selected_entries)
    {
        $form = $this->_getMediaForm();
        $worker = new \Yana\Forms\Worker($this->_connectToDatabase('mediadb'), $form);
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
     * @return     bool
     */
    public function mediadb_new_media()
    {
        $form = $this->_getMediaForm();
        $worker = new \Yana\Forms\Worker($this->_connectToDatabase('mediadb'), $form);
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
     */
    public function mediadb()
    {
        $viewHelper = new \Plugins\MediaDb\ViewHelper($this->_getMediafolderForm());

        $view = $this->_getApplication()->getView();
        try {
            $view->setFunction('folderList', $viewHelper);

        } catch (\Yana\Views\Managers\RegistrationException $e) {
            $view->unsetFunction('folderList');
            $view->setFunction('folderList', $viewHelper);
            unset($e);
        }
    }

    /**
     * process search query
     *
     * @type      read
     * @user      group: mediadb
     * @user      group: admin, level: 1
     * @template  templates/mediafolder.html.tpl
     * @language  mediadb
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
     * @return     bool
     */
    public function mediadb_update_mediafolder()
    {
        $worker = $this->_getMediafolderFormWorker();
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
     * @param      array  $selected_entries  array of entries to delete
     * @return     bool
     */
    public function mediadb_delete_mediafolder(array $selected_entries)
    {
        $worker = $this->_getMediafolderFormWorker();
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
     * @return     bool
     */
    public function mediadb_insert_mediafolder()
    {
        $worker = $this->_getMediafolderFormWorker();
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
     * @return     string
     */
    public function mediadb_export_mediafolder()
    {
        $csv = $this->_getMediafolderFormWorker()->export();
        return $csv;
    }
}
?>
