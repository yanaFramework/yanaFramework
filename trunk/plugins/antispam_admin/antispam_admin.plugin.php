<?php
/**
 * Anti-Spam - Setup
 *
 * Various smart techniques to avoid spam.
 *
 * {@translation
 *
 *   de: Anti-Spam - Setup
 *
 *       Verschiedene clevere Techniken zum Schutz vor Spam.
 *
 * }
 *
 * @author     Thomas Meyer
 * @type       config
 * @group      antispam
 * @extends    antispam
 * @license    http://www.gnu.org/licenses/gpl.txt
 *
 * @package    yana
 * @subpackage plugins
 */

/**
 * automated spam protection administration menu
 *
 * @access     public
 * @package    yana
 * @subpackage plugins
 */
class plugin_antispam_admin extends StdClass implements IsPlugin
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
     * @ignore
     */
    public function catchAll($event, array $ARGS)
    {
        return true;
    }

    /**
     * Get global settings
     *
     * this function does not expect any arguments
     *
     * @type        security
     * @user        group: admin, level: 100
     * @template    setup_antispam
     * @menu        group: setup
     * @safemode    true
     *
     * @access      public
     * @return      bool
     */
    public function get_setup_global_antispam()
    {
        /* this function expects no arguments */

        global $YANA;
        $configFile = $YANA->getResource('system:/config');
        $YANA->setVar("WRITEABLE", $configFile->isWriteable());
        $YANA->setVar("NEXT_ACTION", 'set_setup_global_antispam');
        return true;
    }

    /**
     * Save global settings
     *
     * takes configuration settings from the form as input
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    message
     * @onsuccess   goto: get_setup_global_antispam
     * @onerror     goto: get_setup_global_antispam, text: InvalidInputWarning
     * @safemode    true
     *
     * @access      public
     * @param       array  $ARGS  array of params passed to the function
     * @return      bool
     */
    public function set_setup_global_antispam(array $ARGS)
    {
        return $GLOBALS['YANA']->callAction('set_config_default', $this->_getArguments($ARGS));
    }

    /**
     * Get profile settings
     *
     * this function does not expect any arguments
     *
     * @type        security
     * @user        group: admin, level: 60
     * @template    setup_antispam
     * @menu        group: setup
     * @safemode    false
     *
     * @access  public
     * @return  bool
     */
    public function get_setup_antispam()
    {
        /* this function expects no arguments */

        global $YANA;
        $configFile = $YANA->getResource('system:/config');
        $YANA->setVar("WRITEABLE", $configFile->isWriteable());
        $YANA->setVar("NEXT_ACTION", 'set_setup_antispam');
        return true;
    }

    /**
     * Save profile settings
     *
     * takes configuration settings from the form as input
     *
     * @type        config
     * @user        group: admin, level: 60
     * @template    message
     * @onsuccess   goto: get_setup_antispam
     * @onerror     goto: get_setup_antispam, text: InvalidInputWarning
     * @safemode    false
     *
     * @access  public
     * @param   array  $ARGS  array of params passed to the function
     * @return  bool
     */
    public function set_setup_antispam(array $ARGS)
    {
        return $GLOBALS['YANA']->callAction('set_config_profile', $this->_getArguments($ARGS));
    }

    /**
     * event handler
     *
     * @access  private
     * @param   array  $ARGS  array of params passed to the function
     * @return  array
     * @ignore
     */
    private function _getArguments(array $ARGS)
    {
        $settings = array();
        if (isset($ARGS['spam/level']) && is_numeric($ARGS['spam/level'])) {
            $ARGS['spam/level'] = (int) $ARGS['spam/level'];

            if (isset($ARGS['spam/words'])) {
                if (is_array($ARGS['spam/words'])) {
                    foreach ($ARGS['spam/words'] as $i => $word)
                    {
                        if ($word != '') {
                            $settings['spam/words'][$i] = htmlspecialchars($word, ENT_COMPAT, 'UTF-8');
                        }
                    }
                    unset($i, $word);
                }
                unset($ARGS['spam/words']);
            }
            if (isset($settings['spam/words'])) {
                $oldWords = $GLOBALS['YANA']->getVar('PROFILE.SPAM.WORDS');
                foreach (array_keys($oldWords) as $word)
                {
                    if (!isset($settings['spam/words'][$word])) {
                        $settings['spam/words'][$word] = null;
                    }
                }
                unset($word);
            }
            switch ($ARGS['spam/level'])
            {
                case 3:
                    $settings['spam/level'] = 3;
                    $settings['spam/permission'] = true;
                    $settings['spam/word_filter'] = true;
                    $settings['spam/captcha'] = true;
                    $settings['spam/header'] = true;
                    $settings['spam/log'] = true;
                    $settings['spam/form_id'] = true;
                break;

                case 2:
                    $settings['spam/level'] = 2;
                    $settings['spam/permission'] = false;
                    $settings['spam/word_filter'] = true;
                    $settings['spam/captcha'] = true;
                    $settings['spam/header'] = true;
                    $settings['spam/log'] = true;
                    $settings['spam/form_id'] = true;
                break;

                case 1:
                    $settings['spam/level'] = 1;
                    $settings['spam/permission'] = false;
                    $settings['spam/word_filter'] = false;
                    $settings['spam/captcha'] = false;
                    $settings['spam/header'] = false;
                    $settings['spam/log'] = false;
                    $settings['spam/form_id'] = true;
                break;

                case 0:
                    $settings['spam/level'] = 0;
                    $settings['spam/permission'] = false;
                    $settings['spam/word_filter'] = false;
                    $settings['spam/captcha'] = false;
                    $settings['spam/header'] = false;
                    $settings['spam/log'] = false;
                    $settings['spam/form_id'] = true;
                break;

                case -1:
                default:
                    foreach (array_keys($ARGS) as $key)
                    {
                        if (strpos($key, 'spam/') === 0) {
                            $settings[$key] = $ARGS[$key];
                        }
                    }
                    $settings['spam/level'] = -1;
                break;

            }
        }
        return $settings;
    }
}
?>
