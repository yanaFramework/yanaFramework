<?php
/**
 * Setup additional datasources
 *
 * The SDK is an assistant to help you create new plugins.<br />
 * <br />
 * <b>Attention!</b> The plugins is intended to be used by developers and
 * should not be activated on a public web server.
 *
 * {@translation
 *
 *    de:  Weitere Datenquellen einrichten
 *
 *         Ein Plugin zur Konfiguration weiterer Datenbankverbindungen für Anwendungen,
 *         welche mit mehr als einer Datenquelle arbeiten.
 * }
 * 
 * @type        config
 * @author      Thomas Meyer
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @package     yana
 * @subpackage  plugins
 */

namespace Plugins\Datasources;

/**
 * <<plugin>> Datasources
 *
 * @package     yana
 * @subpackage  plugins
 */
class DatasourcesPlugin extends \Yana\Plugins\AbstractPlugin
{

    /**
     * Connection to data source (API)
     *
     * @var  \Yana\Db\IsConnection  Database-API with Query-Builder (also works with text-files)
     */
    private $_database = null;

    /**
     * Return database connection.
     *
     * @return  \Yana\Db\IsConnection
     */
    protected function _getDatabase()
    {
        if (!isset($this->_database)) {
            $this->_database = $this->_connectToDatabase('datasources');
        }
        return $this->_database;
    }

    /**
     * Get form definition.
     *
     * @return  \Yana\Forms\Facade
     */
    protected function _getDatasourcesForm()
    {
        $builder = $this->_getApplication()->buildForm('datasources');
        return $builder->__invoke();
    }

    /**
     * Provide edit-form.
     *
     * @type      read
     * @user      group: datasources
     * @user      group: admin, level: 100
     * @menu      group: setup
     * @template  templates/datasources.html.tpl
     * @language  Datasources
     */
    public function datasources()
    {
        // @todo add your code here
    }

    /**
     * Process search query.
     *
     * @type      read
     * @user      group: datasources
     * @user      group: admin, level: 100
     * @template  templates/datasources.html.tpl
     * @language  Datasources
     */
    public function searchDatasources()
    {
        // @todo add your code here
    }

    /**
     * Save changes made in edit-form.
     *
     * @type       write
     * @user       group: datasources, role: moderator
     * @user       group: admin, level: 100
     * @template   MESSAGE
     * @language   Datasources
     * @onsuccess  goto: datasources
     * @onerror    goto: datasources
     * @return     bool
     */
    public function datasources_update()
    {
        $form = $this->_getDatasourcesForm();
        $worker = new \Yana\Forms\Worker($this->_getDatabase(), $form);
        return $worker->update();
    }

    /**
     * Delete an entry.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @type       write
     * @user       group: datasources, role: moderator
     * @user       group: admin, level: 100
     * @template   MESSAGE
     * @language   Datasources
     * @onsuccess  goto: datasources
     * @onerror    goto: datasources
     * @param      array  $selected_entries  array of entries to delete
     * @return     bool
     */
    public function datasources_delete(array $selected_entries)
    {
        $form = $this->_getDatasourcesForm();
        $worker = new \Yana\Forms\Worker($this->_getDatabase(), $form);
        return $worker->delete($selected_entries);
    }

    /**
     * Write new entry to database.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @type       write
     * @user       group: datasources, role: moderator
     * @user       group: admin, level: 100
     * @template   MESSAGE
     * @language   Datasources
     * @onsuccess  goto: datasources
     * @onerror    goto: datasources
     * @return     bool
     */
    public function datasources_insert()
    {
        $form = $this->_getDatasourcesForm();
        $worker = new \Yana\Forms\Worker($this->_getDatabase(), $form);
        return $worker->create();
    }

    /**
     * Export data as CSV.
     *
     * Creates the CSV, sets it up as download and exits the program.
     *
     * @type       read
     * @user       group: datasources
     * @user       group: admin, level: 1
     * @template   NULL
     *
     * @param   int   $col     column seperator index
     * @param   int   $row     row seperator index
     * @param   bool  $header  add column names as first line (yes/no)
     * @param   int   $text    text seperator index
     */
    public function datasources_export(int $col = 1, int $row = 1, bool $header = true, int $text = 1)
    {
        if (!\headers_sent()) {
            header("Content-Disposition: attachment; filename=export.csv");
            header("Content-type: text/csv");
        }
        $this->_getApplication()->getLanguage()->loadTranslations('Datasources');
        $form = $this->_getDatasourcesForm();
        $worker = new \Yana\Forms\Worker($this->_getDatabase(), $form);
        $csv = $worker->export($col, $row, $header, $text);
        print $csv;
        exit(0);
    }

}

?>