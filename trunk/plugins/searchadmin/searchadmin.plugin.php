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

namespace Plugins\SearchAdmin;

/**
 * Search plugin setup
 *
 * This implements basic setup functions for
 * the search plugin
 *
 * @package    yana
 * @subpackage plugins
 */
class SearchAdminPlugin extends \Yana\Plugins\AbstractPlugin
{

    /**
     * Show setup form.
     *
     * @type        config
     * @user        group: admin, level: 50
     * @template    SEARCH_SETUP
     * @menu        group: setup
     */
    public function search_setup()
    {
        // Just views a template. No business logic required.
    }

}

?>