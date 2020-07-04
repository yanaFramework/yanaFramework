<?php
/**
 * Self-diagnosis
 *
 * @package    yana
 * @subpackage plugins
 */

namespace Plugins\Diagnostics;

/**
 * Self-diagnosis implementation.
 *
 * Runs a self-test on the current installation of the Yana Framework
 * and displays the results.
 * It informs about found problems and provides hints on how to solve them.
 *
 * @package    yana
 * @subpackage plugins
 */
class DiagnosticsHelper extends \Yana\Core\StdObject
{

    /**
     * @var \Yana\Application 
     */
    private $_application = null;

    /**
     * <<construct>> Initialize application.
     *
     * @param  \Yana\Application  $application  used to produce report and resolve dependencies
     */
    public function __construct(\Yana\Application $application)
    {
        $this->_application = $application;
    }

    /**
     * Call application to produce system diagnostics report.
     *
     * @return  \Yana\Report\IsReport
     */
    protected function _getReport(): \Yana\Report\IsReport
    {
        return $this->_application->getReport();
    }

    /**
     * Call application to resolve and return dependency to XSL file required to produce HTML.
     *
     * @return  \Yana\Files\IsReadable
     */
    protected function _getXslFile(): \Yana\Files\IsReadable
    {
        return $this->_application->getPlugins()->{'diagnostics:/report.file'};
    }

    /**
     * Runs system diagnostics on the application and returns the result as XML string.
     *
     * @return  string
     */
    public function runDiagnosticsReportAsXml(): string
    {
        return $this->_getReport()->asXML();
    }

    /**
     * Runs system diagnostics on the application and returns the result as XML string.
     *
     * @param   bool  $withDetails  whether or not the report should include full details or just a summary
     * @return  string
     */
    public function runDiagnosticsReportAsHtml(bool $withDetails): string
    {
        /* create objects */
        assert(!isset($doc), 'Cannot redeclare var $doc');
        $doc = new \DOMDocument();
        assert(!isset($xsl), 'Cannot redeclare var $xsl');
        $xsl = new \XSLTProcessor();
        $xslFile = $this->_getXslFile();

        /* load stylesheet */
        $doc->load($xslFile->getPath());
        $xsl->importStyleSheet($doc);
        unset($xslFile);

        /* add parameters */
        $xsl->setParameter("", "details", $withDetails ? "1" : "0");
        $urlFormatter = new \Yana\Views\Helpers\Formatters\UrlFormatter();
        $detailsUrl = $urlFormatter('action=test&details=' . !$withDetails, true);
        $xsl->setParameter("", "urlChooseDetails", $detailsUrl);
        $xmlUrl = $urlFormatter('action=test&xml=1', true);
        $xsl->setParameter("", "urlChooseXml", $xmlUrl);

        /* transform and output report */
        $doc->loadXML($this->_getReport()->asXML());
        return $xsl->transformToXML($doc);
    }

}

?>
