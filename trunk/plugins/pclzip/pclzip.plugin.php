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

namespace Plugins\PclZip;

/**
 * <<plugin>> Loads the PCL-ZIP library.
 *
 * This is a demo plugin that shows how to integrate libraries into
 * the structure of the plugin system.
 *
 * @package     yana
 * @subpackage  plugins
 */
class PclZipPlugin extends \Yana\Plugins\AbstractPlugin
{

    /**
     * Loads library.
     *
     * @return  bool
     * @param   string  $event  name of the called event in lower-case
     * @param   array   $ARGS   array of arguments passed to the function
     */
    public function catchAll($event, array $ARGS)
    {
        /**
         * @ignore
         */
        include_once 'pclzip.lib.php';
        return true;
    }

}

?>