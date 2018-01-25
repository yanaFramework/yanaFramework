<?php
/**
 * RSS-to-HTML Factory
 *
 * Reads an RSS feed and creates an HTML page.
 *
 * {@translation
 *
 *    de: RSS-to-HTML Factory
 *
 *    Liest einen RSS-Feed und erzeugt daraus eine HTML-Seite.
 * }
 *
 * @author     Thomas Meyer
 * @type       read
 * @priority   high
 * @group      rss_factory
 * @license    http://www.gnu.org/licenses/gpl.txt
 *
 * @package    yana
 * @subpackage plugins
 */

namespace Plugins\Rss;

/**
 * RSS to HTML factory plugin
 *
 * creates HTML from RSS files
 *
 * @package    yana
 * @subpackage plugins
 */
class RssPlugin extends \Yana\Plugins\AbstractPlugin
{

    /**#@+
     * @ignore
     * @access  private
     */

    /** @var array  */ private $_rss;

    /** "file" should be a valid path to an existing XML-File
     * @var string  */ private $file;

    /**  maximum number of entries to show
     * @var int     */ private $max;

    /** @var string */ private $currentTag = "";
    /** @var int    */ private $currentEntry = -1;

    /**#@-*/

    /**
     * Constructor
     *
     * @access  public
     * @ignore
     */
    public function __construct()
    {
        $YANA = $this->_getApplication();
        if (isset($YANA)) {
            $this->file = $YANA->getVar("PROFILE.RSS.FILE");
            $this->max  = $YANA->getVar("PROFILE.RSS.MAX");
            settype($this->file, "string");
            settype($this->max, "integer");

            if (!$this->max > 0) {
                $this->max  = 5;
            }
            if (!$this->file > 0) {
                $this->file = 'plugins/rss/test.rss';
            }
        }
    }

    /**
     * Transform RSS/XML to HTML
     *
     * This creates the output.
     *
     * @type        read
     * @template    RSS_NEWS
     * @menu        group: start
     *
     * @access  public
     * @return  bool
     */
    public function get_news()
    {
        /* this function expects no arguments */

        $YANA = $this->_getApplication();
        $parser = xml_parser_create();
        xml_set_element_handler($parser, array(&$this, "_startElement"), array(&$this, "_endElement"));
        xml_set_character_data_handler($parser, array(&$this, "_characterData"));

        if (!($fp = @fopen($this->file, "r"))) {
            $message = "Could not open XML input. Check if '".print_r($this->file).
                "' is a correct path to a valid XML file!";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            \Yana\Log\LogManager::getLogger()->addLog($message, $level);
            return false;

        } else {

            while ($data = fread($fp, 4096))
            {
                if (!xml_parse($parser, $data, feof($fp))) {
                    $message = "XML error: ".
                    xml_error_string(xml_get_error_code($parser)) .
                    " in file '" . $this->file .
                    "' at line " . xml_get_current_line_number($parser);
                    \Yana\Log\LogManager::getLogger()->addLog($message, $level);
                    return false;
                }
            }
            xml_parser_free($parser);
            $YANA->setVar("RSS", array_slice($this->_rss, 0, $this->max));
            $YANA->setVar("FILE", $this->file);

            /* Microsummaries */
            $this->_getMicrosummary()->publishSummary(__CLASS__);

            if (count($this->_rss) > 0) {
                $latest = array_shift($this->_rss);
                $this->_getMicrosummary()->setText(__CLASS__, 'RSS latest: '.$latest['TITLE'].
                    ' ('.$latest['PUBDATE'].')');
            }

            return true;

        } // end if
    }

    /**
     * _startElement
     *
     * @access  private
     * @param   int     $parser parser
     * @param   string  $name   name
     * @param   array   $attrs  attributes
     * @ignore
     */
    public function _startElement($parser, $name, $attrs)
    {
        if (preg_match("/title|pubDate|description|link|author|category|comments/i", $name)) {
            $this->currentTag = mb_strtoupper($name);
        }
        if (mb_strtoupper($name) == "ITEM" && $this->currentEntry == -1) {
            $this->currentEntry = 0;
        }
    }

    /**
     * _endElement
     *
     * @access  private
     * @param   int     $parser parser
     * @param   string  $name   name
     * @return  bool
     * @ignore
     */
    public function _endElement($parser, $name)
    {
        if (mb_strtoupper($name) == "ITEM") {
            $this->currentEntry++;
        }
    }

    /**
     * _characterData
     *
     * @access  private
     * @param   int     $parser parser
     * @param   string  $data   data
     * @return  bool
     * @ignore
     */
    public function _characterData($parser, $data)
    {

        if ( trim($data) && $this->currentEntry > -1) {
            if ($this->currentTag != "LINK") {
                $data = preg_replace("/'/u", "\\'", $data);
            }
            settype($this->_rss[$this->currentEntry][$this->currentTag], "string");
            $this->_rss[$this->currentEntry][$this->currentTag] .= $data;
        }
    }

}

?>