<?php
/**
 * YANA library
 *
 * Software:  Yana PHP-Framework
 * Version:   {VERSION} - {DATE}
 * License:   GNU GPL  http://www.gnu.org/licenses/
 *
 * This program: can be redistributed and/or modified under the
 * terms of the GNU General Public License as published by the
 * Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see http://www.gnu.org/licenses/.
 *
 * This notice MAY NOT be removed.
 *
 * @package  yana
 * @license  http://www.gnu.org/licenses/gpl.txt
 */

namespace Yana\Db\Ddl;

/**
 * database grant structure
 *
 * This wrapper class represents the user rights management information stored for various elements
 * of a database.
 *
 * Rights management comes in 3-layer, each of which is optional in this document.
 * <ul>
 *  <li> User groups: like Sales, Human Ressources </li>
 *  <li> User role: like Project Manager </li>
 *  <li> Security level: an integer of 0 through 100 </li>
 * </ul>
 *
 * So you may decide that every manager of human ressources, who has at least a security level of
 * 50 may create a new employee and view salaries, but that it requires a manger of HR with security
 * level 80 to update them.
 *
 * You may even skip any of the levels to perhaps allow anybody to view a catalog form, who has at
 * least a security level of 1, or grant access for any member of the sales department to sales
 * data.
 *
 * You may precisely choose what each member may or may not do: select (view), insert (create),
 * update (edit), delete.
 *
 * Note that you may have multiple grant elements to define several alternatives, so that you may
 * either be a member of the sales department OR a company manager to view and edit sales
 * information.
 *
 * In analogy to databases, there is also a grant option, which allows users to temporarily grant
 * any right they own in person, to any other user. So a manager may grant (and later revoke) all
 * his rights to an assistant while he is on vacation.
 *
 * The Yana Framework implements a profile-system on top of all that as well.
 * Application profiles may define different subsidiaries inside your company. E.g. Europe or Asia.
 * Note that you can't define profile-access using grants as these provide a partition of your data.
 *
 * @access      public
 * @package     yana
 * @subpackage  db
 */
class Grant extends \Yana\Db\Ddl\DDL
{
    /**#@+
     * @ignore
     * @access  protected
     */

    /**
     * tag name for persistance mapping: object <-> XDDL
     * @var string
     */
    protected $xddlTag = "grant";

    /**
     * attributes for persistance mapping: object <-> XDDL
     * @var  array
     */
    protected $xddlAttributes = array(
        'role'   => array('role',   'string'),
        'user'   => array('user',   'string'),
        'level'  => array('level',  'int'),
        'select' => array('select', 'bool'),
        'insert' => array('insert', 'bool'),
        'update' => array('update', 'bool'),
        'delete' => array('delete', 'bool'),
        'grant'  => array('grant',  'bool')
    );

    /** @var string */ protected $role = null;
    /** @var string */ protected $user = null;
    /** @var int    */ protected $level = null;
    /** @var bool   */ protected $select = true;
    /** @var bool   */ protected $insert = true;
    /** @var bool   */ protected $update = true;
    /** @var bool   */ protected $delete = true;
    /** @var bool   */ protected $grant = true;

    /**#@-*/

    /**
     * Get user role.
     *
     * The role a user plays inside a user group.
     * This may be any string value.
     *
     * @access  public
     * @return  string
     */
    public function getRole()
    {
        if (is_string($this->role)) {
            return $this->role;
        } else {
            return null;
        }
    }

    /**
     * Set required user role.
     *
     * The role a user plays inside a user group.
     * This may be any string value.
     *
     * Note that it is not checked wether the role is in use ore not.
     *
     * @access  public
     * @param   string  $role  new value of this property
     * @return  \Yana\Db\Ddl\Grant
     */
    public function setRole($role = "")
    {
        assert('is_string($role); // Wrong type for argument 1. String expected');
        if (empty($role)) {
            $this->role = null;
        } else {
            $this->role = "$role";
        }
        return $this;
    }

    /**
     * Get user group.
     *
     * The group a user belongs. Each group defines it's own default security level (which may be
     * overwritten though).
     *
     * You may additionally define security levels to check.
     *
     * @access  public
     * @return  string
     */
    public function getUser()
    {
        if (is_string($this->user)) {
            return $this->user;
        } else {
            return null;
        }
    }

    /**
     * Set required user group.
     *
     * The group a user belongs. Each group defines it's own default security level (which may be
     * overwritten though).
     *
     * You may additionally define security levels to check.
     *
     * Note that it is not checked wether the group is in use ore not.
     *
     * @access  public
     * @param   string  $user  new value of this property
     * @return  \Yana\Db\Ddl\Grant 
     */
    public function setUser($user = "")
    {
        assert('is_string($user); // Wrong type for argument 1. String expected');
        if (empty($user)) {
            $this->user = null;
        } else {
            $this->user = "$user";
        }
        return $this;
    }

    /**
     * Get security level.
     *
     * The security level may be any integer number of 0 through 100.
     * You may translate this to 0-100 percent, where 0 is the lowest level of access and 100 is the
     * highest.
     * If there is no restriction, the function returns NULL.
     *
     * @access  public
     * @return  int
     */
    public function getLevel()
    {
        if (is_int($this->level)) {
            return $this->level;
        } else {
            return null;
        }
    }

    /**
     * Set security level.
     *
     * The security level may be any integer number of 0 through 100.
     * You may translate this to 0-100 percent, where 0 is the lowest level of access and 100 is the
     * highest.
     *
     * @access  public
     * @param   string  $level  new value of this property
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the given security level is outside range [0,100]
     * @return  \Yana\Db\Ddl\Grant 
     */
    public function setLevel($level = null)
    {
        assert('is_null($level) || is_int($level); // Wrong type for argument 1. Integer expected');
        if (is_null($level)) {
            $this->level = null;
        } elseif ($level < 0 || $level > 100) {
            $message = "Security level '$level' outside range [0,100].";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, E_USER_WARNING);
        } else {
            $this->level = (int) $level;
        }
        return $this;
    }

    /**
     * Grant select statements.
     *
     * Tells whether the user is granted to issue a select-statement on the database object.
     *
     * @access  public
     * @return  bool
     */
    public function isSelectable()
    {
        return !empty($this->select);
    }

    /**
     * Set/revoke select.
     *
     * Tells whether the user is granted to issue a select-statement on the database object.
     *
     * This value defaults to bool(true).
     *
     * @access  public
     * @param   bool  $isSelectable  true: selectable, false: not selectable
     * @return  \Yana\Db\Ddl\Grant 
     */
    public function setSelect($isSelectable = true)
    {
        assert('is_bool($isSelectable); // Wrong type for argument 1. Boolean expected');
        $this->select = (bool) $isSelectable;
        return $this;
    }

    /**
     * Grant insert statements.
     *
     * Tells whether the user is granted to issue an insert-statement on the database object.
     *
     * @access  public
     * @return  bool
     */
    public function isInsertable()
    {
        return !empty($this->insert);
    }

    /**
     * Set/revoke insert.
     *
     * Tells whether the user is granted to issue an insert-statement on the database object.
     *
     * This value defaults to bool(true).
     *
     * @access  public
     * @param   bool  $isInsertable  true = allow, false = disallow insert statements
     * @return  \Yana\Db\Ddl\Grant 
     */
    public function setInsert($isInsertable = true)
    {
        assert('is_bool($isInsertable); // Wrong type for argument 1. Boolean expected');
        $this->insert = (bool) $isInsertable;
        return $this;
    }

    /**
     * Grant update statements.
     *
     * Tells whether the user is granted to issue an update-statement on the database object.
     *
     * @access  public
     * @return  bool
     */
    public function isUpdatable()
    {
        return !empty($this->update);
    }

    /**
     * Set/revoke update.
     *
     * Tells whether the user is granted to issue an update-statement on the database object.
     *
     * This value defaults to bool(true).
     *
     * @access  public
     * @param   bool  $isUpdatable  new value of this property
     * @return  \Yana\Db\Ddl\Grant 
     */
    public function setUpdate($isUpdatable = true)
    {
        assert('is_bool($isUpdatable); // Wrong type for argument 1. Boolean expected');
        $this->update = (bool) $isUpdatable;
        return $this;
    }

    /**
     * Grant delete statements.
     *
     * Tells whether the user is granted to issue a delete-statement on the database object.
     *
     * @access  public
     * @return  bool
     */
    public function isDeletable()
    {
        return !empty($this->delete);
    }

    /**
     * set/revoke delete
     *
     * Tells whether the user is granted to issue a delete-statement on the database object.
     *
     * This value defaults to bool(true).
     *
     * @access  public
     * @param   bool  $isDeletable  new value of this property
     */
    public function setDelete($isDeletable = true)
    {
        assert('is_bool($isDeletable); // Wrong type for argument 1. Boolean expected');
        $this->delete = (bool) $isDeletable;
    }

    /**
     * Has grant option.
     *
     * Tells whether the user may temporarily grant his security permissions to other users.
     *
     * In analogy to databases, this option allows users to temporarily grant any right they own in
     * person, to any other user. So a manager may grant (and later revoke) all his rights to an
     * assistant while he is on vacation.
     *
     * @access  public
     * @return  bool
     */
    public function isGrantable()
    {
        return !empty($this->grant);
    }

    /**
     * Set/revoke grant option.
     *
     * Tells whether the user may temporarily grant his security permissions to other users.
     *
     * @access  public
     * @param   bool  $isGrantable  true: may grant, false: may not grant
     * @return  \Yana\Db\Ddl\Grant 
     */
    public function setGrantOption($isGrantable = true)
    {
        assert('is_bool($isGrantable); // Wrong type for argument 1. Boolean expected');
        $this->grant = (bool) $isGrantable;
        return $this;
    }

    /**
     * Check if the current user is granted a certain permission.
     *
     * Returns bool(true) if the current grant permits the user to a certain action,
     * or a certain combination of actions.
     * Returns bool(false) otherwise.
     *
     * @access  public
     * @param   bool  $select  must be selectable
     * @param   bool  $insert  must be insertable
     * @param   bool  $update  must be updatable
     * @param   bool  $delete  must be deletable
     * @param   bool  $grant   must be grantable
     * @return  bool
     * @ignore
     */
    public function checkPermission($select = false, $insert = false, $update = false, $delete = false, $grant = false)
    {
        assert('is_bool($select); // Wrong type for argument 1. Boolean expected');
        assert('is_bool($insert); // Wrong type for argument 2. Boolean expected');
        assert('is_bool($update); // Wrong type for argument 3. Boolean expected');
        assert('is_bool($delete); // Wrong type for argument 4. Boolean expected');
        assert('is_bool($grant); // Wrong type for argument 5. Boolean expected');
        switch (true)
        {
            case $select && $this->isSelectable():
            case $insert && $this->isInsertable():
            case $update && $this->isUpdatable():
            case $delete && $this->isDeletable():
            case $grant && $this->isGrantable():
                $user = $this->getUser();
                $role = $this->getRole();
                $level = $this->getLevel();
                if (empty($user) && empty($role) && empty($level)) {
                    return true;
                }
                $required = array(
                    \Yana\Plugins\Annotations\Enumeration::GROUP => $user,
                    \Yana\Plugins\Annotations\Enumeration::ROLE => $role,
                    \Yana\Plugins\Annotations\Enumeration::LEVEL => $level
                );
                $profileId = \Yana::getId();
                $action = \Yana\Plugins\Manager::getLastEvent();
                $userName = (string) \YanaUser::getUserName();
                return (bool) \SessionManager::checkRule($required, $profileId, $action, $userName);
            break;
            default:
                return false;
            break;
        }
    }

    /**
     * Check if the user is granted certain permissions.
     *
     * The function takes a list of grant objects and a list of requirements.
     *
     * Returns bool(true) if at least one of the grants permits the user to a certain action,
     * or a certain combination of actions.
     * Returns bool(false) otherwise.
     *
     * @access  public
     * @static
     * @param   array  $grants  list of \Yana\Db\Ddl\Grant objects
     * @param   bool   $select  must be selectable
     * @param   bool   $insert  must be insertable
     * @param   bool   $update  must be updatable
     * @param   bool   $delete  must be deletable
     * @param   bool   $grant   must be grantable
     * @return  bool
     */
    public static function checkPermissions(array $grants, $select = false, $insert = false, $update = false, $delete = false, $grant = false)
    {
        $hasPermission = true;
        /* @var $grant \Yana\Db\Ddl\Grant */
        foreach ($grants as $grant)
        {
            assert('$grant instanceof \Yana\Db\Ddl\Grant;');
            $hasPermission = $grant->checkPermission(
                $grant->isSelectable(), $grant->isInsertable(), $grant->isUpdatable(), $grant->isDeletable(), $grant->isGrantable()
            );
            if ($hasPermission) {
                 break;
            }
        }
        return $hasPermission;
    }

    /**
     * Unserializes a XDDL-node to an instance of this class and returns it.
     *
     * @access  public
     * @static
     * @param   \SimpleXMLElement  $node    XML node
     * @param   mixed              $parent  parent node (if any)
     * @return  \Yana\Db\Ddl\Grant
     */
    public static function unserializeFromXDDL(\SimpleXMLElement $node, $parent = null)
    {
        $ddl = new self();
        $ddl->_unserializeFromXDDL($node);
        return $ddl;
    }

}

?>