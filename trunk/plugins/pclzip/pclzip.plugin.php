<?php
/**
 * PCL-ZIP
 *
 * PclZip is a library that allows you to manage a Zip archive.
 * Full documentation can be found here: http://www.phpconcept.net/pclzip
 *
 * @author     Vincent Blavet
 * @type       library
 * @license    GNU LGPL
 * @link       http://www.phpconcept.net
 * @version    2.6
 *
 * @package    yana
 * @subpackage plugins
 */

/**
 * <<plugin>> class "plugin_katalog"
 *
 * @access      public
 * @package     yana
 * @subpackage  plugins
 */
class plugin_pclzip extends StdClass implements IsPlugin
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
     */
    public function _default($event, array $ARGS)
    {
        /**
         * @ignore
         */
        include_once 'pclzip.lib.php';
    }
}
?>