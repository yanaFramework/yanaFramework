<?php
/**
 * Guestbook - Setup
 *
 * When active, the menu entry "Guestbook - Setup" is available, which provides settings for the guestbook.
 *
 * {@translation
 *
 *    de:  Gästebuch - Setup
 *
 *         Wenn dieses Plugin aktiviert ist, wird dem Hauptmenü der Eintrag "Gästebuch - Setup" hinzugefügt,
 *         welcher Einstellungen für das Gästebuch bereitstellt.
 *
 *  , fr:  Livre d'or - Setup
 * }
 *
 * @author     Thomas Meyer
 * @type       config
 * @group      guestbook
 * @extends    guestbook
 * @license    http://www.gnu.org/licenses/gpl.txt
 *
 * @package    yana
 * @subpackage plugins
 */

/**
 * guestbook setup
 *
 * This implements basic setup functions for the guestbook plugin.
 *
 * @access     public
 * @package    yana
 * @subpackage plugins
 */
class plugin_guestbook_admin extends StdClass implements IsPlugin
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
    public function catchAll($event, array $ARGS)
    {
        global $YANA;
        /* do nothing */
        return true;
    }

    /**
     * Guestbook Setup (Administrator)
     *
     * this function does not expect any arguments
     *
     * @type        security
     * @user        group: admin, level: 100
     * @template    templates/config.html.tpl
     * @language    admin
     * @language    guestbook
     * @style       ../../skins/default/styles/config.css
     * @script      ../../skins/default/styles/dynamic-styles.js
     * @menu        group: setup
     * @safemode    true
     * @language    admin
     *
     * @access      public
     * @return      bool
     */
    public function get_admin()
    {
        /* this function expects no arguments */

        global $YANA;
        $YANA->setVar("DESCRIPTION", $YANA->language->getVar("DESCR_ADMIN"));
        $configFile = $YANA->getResource('system:/config');
        $YANA->setVar("WRITEABLE", $configFile->isWriteable());
        return true;
    }

    /**
     * Guestbook Setup (Moderator)
     *
     * this function does not expect any arguments
     *
     * @type        security
     * @user        group: admin, level: 60
     * @template    templates/config.html.tpl
     * @language    admin
     * @language    guestbook
     * @style       ../../skins/default/styles/config.css
     * @script      ../../skins/default/styles/dynamic-styles.js
     * @menu        group: setup
     * @safemode    false
     * @language    admin
     *
     * @access      public
     * @return      bool
     */
    public function get_mod()
    {
        /* this function expects no arguments */

        global $YANA;
        $YANA->setVar("DESCRIPTION", $YANA->language->getVar("DESCR_MOD"));
        $configFile = $YANA->getResource('system:/config');
        $YANA->setVar("WRITEABLE", $configFile->isWriteable());
        return true;
    }

}

?>
