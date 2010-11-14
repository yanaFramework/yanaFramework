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
     * returns bool(true) on success and bool(false) on error
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
     * Show Sitemap
     *
     * this function does not expect any arguments
     *
     * @type        primary
     * @template    sitemap
     * @menu        group: start
     *
     * @access      public
     * @return      bool
     */
    public function sitemap ()
    {
        /* this function expects no arguments */
        global $YANA;
        $YANA->view->setFunction(YANA_TPL_FUNCTION, 'sitemap', array(__CLASS__, 'createSitemap'));
        return true;
    }

    /**
     * <<smarty function>> sitemap
     *
     * @access  public
     * @static
     * @param   array   $params   parameters
     * @param   Smarty  &$smarty  smarty
     * @return  string
     *
     * @ignore
     */
    public static function createSitemap($params, &$smarty)
    {
        global $YANA;

        $result = "<ul>\n";
        $dir = $YANA->plugins->getPluginDir();
        $pluginMenu = PluginMenu::getInstance();

        foreach ($pluginMenu->getMenuEntries('start') as $action => $entry)
        {
            $image = $entry[PluginAnnotationEnumeration::IMAGE];
            $title = $entry[PluginAnnotationEnumeration::TITLE];

            if (empty($image)) {
                $result .= "\t<li>";
            } elseif (is_file($image)) {
                $result .= "\t<li style=\"list-style-image: url('${image}')\">";
            } elseif (is_file($dir . $image)) {
                $result .= "\t<li style=\"list-style-image: url('${dir}${image}')\">";
            } else {
                Log::report("Sitemap icon not found: '${image}'.", E_USER_WARNING);
                $result .= "\t<li>";
            }
            $result .= "<a href=" . SmartUtility::href("action=${action}") . ">${title}</a></li>\n";
        } // end foreach

        $result .= "</ul>\n";
        $result = $YANA->language->replaceToken($result);
        return $result;
    }

}

?>