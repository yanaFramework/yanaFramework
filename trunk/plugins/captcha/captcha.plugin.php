<?php
/**
 * Captcha
 *
 * Functions to generate and check CAPTCHA images.
 *
 * {@translation
 *   de: Captcha
 *
 *       Funktionen zum Erzeugen und Prüfen von CAPTCHA-Bildern.
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
 * Default library for common functions
 *
 * This plugin is important. It provides functionality
 * that might be usefull for other plugins.
 *
 * @access     public
 * @package    yana
 * @subpackage plugins
 */
class plugin_captcha extends StdClass implements IsPlugin
{

    /**
     * Default event handler
     *
     * @access  public
     * @param   string  $event  name of the called event in lower-case
     * @param   array   $ARGS   array of params passed to the function
     * @return  bool
     * @ignore
     */
    public function catchAll($event, array $ARGS)
    {
        return true;
    }

    /**
     * Create CAPTCHA-image.
     *
     * parameters taken:
     *
     * <ul>
     * <li> int security_image_index    index of image to display </li>
     * </ul>
     *
     * @type        primary
     * @template    null
     *
     * @access      public
     * @param       int  $security_image_index  id of index to check
     */
    public function security_get_image($security_image_index)
    {
        global $YANA;
        $imagesrc = dirname(__FILE__) . "captchas/security_image" . rand(0, 9) . ".png";
        $file = $YANA->getPlugins()->default_library->getResource('lib:/security.datfile');
        $contents = array();

        if (!$file->exists()) {
            $file->create();
        }
        $file->read();
        if (!$file->isEmpty()) {
            $contents = $file->getLine(0);
        }

        if (!isset($contents['TIME']) || $contents['TIME'] < time() || $contents['MAX_TIME'] < time()) {
            $contents = array();
            $contents['MAX_TIME'] = time() + 10000;
            for ($i=1;$i<10;$i++)
            {
                $contents["_$i"] = "";
                for ($j=0;$j<5;$j++)
                {
                    $letter = "";
                    // while letter is empty or black-listed
                    while (empty($letter) || in_array(mb_strtolower($letter), array('1', '0', 'o', 'l', 'i')))
                    {
                        switch (rand(0, 2))
                        {
                            case 0:
                                $letter = chr(rand(65, 90));
                            break;
                            case 1:
                                $letter = chr(rand(48, 57));
                            break;
                            case 2:
                                $letter = chr(rand(97, 122));
                            break;
                        }
                    }
                    $contents["_$i"] .= $letter;
                }
            }
        }
        $contents['TIME'] = time() + 1200;
        $file->setLine(0, $contents);
        $file->write();

        if ($security_image_index < 1 || $security_image_index > 9) {
            $text =& $contents['_1'];
        } else {

            $text =& $contents['_'.$security_image_index];
        }

        $image = new Image($imagesrc, 'png');
        for ($i = 0; $i < 5; $i++)
        {
            $image->drawString(
                $text[$i],             // Text
                4+($i*9)+rand(0, 1),   // x
                1+rand(-1, 1),         // y
                $image->getColor(      // color (palette index number)
                    40+rand(-30, 60),  // r
                    40+rand(-30, 60),  // g
                    40+rand(-30, 60)   // b
                ),
                5                      // font size
            );
        }
        $image->outputToScreen();
        exit(0);
    }

    /**
     * Test if a string matches the corresponding CAPTCHA.
     *
     * parameters taken:
     *
     * <ul>
     * <li> int security_image_index    index of image to display </li>
     * <li> string security_image       text to compare with CAPTCHA </li>
     * </ul>
     *
     * @type        primary
     * @template    null
     *
     * @access      public
     * @param       int     $security_image_index  id of index to check
     * @param       string  $security_image        user-entered text
     * @return      bool
     */
    public function security_check_image($security_image_index, $security_image)
    {
        global $YANA;

        $permission = $YANA->getVar("PERMISSION");
        if (is_int($permission) && $permission > 0) {
            return true;
        }

        $file = $YANA->getPlugins()->default_library->getResource('lib:/security.datfile');
        $file->read();
        if (!$file->isEmpty()) {
            $contents = $file->getLine(0);
        } else {
            $contents = array();
        }

        if ($contents['MAX_TIME'] < time()) {
            return false;
        } else {
            if ($security_image_index < 1 || $security_image_index > 9) {
                $text =& $contents['_1'];
            } else {
                $text =& $contents['_'.$security_image_index];
            }
            return (bool) (!empty($text) && ($security_image == $text));
        }
    }

}

?>