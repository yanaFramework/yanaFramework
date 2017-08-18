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

namespace Plugins\Sitemap;

/**
 * Configration menu
 *
 * This plugin provides the basic administration menu and
 * interfaces to create custom profile settings.
 *
 * @package    yana
 * @subpackage plugins
 */
class SitemapPlugin extends \Yana\Plugins\AbstractPlugin
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
        $application = $this->_getApplication();
        $viewHelper = new \Plugins\Sitemap\ViewHelper(
            $application->getPlugins()->getPluginDir(),
            $application->buildApplicationMenu(),
            $application->getLanguage()
        );

        $this->_getApplication()->getView()->setFunction('sitemap', array($viewHelper, '__invoke'));
    }

}

?>