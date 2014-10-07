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
class plugin_ipblock extends StdClass implements \Yana\IsPlugin
{

    /**
     * Check user's IP.
     *
     * Checks if the user's IP is black-listed and if so, produces an error message and returns false.
     *
     * returns bool(true) on success and bool(false) on error
     *
     * @access  public
     * @return  bool
     * @param   string  $event  name of the called event in lower-case
     * @param   array   $ARGS   array of arguments passed to the function
     * @throws \Yana\Core\Exceptions\Security\PermissionDeniedException  when the client's IP is black-listed
     */
    public function catchAll($event, array $ARGS)
    {
        global $YANA;
        $plugins = $YANA->getPlugins();
        $whitelist = $plugins->{"ipblock:/dir/whitelist.block"};
        /** @var $whitelist \Yana\Files\Block */
        $blacklist = $plugins->{"ipblock:/dir/blacklist.block"};
        /** @var $blacklist \Yana\Files\Block */
        $dir = $plugins->{"ipblock:/dir"};

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

        $remoteAddress = $YANA->getVar('REMOTE_ADDR');

        if ($blacklist->isBlocked($remoteAddress) && !($whitelist->exists() && $whitelist->isBlocked())) {
            throw new \Yana\Core\Exceptions\Security\PermissionDeniedException();
        }

        return true; // access granted
    }

}

?>