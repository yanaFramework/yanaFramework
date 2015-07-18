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

namespace Plugins\GuestbookAdmin;

/**
 * guestbook setup
 *
 * This implements basic setup functions for the guestbook plugin.
 *
 * @package    yana
 * @subpackage plugins
 */
class GuestbookAdminPlugin extends \Yana\Plugins\AbstractPlugin
{

    /**
     * Default event handler
     *
     * @access  public
     * @param   string  $event  name of the called event in lower-case
     * @param   array   $ARGS   array of arguments passed to the function
     * @return  bool
     * @ignore
     */
    public function catchAll($event, array $ARGS)
    {
        return true;
    }

    /**
     * Guestbook Setup (Administrator)
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
     */
    public function get_admin()
    {
        /* this function expects no arguments */

        $YANA = $this->_getApplication();
        $YANA->setVar("DESCRIPTION", $YANA->getLanguage()->getVar("DESCR_ADMIN"));
        $configFile = $YANA->getResource('system:/config');
        $YANA->setVar("WRITEABLE", $configFile->isWriteable());
    }

    /**
     * Guestbook Setup (Moderator)
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
     */
    public function get_mod()
    {
        /* this function expects no arguments */

        $YANA = $this->_getApplication();
        $YANA->setVar("DESCRIPTION", $YANA->getLanguage()->getVar("DESCR_MOD"));
        $configFile = $YANA->getResource('system:/config');
        $YANA->setVar("WRITEABLE", $configFile->isWriteable());
    }

}

?>