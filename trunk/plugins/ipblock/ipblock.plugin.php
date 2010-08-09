<?php
/**
 * IP-Filter
 *
 * Allows to block single or groups of IPs via black- and whitelists. Blocked users don't have access to the system.
 *
 * {@translation
 *
 *    de: IP-Filter
 *
 *        Dieses Plugin erlaubt es, einzelne oder Gruppen von IPs über Black- und Whitelisten zu blockieren.
 *        Nutzer von blockierten IPs haben keinen Zugriff mehr auf das System.
 * }
 *
 * @author     Thomas Meyer
 * @type       write
 * @priority   highest
 * @license    http://www.gnu.org/licenses/gpl.txt
 *
 * @package    yana
 * @subpackage plugins
 */

/**
 * IP-Blocker utility
 *
 * Security plugin that automatically blocks
 * banned IPs via configurable black- and whitelists.
 *
 * @access     public
 * @package    yana
 * @subpackage plugins
 */
class plugin_ipblock extends StdClass implements IsPlugin
{
    /**
     * check user
     *
     * Checks if the user's IP is black-listed and if so, produces an error message and returns false.
     *
     * returns bool(true) on success and bool(false) on error
     *
     * @access  public
     * @return  bool
     * @param   string  $event  name of the called event in lower-case
     * @param   array   $ARGS   array of arguments passed to the function
     * @ignore
     */
    public function _default($event, array $ARGS)
    {
        global $YANA;
        $whitelist = $YANA->plugins->{"ipblock:/dir/whitelist.blockfile"};
        $blacklist = $YANA->plugins->{"ipblock:/dir/blacklist.blockfile"};
        $dir = $YANA->plugins->{"ipblock:/dir"};

        if (!$dir->exists()) {
            $dir->create(0777);
        }

        if (!$blacklist->exists()) {
            return true;
        }

        if ($whitelist->exists()) {
            $whitelist->read();
        }
        if ($blacklist->exists()) {
            $blacklist->read();
        }

        if ($blacklist->isBlocked() && !($whitelist->exists() && $whitelist->isBlocked())) {
            new PermissionDeniedError();
            $YANA->exitTo(null); // die
            return false;
        }

        return true; // access granted
    }

}

?>