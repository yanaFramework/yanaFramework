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

namespace Plugins\AntiSpamAdmin;

/**
 * automated spam protection administration menu
 *
 * @package    yana
 * @subpackage plugins
 */
class AntiSpamAdminPlugin extends \Yana\Plugins\AbstractPlugin
{

    /**
     * Get global settings.
     *
     * @type        security
     * @user        group: admin, level: 100
     * @template    setup_antispam
     * @menu        group: setup
     * @safemode    true
     *
     * @access      public
     */
    public function get_setup_global_antispam()
    {
        /* this function expects no arguments */

        $YANA = $this->_getApplication();
        $configFile = $YANA->getResource('system:/config');
        $YANA->setVar("WRITEABLE", $configFile->isWriteable());
        $YANA->setVar("NEXT_ACTION", 'set_setup_global_antispam');
    }

    /**
     * Save global settings.
     *
     * @type        config
     * @user        group: admin, level: 100
     * @template    message
     * @onsuccess   goto: get_setup_global_antispam
     * @onerror     goto: get_setup_global_antispam, text: Yana\Core\Exceptions\InvalidInputException
     * @safemode    true
     *
     * @access      public
     * @param       array  $ARGS  configuration settings from the form
     * @return      bool
     */
    public function set_setup_global_antispam(array $ARGS)
    {
        return $this->_getApplication()->execute('set_config_default', $this->_getArguments($ARGS));
    }

    /**
     * Get profile settings.
     *
     * @type        security
     * @user        group: admin, level: 60
     * @template    setup_antispam
     * @menu        group: setup
     * @safemode    false
     *
     * @access  public
     */
    public function get_setup_antispam()
    {
        /* this function expects no arguments */

        $YANA = $this->_getApplication();
        $configFile = $YANA->getResource('system:/config');
        $YANA->setVar("WRITEABLE", $configFile->isWriteable());
        $YANA->setVar("NEXT_ACTION", 'set_setup_antispam');
    }

    /**
     * Save profile settings.
     *
     * @type        config
     * @user        group: admin, level: 60
     * @template    message
     * @onsuccess   goto: get_setup_antispam
     * @onerror     goto: get_setup_antispam, text: Yana\Core\Exceptions\InvalidInputException
     * @safemode    false
     *
     * @access  public
     * @param   array  $ARGS  configuration settings from the form
     * @return  bool
     */
    public function set_setup_antispam(array $ARGS)
    {
        return $this->_getApplication()->execute('set_config_profile', $this->_getArguments($ARGS));
    }

    /**
     * event handler
     *
     * @access  private
     * @param   array  $ARGS  array of params passed to the function
     * @return  array
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
                $oldWords = $this->_getApplication()->getVar('PROFILE.SPAM.WORDS');
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