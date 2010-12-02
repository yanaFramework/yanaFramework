<?php
/**
 * User Profile
 *
 * Allows users to create and edit their own profiles.
 *
 * {@translation
 *
 *   de:   Nutzerprofile
 *
 *         Erlaubt es Nutzern eigene Profile anzulegen und zu editieren.
 *
 * }
 *
 * @author     Thomas Meyer
 * @type       config
 * @extends    user
 * @license    http://www.gnu.org/licenses/gpl.txt
 *
 * @package    yana
 * @subpackage plugins
 */

/**
 * user management plugin
 *
 * This creates forms and implements functions to
 * manage user data.
 *
 * @access     public
 * @package    yana
 * @subpackage plugins
 */
class plugin_user_profile extends StdClass implements IsPlugin
{
    /**
     * @access  private
     * @static
     * @var     DBStream;
     */
    private static $database = null;

    /**
     * Form definition
     *
     * @access  private
     * @static
     * @var     DDLDefaultForm
     */
    private static $profileForm = null;

    /**
     * Form definition
     *
     * @access  private
     * @static
     * @var     DDLDefaultForm
     */
    private static $detailForm = null;

    /**
     * get database connection
     *
     * @access  protected
     * @static
     * @return  DbStream
     */
    protected static function getDatabase()
    {
        if (!isset(self::$database)) {
            self::$database = Yana::connect("user_admin");
        }
        return self::$database;
    }

    /**
     * get form definition
     *
     * @access  protected
     * @static
     * @return  DDLDefaultForm
     */
    protected static function getProfileForm()
    {
        if (!isset(self::$profileForm)) {
            $database = self::getDatabase();
            self::$profileForm = $database->getSchema()->getForm("userprofile");
        }
        return self::$profileForm;
    }

    /**
     * get form definition
     *
     * @access  protected
     * @static
     * @return  DDLDefaultForm
     */
    protected static function getDetailForm()
    {
        if (!isset(self::$detailForm)) {
            $database = self::getDatabase();
            self::$detailForm = $database->getSchema()->getForm("userdetails");
        }
        return self::$detailForm;
    }

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
    public function catchAll($event, array $ARGS)
    {
        return true;
    }

    /**
     * event handler
     *
     * @type        config
     * @template    PROFILE_EDIT_TEMPLATE
     * @user        level: 30
     * @menu        group: setup
     * @title       {lang id="user.22"}
     * @onerror     goto: index, text: UserNotFoundError
     *
     * @access      public
     * @return      bool
     */
    public function get_profile_edit()
    {
        global $YANA;
        $YANA->setVar("DESCRIPTION", $YANA->language->getVar("DESCR_USER_EDIT"));
        $form = self::getDetailForm();
        $query = $form->getQuery();
        $query->setRow(YanaUser::getUserName());
        $form->setEntriesPerPage(1);

        $userData = $query->getResults();

        if (empty($userData['USER_ID']) || empty($userData['USER_ACTIVE'])) {
            return false;
        }

        $YANA->setVar("USER", $userData);
        return true;
    }

    /**
     * event handler
     *
     * @type        config
     * @template    MESSAGE
     * @user        level: 30
     * @onsuccess   goto: GET_PROFILE_EDIT
     * @onerror     goto: GET_PROFILE_EDIT
     *
     * @access      public
     * @return      bool
     */
    public function set_profile_edit()
    {
        $updatedEntries = self::getProfileForm()->getUpdateValues();

        /* no data has been provided */
        if (count($updatedEntries) !== 1) {
            throw new InvalidInputWarning();
        }

        $id = YanaUser::getUserName();
        $entry = array_pop($updatedEntries);

        /* before doing anything, check if entry exists */
        if (!$database->exists("userprofile.${id}")) {

            /* error - no such entry */
            throw new InvalidInputWarning();
        }
        /* update the row */
        if (!$database->update("userprofile.${id}", $entry)) {
            /* error - unable to perform update - possibly readonly */
            return false;
        }

        /* commit changes */
        return $database->write();
    }

    /**
     * event handler
     *
     * @type        config
     * @template    MESSAGE
     * @user        level: 100
     * @onsuccess   goto: GET_PROFILE_LIST
     * @onerror     goto: GET_PROFILE_LIST
     *
     * @access      public
     * @return      bool
     */
    public function set_profiles_edit()
    {
        $updatedEntries = self::getProfileForm()->getUpdateValues();

        /* no data has been provided */
        if (count($updatedEntries) !== 1) {
            throw new InvalidInputWarning();
        }

        $database = self::getDatabase();
        foreach ($updatedEntries as $id => $entry)
        {
            /* before doing anything, check if entry exists */
            if (!$database->exists("userprofile.${id}")) {

                /* error - no such entry */
                throw new InvalidInputWarning();
            }

            /* update the row */
            if (!$database->update("userprofile.${id}", $entry)) {
                /* error - unable to perform update - possibly readonly */
                return false;
            }
        } /* end for */
        /* commit changes */
        return $database->write();
    }

    /**
     * view user profiles
     *
     * @type        default
     * @template    PROFILE_LIST_TEMPLATE
     * @menu        group: setup
     * @title       {lang id="user.25"}
     *
     * @access      public
     * @return      bool
     */
    public function get_profile_list()
    {
        return true;
    }

    /**
     * view user profile
     *
     * @type        default
     * @template    PROFILE_VIEW_TEMPLATE
     * @onerror     goto: index, text: UserNotFoundError
     *
     * @access      public
     * @return      bool
     * @param       array  $target  array of params passed to the function
     */
    public function view_profile(array $target = array())
    {
        global $YANA;
        if (isset($target['user_id'])) {
            $userId = $target['user_id'];
            $_SESSION['user'][__FUNCTION__] = $target['user_id'];
        } else if (isset($_SESSION['user'][__FUNCTION__])) {
            $userId = $_SESSION['user'][__FUNCTION__];
        } else {
            $userId = YanaUser::getUserName();
        }

        $userData = self::getDatabase()->select("userprofile." . $userId);

        if (empty($userData['USER_ID']) || empty($userData['USER_ACTIVE'])) {
            return false;
        }

        $YANA->setVar("USER", $userData);
        return true;
    }

    /**
     * show image
     *
     * @type        config
     * @template    null
     * @onerror     goto: index
     *
     * @access      public
     * @return      bool
     * @param       string  $target  user id
     * @param       bool    $thumb   use thumbnail (yes/no)
     */
    public function get_profile_image($target, $thumb = false)
    {
        /* @var $YANA Yana */
        global $YANA;
        $userData = self::getDatabase()->select("userprofile.$target");

        // user not found or not active
        if (!$userData['USER_ID'] || !$userData['USER_ACTIVE']) {
            throw new UserNotFoundError();
        }

        $isOwnProfile = strcasecmp($target, YanaUser::getUserName()) === 0;
        if (!empty($userData['USER_IMAGE']) && ($isOwnProfile || $userData['USER_IMAGE_ACTIVE'])) {
            if (!$thumb) {
                $image = new Image($userData['USER_IMAGE']);
            } else {
                $image = new Image(str_replace('/image.', '/thumb.', $userData['USER_IMAGE']));
            }
        } else {
            $image = new Image($YANA->getVar('DATADIR').'userpic.gif');
        }
        $image->outputToScreen();
        exit;
    }
}

?>