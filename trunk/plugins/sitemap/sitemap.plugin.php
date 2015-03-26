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
class plugin_sitemap extends StdClass implements \Yana\IsPlugin
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
        \Yana\Application::getInstance()->getView()->setFunction('sitemap', array(__CLASS__, 'createSitemap'));
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

        $html = "<ul>\n";
        $dir = $YANA->getPlugins()->getPluginDir();
        $pluginMenu = \Yana\Plugins\Menu::getInstance();
        $formatter = new \Yana\Views\Helpers\Formatters\UrlFormatter();

        /* @var $entry PluginMenuEntry */
        foreach ($pluginMenu->getMenuEntries('start') as $action => $entry)
        {
            $image = $entry->getIcon();
            $title = $entry->getTitle();

            if (empty($image)) {
                $html .= "\t<li>";
            } elseif (is_file($image)) {
                $html .= "\t<li style=\"list-style-image: url('{$image}')\">";
            } elseif (is_file($dir . $image)) {
                $html .= "\t<li style=\"list-style-image: url('{$dir}{$image}')\">";
            } else {
                $level = \Yana\Log\TypeEnumeration::WARNING;
                \Yana\Log\LogManager::getLogger()->addLog("Sitemap icon not found: '{$image}'.", $level);
                $html .= "\t<li>";
            }
            $html .= '<a href="' . $formatter("action={$action}", false, false) . '">' . $title . "</a></li>\n";
        } // end foreach

        $html .= "</ul>\n";
        $resultWithLanguageTokensReplaced = $YANA->getLanguage()->replaceToken($html);
        return $resultWithLanguageTokensReplaced;
    }

}

?>