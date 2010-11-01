<?php
/**
 * Visitor-Counter
 *
 * This template-plugin displays the number of unique visitors/users on your application page.
 *
 * {@translation
 *
 *   de: Besucherzähler
 *
 *       Dieses Plugin zeigt die Anzahl der Besucher/Nutzer auf der Webseite Ihrer Anwendung an.
 *
 * }
 *
 * @author     Thomas Meyer
 * @type       primary
 * @license    http://www.gnu.org/licenses/gpl.txt
 *
 * @package    yana
 * @subpackage plugins
 */

/**
 * adds a counter as template engine extension
 *
 * @access     public
 * @package    yana
 * @subpackage plugins
 */
class plugin_counter extends StdClass implements IsPlugin
{
    /**
     * @access  private
     * @static
     * @var     Counter
     */
    private static $counter = null;

    /**
     * @access  private
     * @static
     * @var     string
     */
    private static $id = "default";

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
    public function catchAll($event, array $ARGS)
    {
        self::$id = __CLASS__ . '\\' . Yana::getId();
        if (!Counter::exists(self::$id)) {
            Counter::create(self::$id);
        }
        self::$counter = new Counter(self::$id);
        self::$counter->getNextValue();
        if (isset($GLOBALS['YANA'])) {
            $GLOBALS['YANA']->view->setFunction(YANA_TPL_FUNCTION, 'visitorCount', array(__CLASS__, 'visitorCount'));
        }
        return true;
    }

    /**
     * <<smarty function>> visitorCount
     *
     * Outputs the number of unique visitors
     *
     * @static
     * @access  public
     * @param   array   $params   params
     * @param   Smarty  &$smarty  smarty object
     * @return  string
     */
    public static function visitorCount(array $params, Smarty &$smarty)
    {
        $count = self::$counter->getCurrentValue();
        $text = Language::getInstance()->getVar("VISITOR_COUNT");

        return $text . ' <span style="font-weight: bold;">' . $count . '</span>';
    }

    /**
     * Create graphical visitor counter
     *
     * Note: This function ends the program.
     *
     * parameters taken:
     *
     * <ul>
     * <li> string target  </li>
     * </ul>
     *
     * @type        primary
     * @template    null
     *
     * @access      public
     * @return      bool
     */
    public function graphic_counter ()
    {
        global $YANA;
        if (isset($YANA)) {
            $background = $YANA->plugins->{'counter:/images/background.file'};
            $blank = $YANA->plugins->{'counter:/images/blank.file'};
            $dir = $YANA->plugins->{'counter:/images'};
            $imageDir = $dir->getPath();
            $imageExt = $dir->getFilter();

            $this->_default(__FUNCTION__, array());
            $count = self::$counter->getCurrentValue();

            $myImage = new Image($background->getPath());
            $myImageValues = Image::getSize($background->getPath());
            $previousImageWidths = 0;

            if ($blank->exists()) {
                $imageValues = Image::getSize($blank->getPath());
                $previousImageWidths += $imageValues[0];
            }

            for ($i = 0; $i < mb_strlen("$count"); $i++)
            {

                $filename = $imageDir.mb_substr("$count", mb_strlen("$count")-$i-1, 1).$imageExt;
                $imageValues = Image::getSize($filename);
                $previousImageWidths += $imageValues[0];
                if (($myImageValues[0]-$previousImageWidths) < 0 || ($myImageValues[1]-$imageValues[1]) < 0) {
                    unset($myImage);
                    $myImage = new Image();
                    $myImage->resize(325, 55);
                    $myImage->drawString("[ERROR] image size exceeds maximum",               5, 0);
                    $myImage->drawString("or value to big to be displayed",                  5, 10);
                    $myImage->drawString("1) check size of counter \"".
                            mb_substr("$count", mb_strlen("$count")-$i-1, 1).".png\"",       5, 20);
                    $myImage->drawString("2) check dimensions of file \"hintergrund.png\"",  5, 30);
                    $myImage->drawString("   are big enough for the content.",               5, 40);
                    $myImage->outputToScreen();
                    die;
                };
                $image = new Image($filename, 'png');
                $destX = ($myImageValues[0]-$previousImageWidths);
                $destY = (int) floor(($myImageValues[1]-$imageValues[1])/2);
                $myImage->copyRegion($image, 0, 0, null, null, $destX, $destY);
                unset($image, $destX, $destY);

            }

            $myImage->outputToScreen();
        }
        exit(0);
    }
}

?>
