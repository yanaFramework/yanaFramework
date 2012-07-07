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
     * Get database connection.
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
     * Get form definition.
     *
     * @access  protected
     * @static
     * @return  FormFacade
     */
    protected static function getProfileForm()
    {
        $builder = new \Yana\Forms\Builder('user_admin');
        return $builder->setId('userprofile')->__invoke();
    }

    /**
     * Get form definition.
     *
     * @access  protected
     * @static
     * @return  FormFacade
     */
    protected static function getDetailForm()
    {
        $builder = new \Yana\Forms\Builder('user_admin');
        return $builder->setId('userdetails')->__invoke();
    }

    /**
     * Default event handler.
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
     *
     * @access      public
     */
    public function get_profile_edit()
    {
        $YANA = Yana::getInstance();
        $YANA->setVar("DESCRIPTION", $YANA->getLanguage()->getVar("DESCR_USER_EDIT"));
        $YANA->setVar("USERNAME", YanaUser::getUserName());
        $builder = new \Yana\Forms\Builder('user_admin');
        $builder->setId('userdetails')
            ->setEntries(1)
            ->setLayout(1)
            ->setWhere(array('USER_ID', '=', YanaUser::getUserName()));
        $YANA->setVar("USERFORM", $builder->__invoke());
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
        $form = self::getProfileForm();

        if (count($form->getUpdateValues()) !== 1) {
            throw new InvalidInputWarning();
        }

        $worker = new \Yana\Forms\Worker(self::getDatabase(), $form);
        $worker->beforeCreate(
            function (&$id)
            {
                $id = YanaUser::getUserName();
            }
        );
        return $worker->update();
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
        $form = self::getProfileForm();

        if (count($form->getUpdateValues()) !== 1) {
            throw new InvalidInputWarning();
        }

        $worker = new \Yana\Forms\Worker(self::getDatabase(), $form);
        return $worker->update();
    }

    /**
     * View user profiles.
     *
     * @type        default
     * @template    PROFILE_LIST_TEMPLATE
     * @menu        group: setup
     * @title       {lang id="user.25"}
     *
     * @access      public
     */
    public function get_profile_list()
    {
        // Just views template - no business logic required.
    }

    /**
     * view user profile
     *
     * @type        default
     * @template    PROFILE_VIEW_TEMPLATE
     * @onerror     goto: index
     *
     * @access      public
     * @param       array  $target  array of params passed to the function
     */
    public function view_profile(array $target = array())
    {
        if (isset($target['user_id'])) {
            $userId = $target['user_id'];
            $_SESSION['user'][__FUNCTION__] = $target['user_id'];
        } elseif (isset($_SESSION['user'][__FUNCTION__])) {
            $userId = $_SESSION['user'][__FUNCTION__];
        } else {
            $userId = YanaUser::getUserName();
        }
        if (YanaUser::isUser($userId)) {
            $message = "No user found with id: " . \htmlentities($userId);
            $level = E_USER_ERROR;
            throw new \Yana\Core\Exceptions\User\NotFoundException($message, $level);
        }

        $userData = self::getDatabase()->select("userprofile." . $userId);

        if (empty($userData['USER_ID']) || empty($userData['USER_ACTIVE'])) {
            return false;
        }

        $YANA = Yana::getInstance();
        $YANA->setVar("USERNAME", $userId);
        $YANA->setVar("USER", $userData);
    }

    /**
     * show image
     *
     * @type        config
     * @template    null
     * @onerror     goto: index
     *
     * @access      public
     * @param       string  $target  user id
     * @param       bool    $thumb   use thumbnail (yes/no)
     */
    public function get_profile_image($target, $thumb = false)
    {
        $userData = self::getDatabase()->select("userprofile.$target");

        // user not found or not active
        if (empty($userData['USER_ID']) || empty($userData['USER_ACTIVE'])) {
            $message = "No user found with id: " . \htmlentities($target);
            $level = E_USER_ERROR;
            throw new \Yana\Core\Exceptions\User\NotFoundException($message, $level);
        }

        $isOwnProfile = strcasecmp($target, YanaUser::getUserName()) === 0;
        if (!empty($userData['USER_IMAGE']) && ($isOwnProfile || $userData['USER_IMAGE_ACTIVE'])) {
            if (!$thumb) {
                $image = new \Yana\Media\Image($userData['USER_IMAGE']);
            } else {
                $image = new \Yana\Media\Image(str_replace('/image.', '/thumb.', $userData['USER_IMAGE']));
            }
        } else {
            $image = new \Yana\Media\Image(Yana::getInstance()->getVar('DATADIR') . 'userpic.gif');
        }
        $image->outputToScreen();
        exit;
    }

}

?>