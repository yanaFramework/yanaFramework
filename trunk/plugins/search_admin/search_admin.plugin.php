<?php
/**
 * Search Engine - Setup
 *
 * Setup options for the search engine.
 *
 * {@translation
 *
 *    de:  Stichwortsuche - Setup
 *
 *         Setup-Optionen für die Stichwortsuche.
 *
 * , fr:   Recherche - Setup
 *
 * }
 *
 * @author     Thomas Meyer
 * @type       config
 * @group      search
 * @extends    search
 * @license    http://www.gnu.org/licenses/gpl.txt
 *
 * @package    yana
 * @subpackage plugins
 */

/**
 * Search plugin setup
 *
 * This implements basic setup functions for
 * the search plugin
 *
 * @access     public
 * @package    yana
 * @subpackage plugins
 */
class plugin_search_admin extends StdClass implements IsPlugin
{

    /**
     * Default event handler
     *
     * returns bool(true) on success and bool(false) on error
     *
     * @access  public
     * @param   string  $event  name of the called event in lower-case
     * @param   array   $ARGS   array of arguments passed to the function
     * @return  bool
     * @ignore
     */
    public function _default($event, array $ARGS)
    {
        return true;
    }

    /**
     * Create form
     *
     * returns bool(true) on success and bool(false) on error
     *
     * @type        config
     * @user        group: admin, level: 50
     * @template    SEARCH_SETUP
     * @menu        group: setup
     *
     * @access      public
     * @return      bool
     */
    public function search_setup()
    {
        return true;
    }
}

?>