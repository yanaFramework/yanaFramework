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

namespace Plugins\Counter;

/**
 * adds a counter as template engine extension
 *
 * @package    yana
 * @subpackage plugins
 */
class CounterPlugin extends \Yana\Plugins\AbstractPlugin
{
    /**
     * @var  \Yana\Db\FileDb\Counter
     */
    private static $_counter = null;

    /**
     * @var  string
     */
    private static $_id = "default";

    /**
     * Calculates and displays visitor count.
     *
     * @return  bool
     * @param   string  $event  name of the called event in lower-case
     * @param   array   $ARGS   array of arguments passed to the function
     */
    public function catchAll($event, array $ARGS)
    {
        $YANA = $this->_getApplication();

        self::$_id = __CLASS__ . '\\' . $YANA->getProfileId();
        if (!\Yana\Db\FileDb\Counter::exists(self::$_id)) {
            \Yana\Db\FileDb\Counter::create(self::$_id);
        }
        self::$_counter = new \Yana\Db\FileDb\Counter(self::$_id);
        $visitorCount = self::$_counter->getNextValue();
        $label = $YANA->getLanguage()->getVar("VISITOR_COUNT");
        $viewHelper = new \Plugins\Counter\ViewHelper($visitorCount, $label);
        $view = $YANA->getView();

        // The default plugin registers a dummy function of the same name. Need to get rid of that first
        $view->unsetFunction('visitorCount'); // This will do nothing if (for whatever reason) the function doesn't exist
        // Register our function instead
        $view->setFunction('visitorCount', $viewHelper);
        return true;
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
     */
    public function graphic_counter()
    {
        $pluginManager = $this->_getPluginsFacade();
        $background = $pluginManager->{'counter:/images/background.file'};
        $blank = $pluginManager->{'counter:/images/blank.file'};
        $dir = $pluginManager->{'counter:/images'};
        $imageDir = $dir->getPath();
        $imageExt = $dir->getFilter();

        $this->_default(__FUNCTION__, array());
        $count = self::$_counter->getCurrentValue();

        $myImage = new \Yana\Media\Image($background->getPath());
        $myImageValues = \Yana\Media\Image::getSize($background->getPath());
        $previousImageWidths = 0;

        if ($blank->exists()) {
            $imageValues = \Yana\Media\Image::getSize($blank->getPath());
            $previousImageWidths += $imageValues[0];
        }

        for ($i = 0; $i < mb_strlen("$count"); $i++)
        {

            $filename = $imageDir . mb_substr("$count", mb_strlen("$count") - $i - 1, 1) . $imageExt;
            $imageValues = \Yana\Media\Image::getSize($filename);
            $previousImageWidths += $imageValues[0];
            if (($myImageValues[0] - $previousImageWidths) < 0 || ($myImageValues[1] - $imageValues[1]) < 0) {
                unset($myImage);
                $myImage = new \Yana\Media\Image();
                $myImage->resize(325, 55);
                $myImage->drawString("[ERROR] image size exceeds maximum", 5, 0);
                $myImage->drawString("or value to big to be displayed", 5, 10);
                $myImage->drawString("1) check size of counter \"" .
                    mb_substr("$count", mb_strlen("$count") - $i - 1, 1) . ".png\"", 5, 20);
                $myImage->drawString("2) check dimensions of file \"hintergrund.png\"", 5, 30);
                $myImage->drawString("   are big enough for the content.", 5, 40);
                $myImage->outputToScreen();
                die;
            };
            $image = new \Yana\Media\Image($filename, 'png');
            $destX = ($myImageValues[0] - $previousImageWidths);
            $destY = (int) floor(($myImageValues[1] - $imageValues[1]) / 2);
            $myImage->copyRegion($image, 0, 0, null, null, $destX, $destY);
            unset($image, $destX, $destY);
        }

        $myImage->outputToScreen();
        exit(0);
    }

}

?>