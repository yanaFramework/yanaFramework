<?php
/**
 * Self-diagnosis
 *
 * Runs a self-test on the current installation of the Yana Framework
 * and displays the results.
 * It informs about found problems and provides hints on how to solve them.
 *
 * {@translation
 *
 *   de:  Selbstdiagnose
 *
 *        Dieses Plugin führt eine automatische Selbstdiagnose des Programms durch,
 *        weist auf Fehler hin und liefert Hinweise zu deren Beseitigung.
 * }
 *
 * @type       config
 * @priority   highest
 * @author     Thomas Meyer
 * @license    http://www.gnu.org/licenses/gpl.txt
 *
 * @package    yana
 * @subpackage plugins
 */

namespace Plugins\Diagnostics;

/**
 * Self-diagnosis plug-in
 *
 * Runs a self-test on the current installation of the Yana Framework
 * and displays the results.
 * It informs about found problems and provides hints on how to solve them.
 *
 * @package    yana
 * @subpackage plugins
 */
class DiagnosticsPlugin extends \Yana\Plugins\AbstractPlugin
{

    /**
     * run system diagnostics
     *
     * This function takes the boolean argument: 'details'.
     * If set, additional information is provided.
     *
     * You may provide the boolean argument: 'xml'.
     * If set, it outputs the XML source of the report.
     *
     * @menu        group: setup
     * @type        config
     * @template    null
     * @user        group: admin, level: 100
     * @access      public
     * @param       bool  $details  include details (yes/no)
     * @param       bool  $xml      export as XML (yes/no)
     */
    public function test($details = false, $xml = false)
    {
        $helper = new \Plugins\Diagnostics\DiagnosticsHelper($this->_getApplication());
        if ($xml) {

            header('Content-type: text/xml');
            header('Charset: utf-8');
            print $helper->runDiagnosticsReportAsXml();

        } else {

            print $helper->runDiagnosticsReportAsHtml($details);
        }

        exit(0);
    }

}

?>
