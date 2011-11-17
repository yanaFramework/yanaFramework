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

/**
 * IP block setup
 *
 * This implements basic setup functions for the IP block plugin.
 *
 * @access     public
 * @package    yana
 * @subpackage plugins
 */
class plugin_ipblock_admin extends StdClass implements IsPlugin
{

    /**#@+
     * @ignore
     * @access  private
     * @static
     */

    /** @var BlockFile */ private static $_whitelist = null;
    /** @var BlockFile */ private static $_blacklist = null;

    /**#@-*/

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
     * get whitelist contents
     *
     * @access  private
     * @static
     * @return  BlockFile
     */
    private static function _getWhitelist()
    {
        if (!isset(self::$_whitelist)) {
            $pluginManager = \Yana\Plugins\Manager::getInstance();
            self::$_whitelist = $pluginManager->ipblock_admin->getContent("ipblock:/dir/whitelist.blockfile");
        }
        return self::$_whitelist;
    }

    /**
     * get blacklist contents
     *
     * @access  private
     * @static
     * @return  BlockFile
     */
    private static function _getBlacklist()
    {
        if (!isset(self::$_blacklist)) {
            $pluginManager = \Yana\Plugins\Manager::getInstance();
            self::$_blacklist = $pluginManager->ipblock_admin->getContent("ipblock:/dir/blacklist.blockfile");
        }
        return self::$_blacklist;
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
     *
     * @access      private
     */
    private function _getBlock()
    {
        global $YANA;
        $whitelist = self::_getWhitelist();
        $blacklist = self::_getBlacklist();
        if ($whitelist->exists()) {
            $YANA->setVar("WHITELIST", $whitelist->get());
        }
        if ($blacklist->exists()) {
            $YANA->setVar("BLACKLIST", $blacklist->get());
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
        return $this->_set($blacklist, $whitelist);
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
        return $this->_set($blacklist, $whitelist);
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
     * @param   string  $blacklist  list of blacklisted IPs
     * @param   string  $whitelist  list of whitelisted IPs
     * @return  bool
     * @ignore
     */
    private function _set($blacklist, $whitelist)
    {
        $whitelistFile = self::_getWhitelist();
        $blacklistFile = self::_getBlacklist();

        if (!$whitelistFile->exists()) {
            $whitelistFile = new BlockFile(dirname($whitelistFile->getPath()) . '/' . Yana::getId() . '.whitelist');
            $whitelistFile->create();
        } else {
            $whitelistFile->read();
        }

        if (!$blacklistFile->exists()) {
            $blacklistFile = new BlockFile(dirname($blacklistFile->getPath()) . '/' . Yana::getId() . '.blacklist');
            $blacklistFile->create();
        } else {
            $blacklistFile->read();
        }

        if (!$blacklistFile->setContent($blacklist) || !$blacklistFile->write()) {
            return false;
        }
        if (!$whitelistFile->setContent($whitelist) || !$whitelistFile->write()) {
            return false;
        }
        return true;
    }

}

?>