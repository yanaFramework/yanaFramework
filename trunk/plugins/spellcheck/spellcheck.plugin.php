<?php
/**
 * Spellcheck
 *
 * <p>This is a spellchecker for TinyMCE based on an implementation by Moxiecode.
 * Using the default settings it queries a webservice to look up the correct spelling
 * of a word.</p>
 *
 * <p>Enabling this plug-in should provide spell-checking for HTML input.</p>
 *
 * {@translation
 *
 *    de: Rechtschreibprüfung
 *
 *        <p>Dieses Plugin basiert auf einer Implementierung von Moxiecode stellt eine
 *        Rechtschreibprüfung für TinyMCE zur Verfügung.
 *        In der Grundeinstellung wird ein Webservice abgefragt, um die korrekte Schreibweise eines
 *        Wortes zu erhalten.</p>
 *
 *        <p>Das Aktivieren dieses Plugins sollte Rechtschreibprüfung für HTML-Eingaben bereitstellen.</p>
 * }
 *
 * @author     Moxiecode
 * @type       default
 * @license    http://www.gnu.org/licenses/lgpl.txt
 *
 * @package    yana
 * @subpackage plugins
 */

/**
 * TinyMCE spellchecker interface
 *
 * @access     public
 * @package    yana
 * @subpackage plugins
 */
class plugin_spellcheck extends StdClass implements IsPlugin
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
     *
     * @ignore
     */
    public function _default($event, array $ARGS)
    {
        return true;
    }

    /**
     * AJAX-spellchecking function
     *
     * This is a pure AJAX function. Note: the parameters are taken from the AJAX-call.
     * It expected the arguments to be encoded as a JSON-string.
     *
     * It outputs the results as a response object. This object is JSON-encoded.
     *
     * @group     default
     * @template  NULL
     * @access    public
     * @return    array
     */
    public function spellcheck()
    {
        include_once "config.php";

        // Set RPC response headers
        header('Content-Type: text/plain');
        header('Content-Encoding: UTF-8');
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        // get request object from input stream
        $input = json_decode(file_get_contents('php://input'), true);

        // Configuration is invalid
        if (empty($config['general.engine'])) {
            $language = Language::getInstance();
            exit(json_encode(array(
                "result" => null,
                "id" => null,
                "error" => array(
                    "errstr" => $language->getVar('500', E_USER_ERROR, 'No spellchecking-engine selected.'),
                    "errfile" => "",
                    "errline" => null,
                    "errcontext" => "",
                    "level" => "FATAL"
                )
            )));
        }
        $spellchecker = new $config['general.engine']($config);
        $result = call_user_func_array(array($spellchecker, $input['method']), $input['params']);

        // Return JSON encoded string
        exit(json_encode(array(
            "id" => $input['id'],
            "result" => $result,
            "error" => null
        )));
    }
}

?>