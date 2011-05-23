<?php
/**
 * RSS-to-HTML Factory - Setup
 *
 * Allows to select the RSS feed from which to create HTML.
 *
 * {@translation
 *
 *    de: RSS-to-HTML Factory - Setup
 *
 *        Dieses Plugin erlaubt die Auswahl des RSS-Feeds, aus welchem eine HTML-Seite erzeugt werden soll.
 * }
 *
 * @author     Thomas Meyer
 * @type       config
 * @group      rss_factory
 * @extends    rss
 * @license    http://www.gnu.org/licenses/gpl.txt
 *
 * @package    yana
 * @subpackage plugins
 */

/**
 * RSS to HTML factory setup
 *
 * This implements basic setup functions for
 * the RSS to HTML factory plugin
 *
 * @access     public
 * @package    yana
 * @subpackage plugins
 */
class plugin_rss_admin extends StdClass implements IsPlugin
{

    /**
     * Default event handler
     *
     * @access  public
     * @return  bool
     * @param   string  $event  name of the called event in lower-case
     * @param   array   $ARGS   array of arguments passed to the function
     * @ignore
     */
    public function catchAll($event, array $ARGS)
    {
        return true;
    }

    /**
     * Show configuration panel.
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    RSS_CONFIG_TEMPLATE
     * @menu        group: setup
     *
     * @access      public
     * @param       string  $id  profile id
     */
    public function get_rss_to_html_config($id = "")
    {
        global $YANA;
        $YANA->setVar("ID", $id);
    }

    /**
     * Save configuration data to profile.
     *
     * parameters taken:
     *
     * <ul>
     * <li> string rss/file </li>
     * </ul>
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    MESSAGE
     * @onsuccess   goto: get_rss_to_html_config
     * @onerror     goto: get_rss_to_html_config, text: FileNotFoundError
     *
     * @access      public
     * @param       array  $ARGS  ignored
     * @return      bool
     */
    public function set_rss_to_html_config(array $ARGS)
    {
        global $YANA;
        if ($ARGS['rss/file'] && !file_exists($ARGS['rss/file'])) {
            return false;
        }
        if (empty($ARGS['id'])) {
            return $YANA->callAction('set_config_default', $ARGS);
        } else {
            return $YANA->callAction('set_config_profile', $ARGS);
        }
    }

}

?>