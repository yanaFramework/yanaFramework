<?php
/**
 * Sitemap
 *
 * Create a sitemap of your application, that can be used as and index page.
 *
 * {@translation
 *
 *    de: Sitemap
 *
 *        Erzeugt eine Sitemap Ihrer Programme, welche als Einstiegsseite verwendet werden kann.
 * }
 *
 * @author     Thomas Meyer
 * @type       primary
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @active     always
 *
 * @package    yana
 * @subpackage plugins
 */

/**
 * Configration menu
 *
 * This plugin provides the basic administration menu and
 * interfaces to create custom profile settings.
 *
 * @access     public
 * @package    yana
 * @subpackage plugins
 */
class plugin_sitemap extends StdClass implements IsPlugin
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
     * Show Sitemap.
     *
     * @type        primary
     * @template    sitemap
     * @menu        group: start
     *
     * @access      public
     */
    public function sitemap()
    {
        Yana::getInstance()->getView()->setFunction('sitemap', array(__CLASS__, 'createSitemap'));
    }

    /**
     * <<smarty function>> sitemap
     *
     * @access  public
     * @static
     * @return  string
     *
     * @ignore
     */
    public static function createSitemap()
    {
        global $YANA;

        $result = "<ul>\n";
        $dir = $YANA->getPlugins()->getPluginDir();
        $pluginMenu = \Yana\Plugins\Menu::getInstance();
        $formatter = new \Yana\Views\Helpers\Formatters\UrlFormatter();

        /* @var $entry PluginMenuEntry */
        foreach ($pluginMenu->getMenuEntries('start') as $action => $entry)
        {
            $image = $entry->getIcon();
            $title = $entry->getTitle();

            if (empty($image)) {
                $result .= "\t<li>";
            } elseif (is_file($image)) {
                $result .= "\t<li style=\"list-style-image: url('${image}')\">";
            } elseif (is_file($dir . $image)) {
                $result .= "\t<li style=\"list-style-image: url('${dir}${image}')\">";
            } else {
                \Yana\Log\LogManager::getLogger()->addLog("Sitemap icon not found: '${image}'.", E_USER_WARNING);
                $result .= "\t<li>";
            }
            $result .= '<a href="' . $formatter("action=${action}", false, false) . '">' . $title . "</a></li>\n";
        } // end foreach

        $result .= "</ul>\n";
        $result = $YANA->getLanguage()->replaceToken($result);
        return $result;
    }

}

?>