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

/**
 * Self-diagnosis plug-in
 *
 * Runs a self-test on the current installation of the Yana Framework
 * and displays the results.
 * It informs about found problems and provides hints on how to solve them.
 *
 * @access     public
 * @package    yana
 * @subpackage plugins
 */
class plugin_diagnostics extends StdClass implements \Yana\IsPlugin
{

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
        global $YANA;

        /* get report */
        assert('!isset($report); // Cannot redeclare var $report');
        $report = $YANA->getReport();
        assert('$report instanceof \Yana\Report\IsReport; // unexpected return type - instance of Report\IsReport expected');

        if ($xml) {

            header('Content-type: text/xml');
            header('Charset: utf-8');
            print($report->asXML());

        } else {

            /* create objects */
            assert('!isset($doc); // Cannot redeclare var $doc');
            $doc = new \DOMDocument();
            assert('!isset($xsl); // Cannot redeclare var $xsl');
            $xsl = new \XSLTProcessor();
            $xslFile = $YANA->getPlugins()->{'diagnostics:/report.file'};

            /* load stylesheet */
            $doc->load($xslFile->getPath());
            $xsl->importStyleSheet($doc);
            unset($xslFile);

            /* add parameters */
            if ($details) {
                $xsl->setParameter("", "details", "1");
            } else {
                $xsl->setParameter("", "details", "0");
            }
            $urlFormatter = new \Yana\Views\Helpers\Formatters\UrlFormatter();
            $detailsUrl = $urlFormatter('action=' . __FUNCTION__ . "&details=" . !$details, true);
            $xsl->setParameter("", "urlChooseDetails", $detailsUrl);
            $xmlUrl = $urlFormatter('action=' . __FUNCTION__ . "&xml=1", true);
            $xsl->setParameter("", "urlChooseXml", $xmlUrl);

            /* transform and output report */
            $doc->loadXML($report->asXML());
            print($xsl->transformToXML($doc));
        }

        exit(0);
    }

}

?>
