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

namespace Plugins\RssAdmin;

/**
 * RSS to HTML factory setup
 *
 * This implements basic setup functions for
 * the RSS to HTML factory plugin
 *
 * @package    yana
 * @subpackage plugins
 */
class RssAdminPlugin extends \Yana\Plugins\AbstractPlugin
{

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
        $YANA = $this->_getApplication();
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
     * @onerror     goto: get_rss_to_html_config, text: Yana\Core\Exceptions\Files\NotFoundException
     *
     * @access      public
     * @param       array  $ARGS  ignored
     * @return      bool
     */
    public function set_rss_to_html_config(array $ARGS)
    {
        $YANA = $this->_getApplication();
        if ($ARGS['rss/file'] && !file_exists($ARGS['rss/file'])) {
            return false;
        }
        if (empty($ARGS['id'])) {
            return $YANA->execute('set_config_default', $ARGS);
        } else {
            return $YANA->execute('set_config_profile', $ARGS);
        }
    }

}

?>