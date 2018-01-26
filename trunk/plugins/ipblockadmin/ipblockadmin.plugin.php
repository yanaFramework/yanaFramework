<?php
/**
 * IP-Filter - Setup
 *
 * Allows to edit the list of blocked IPs.
 *
 * {@translation
 *
 *    de: IP-Filter - Setup
 *
 *        Erlaubt es die Liste der blockierten IPs zu editieren.
 * }
 *
 * @author     Thomas Meyer
 * @type       config
 * @extends    ipblock
 * @license    http://www.gnu.org/licenses/gpl.txt
 *
 * @package    yana
 * @subpackage plugins
 */

namespace Plugins\IpBlockAdmin;

/**
 * IP block setup
 *
 * This implements basic setup functions for the IP block plugin.
 *
 * @package    yana
 * @subpackage plugins
 */
class IpBlockAdminPlugin extends \Yana\Plugins\AbstractPlugin
{

    /** @var \Yana\Files\Block */ private $_whitelist = null;
    /** @var \Yana\Files\Block */ private $_blacklist = null;

    /**
     * get whitelist contents
     *
     * @return  \Yana\Files\Block
     */
    private function _getWhitelist()
    {
        if (!isset($this->_whitelist)) {
            $this->_whitelist = $this->_getPluginsFacade()->{"ipblock:/dir/whitelist.block"};
            if ($this->_whitelist->exists()) {
                $this->_whitelist->read();
            }
        }
        return $this->_whitelist;
    }

    /**
     * get blacklist contents
     *
     * @return  \Yana\Files\Block
     */
    private function _getBlacklist()
    {
        if (!isset($this->_blacklist)) {
            $this->_blacklist = $this->_getPluginsFacade()->{"ipblock:/dir/blacklist.block"};
            if ($this->_blacklist->exists()) {
                $this->_blacklist->read();
            }
        }
        return $this->_blacklist;
    }

    /**
     * event handler
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    BLOCK_TEMPLATE
     * @menu        group: setup
     * @safemode    true
     *
     * @access      public
     */
    public function get_root_block()
    {
        $this->_getBlock();
    }

    /**
     * event handler
     *
     * @type        config
     * @user        group: admin, level: 60
     * @template    BLOCK_TEMPLATE
     * @menu        group: setup
     * @safemode    false
     *
     * @access      public
     */
    public function get_block()
    {
        $this->_getBlock();
    }

    /**
     * set template vars for presentation
     */
    private function _getBlock()
    {
        $YANA = $this->_getApplication();
        $whitelist = $this->_getWhitelist();
        $blacklist = $this->_getBlacklist();
        if ($whitelist->exists()) {
            $YANA->setVar("WHITELIST", $whitelist->getContent());
        }
        if ($blacklist->exists()) {
            $YANA->setVar("BLACKLIST", $blacklist->getContent());
        }
    }

    /**
     * event handler
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    message
     * @onsuccess   goto: index
     * @onerror     goto: get_root_block
     * @safemode    true
     *
     * @access      public
     * @param       string  $blacklist  list of blacklisted IPs
     * @param       string  $whitelist  list of whitelisted IPs
     * @return      bool
     */
    public function set_root_block($blacklist = "", $whitelist = "")
    {
        return $this->_set($blacklist, $whitelist, $this->_getApplication()->getDefault('profile'));
    }

    /**
     * event handler
     *
     * @type        config
     * @user        group: admin, level: 60
     * @template    message
     * @onsuccess   goto: index
     * @onerror     goto: get_block
     * @safemode    false
     *
     * @access      public
     * @param       string  $id         profile id
     * @param       string  $blacklist  list of blacklisted IPs
     * @param       string  $whitelist  list of whitelisted IPs
     * @return      bool
     */
    public function set_block($id, $blacklist = "", $whitelist = "")
    {
        return $this->_set($blacklist, $whitelist, $this->_getApplication()->getProfileId());
    }

    /**
     * set black- and whitelist contents
     *
     * This function saves the contents of black- and whitelist,
     * as given in $ARGS, to the corresponding files.
     * It creates them, if they don't exist.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @access  private
     * @param   string  $blacklist    list of blacklisted IPs
     * @param   string  $whitelist    list of whitelisted IPs
     * @param   string  $profileName  to save to
     * @return  bool
     * @ignore
     */
    private function _set($blacklist, $whitelist, $profileName)
    {
        /* @var $baseDirectory \Yana\Files\IsDir */
        $baseDirectory = $this->_getApplication()->getPlugins()->{"ipblock:/dir"};
        $whitelistFile = new \Yana\Files\Block($baseDirectory->getPath() . $profileName . '.whitelist');
        $blacklistFile = new \Yana\Files\Block($baseDirectory->getPath() . $profileName . '.blacklist');
        unset($baseDirectory);

        if (!$whitelistFile->exists()) {
            $whitelistFile->create();
        } else {
            $whitelistFile->read();
        }

        if (!$blacklistFile->exists()) {
            $blacklistFile->create();
        } else {
            $blacklistFile->read();
        }

        try {
            $blacklistFile->setContent($blacklist)->write();
            $whitelistFile->setContent($whitelist)->write();

        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

}

?>