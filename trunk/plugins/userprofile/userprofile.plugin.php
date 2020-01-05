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

namespace Plugins\UserProfile;

/**
 * user management plugin
 *
 * This creates forms and implements functions to
 * manage user data.
 *
 * @package    yana
 * @subpackage plugins
 */
class UserProfilePlugin extends \Yana\Plugins\AbstractPlugin
{

    /**
     * Get form definition.
     *
     * @return  \Yana\Forms\Facade
     */
    protected function _getProfileForm()
    {
        $builder = $this->_getApplication()->buildForm('user_admin', 'userprofile');
        return $builder->__invoke();
    }

    /**
     * Get form definition.
     *
     * @return  \Yana\Forms\Facade
     */
    protected function _getDetailForm()
    {
        $builder = $this->_getApplication()->buildForm('user_admin', 'userdetails');
        return $builder->__invoke();
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
        $YANA = $this->_getApplication();
        $YANA->setVar("DESCRIPTION", $YANA->getLanguage()->getVar("DESCR_USER_EDIT"));
        $YANA->setVar("USERNAME", $this->_getSession()->getCurrentUserName());
        $builder = $this->_getApplication()->buildForm('user_admin', 'userdetails');
        $builder
            ->setEntries(1)
            ->setLayout(4)
            ->setWhere(array('USER_ID', '=', $this->_getSession()->getCurrentUserName()));
        $profileEditForm = $builder->__invoke();
        $YANA->setVar("USERFORM", $profileEditForm);
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
        $form = $this->_getDetailForm();

        $updateContext = $form->getUpdateForm()->getContext(); // switch to update context
        $rows = $updateContext->getRows(); // and get the updated rows
        if (!isset($rows[0]) || !is_array($rows[0])) { // this form doesn't contain a primary key, so the values will be at index "0"
            $message = "Input is invalid";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\Forms\MissingInputException($message, $level);
        }

        // Add user name to updated row or else we will get a database error
        $userName = \mb_strtoupper($this->_getSession()->getCurrentUserName());
        $updateContext->setRows(array($userName => $rows[0]));
        // Just for statistical purposes we keep track of when the form was changed
        $updateContext->updateRow($userName, array('USERPROFILE_MODIFIED' => time()));

        $worker = new \Yana\Forms\Worker($this->_connectToDatabase('user_admin'), $form);
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
        $form = $this->_getProfileForm();

        if (count($form->getUpdateValues()) !== 1) {
            $message = "Input is invalid";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\Forms\MissingInputException($message, $level);
        }

        $worker = new \Yana\Forms\Worker($this->_connectToDatabase('user_admin'), $form);
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
        $session = $this->_getSession();
        if (isset($target['user_id'])) {
            $userId = $target['user_id'];
            $session[__FUNCTION__] = $target['user_id'];
        } elseif (isset($session[__FUNCTION__])) {
            $userId = $session[__FUNCTION__];
        } else {
            $userId = $this->_getSession()->getCurrentUserName();
        }
        if ($this->_getSecurityFacade()->isExistingUserName($userId)) {
            $message = "No user found with id: " . \htmlentities($userId);
            $level = \Yana\Log\TypeEnumeration::ERROR;
            throw new \Yana\Core\Exceptions\User\NotFoundException($message, $level);
        }

        $userData = $this->_connectToDatabase('user_admin')->select("userprofile." . $userId);

        if (empty($userData['USER_ID']) || empty($userData['USER_ACTIVE'])) {
            return false;
        }

        $YANA = $this->_getApplication();
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
        $userData = $this->_connectToDatabase('user_admin')->select("userprofile.$target");

        // user not found or not active
        if (empty($userData['USER_ID']) || empty($userData['USER_ACTIVE'])) {
            $message = "No user found with id: " . \htmlentities($target);
            $level = \Yana\Log\TypeEnumeration::ERROR;
            throw new \Yana\Core\Exceptions\User\NotFoundException($message, $level);
        }

        $isOwnProfile = strcasecmp($target, $this->_getSession()->getCurrentUserName()) === 0;
        if (!empty($userData['USER_IMAGE']) && ($isOwnProfile || $userData['USER_IMAGE_ACTIVE'])) {
            if (!$thumb) {
                $image = new \Yana\Media\Image($userData['USER_IMAGE']);
            } else {
                $image = new \Yana\Media\Image(str_replace('/image.', '/thumb.', $userData['USER_IMAGE']));
            }
        } else {
            $image = new \Yana\Media\Image($this->_getApplication()->getVar('DATADIR') . 'userpic.gif');
        }
        $image->outputToScreen();
        exit;
    }

}

?>