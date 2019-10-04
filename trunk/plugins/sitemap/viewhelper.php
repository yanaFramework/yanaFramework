<?php
/**
 * Sitemap
 *
 * @package    yana
 * @subpackage plugins
 */

namespace Plugins\Sitemap;

/**
 * View helper.
 *
 * Generates sitemap menu.
 *
 * @package    yana
 * @subpackage plugins
 */
class ViewHelper extends \Yana\Core\AbstractObject implements \Yana\Views\Helpers\IsFunction
{

    /**
     * @var  string
     */
    private $_pluginDir = "";

    /**
     * @var \Yana\Translations\Facade
     */
    private $_translations = null;

    /**
     * @var \Yana\Plugins\Menus\IsMenu
     */
    private $_menu = null;

    /**
     * Initialize the view helper
     *
     * @param  string                      $pluginDir     required to determine the URL for plugin-icons that are used in the menu
     * @param  \Yana\Plugins\Menus\IsMenu  $menu          application menu
     * @param  \Yana\Translations\Facade   $translations  provides translations for text parts of the menu
     */
    public function __construct($pluginDir, \Yana\Plugins\Menus\IsMenu $menu, \Yana\Translations\Facade $translations)
    {
        assert('is_string($pluginDir); // Invalid argument type $pluginDir: string expected.');
        $this->_pluginDir = (string) $pluginDir;
        $this->_menu = $menu;
        $this->_translations = $translations;
    }

    /**
     * Get path to plugin base-directory.
     *
     * @return  string
     */
    protected function _getPluginDir()
    {
        return $this->_pluginDir;
    }

    /**
     * Looks up translations and returns them.
     *
     * @param   string  $text  to be translated
     * @return  \Yana\Translations\Facade
     */
    protected function _translateText($text)
    {
        return $this->_translations->replaceToken($text);
    }

    /**
     * Returns application menu.
     *
     * @return  \Yana\Plugins\Menus\IsMenu
     */
    protected function _getMenu()
    {
        return $this->_menu;
    }

    /**
     * <<smarty function>> sitemap
     *
     * @param   array                      $params  any list of arguments
     * @param   \Smarty_Internal_Template  $smarty  reference to currently rendered template
     * @return  string
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty)
    {
        unset($params, $smarty); // none required

        $html = "<ul>\n";
        $dir = $this->_getPluginDir();
        $formatter = new \Yana\Views\Helpers\Formatters\UrlFormatter();

        $pluginMenu = $this->_getMenu();

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
        $resultWithLanguageTokensReplaced = $this->_translateText($html);
        return $resultWithLanguageTokensReplaced;
    }

}

?>