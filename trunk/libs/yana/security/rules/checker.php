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
 *
 * @ignore
 */

namespace Yana\Security\Rules;

/**
 * Rule checking class.
 *
 * Allows collection and checking of security rules.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class Checker extends \Yana\Core\Object
    implements \Yana\Data\Adapters\IsCacheable, \Yana\Security\Rules\IsChecker, \Yana\Data\Adapters\IsDataConsumer
{

    /**
     * database connection
     *
     * @var  \Yana\Db\IsConnection
     */
    private $_database = null;

    /**
     * result cache
     *
     * @var  array
     */
    private $_cache = array();

    /**
     * @var  \Yana\Security\Rules\Collection
     */
    private $_rules = null;

    /**
     * Returns collection of security rules.
     *
     * Lazy-loads one, if none exists.
     *
     * @return  \Yana\Security\Rules\Collection
     */
    protected function _getRules()
    {
        if (!isset($this->_rules)) {
            $this->_rules = new \Yana\Security\Rules\Collection();
        }
        return $this->_rules;
    }

    /**
     * Set datasource.
     *
     * @param  \Yana\Db\IsConnection  $database  data-source
     * @ignore
     */
    public function setDatasource(\Yana\Db\IsConnection $database)
    {
        $this->_database = $database;
    }

    /**
     * Replace the cache adapter.
     *
     * This class uses an ArrayAdapter by default.
     * Overwrite only for unit-tests, or if you are absolutely sure you need to
     * and know what you are doing.
     * Replacing this by the wrong adapter might introduce a security risk,
     * unless you are in a very specific usage scenario.
     *
     * Note that this may also replace the cache contents.
     *
     * @param   \Yana\Data\Adapters\IsDataAdapter  $cache  new cache adapter
     * @return  \Yana\Data\Adapters\IsCacheable
     * @ignore
     */
    public function setCache(\Yana\Data\Adapters\IsDataAdapter $cache)
    {
        $this->_cache = $cache;
        return $this;
    }

    /**
     * Get cache-adapter
     *
     * @return  \Yana\Data\Adapters\IsDataAdapter
     * @ignore
     */
    protected function _getCache()
    {
        if (!isset($this->_cache)) {
            $this->_cache = new \Yana\Data\Adapters\ArrayAdapter();
        }
        return $this->_cache;
    }

    /**
     * Get datasource.
     *
     * @return  \Yana\Db\IsConnection
     * @ignore
     */
    public function getDatasource()
    {
        if (!isset($this->_database)) {
            $this->_database = \Yana\Application::connect('user');
        }
        return $this->_database;
    }

    /**
     * Add security rule.
     *
     * This method adds a user-definded implementation to a list of custom security checks.
     *
     * To execute these rules call checkPermission().
     * The rules are executed in the order in which they were added.
     *
     * @param   \Yana\Security\Rules\IsRule  $rule  must be a valid callback
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException when the function is not callable
     * @return  \Yana\Security\Rules\Checker
     */
    public function addSecurityRule(\Yana\Security\Rules\IsRule $rule)
    {
        $rules = $this->_getRules();
        $rules[] = $rule;
        return $this;
    }

    /**
     * Check permission.
     *
     * Check if user has permission to apply changes to the profile identified
     * by the argument $profileId.
     *
     * Returns bool(true) if the user's permission level is high enough to
     * execute the changes and bool(false) otherwise.
     *
     * @param   string  $profileId  profile id
     * @param   string  $action     action
     * @param   string  $userName   user name
     * @return  bool
     * @ignore
     */
    public function checkPermission($profileId = null, $action = null, $userName = null)
    {
        assert('is_null($profileId) || is_string($profileId); // Wrong type for argument 1. String expected');
        assert('is_null($action) || is_string($action); // Wrong type for argument 2. String expected');
        assert('is_null($userName) || is_string($userName); // Wrong type for argument 3. String expected');

        /* Argument 1 */
        if (empty($profileId)) {
            $profileId = \Yana\Application::getId();
        }
        $profileId = mb_strtoupper("$profileId");
        assert('is_string($profileId);');

        /* Argument 2 */
        if (empty($action)) {
            $action = \Yana\Plugins\Manager::getLastEvent();
            // security restriction on undefined event
            if (empty($action)) {
                return false;
            }
        }
        $action = mb_strtolower("$action");
        assert('is_string($action);');

        /* Argument 3 */
        /**
         * {@internal
         * The user id is resolved by the "user" plugin and stored
         * in a session var called "user_name", so other plugins can look it up.
         * }}
         */
        if (empty($userName)) {
            $userName = \Yana\User::getUserName();

            // if no value is provided, switch to default user
            if (empty($userName)) {
                $userName = '';
            }

        }
        $userName = mb_strtoupper("$userName");
        assert('is_string($userName);');

        assert('!isset($cache); // Cannot redeclare $cache');
        $cache = $this->_getCache();
        /**
         * {@internal
         * check if value has already been processed and cached
         * and if so, return the cached value instead, for a
         * better performance.
         * }}
         */
        if (isset($cache["$profileId\\$userName\\$action"])) {
            assert('is_bool($cache["$profileId\\\\$userName\\\\$action"]); /* unexpected result in cached value */');
            return $cache["$profileId\\$userName\\$action"];
        }
        $database = $this->getDatasource();
        // if security settings are missing, auto-refresh them and issue a warning
        if ($database->isEmpty("securityactionrules")) {
            $this->refreshPluginSecuritySettings();
            $message = "No security settings found. Trying to auto-refresh table 'securityactionrules'.";
            \Yana\Log\LogManager::getLogger()->addLog($message);
            return false;
        }
        // find out what the required permission level is to perform the current action
        assert('!isset($requiredLevels); // Cannot redeclare var $requiredLevels');
        $requiredLevels = $database->select("securityactionrules", array('action_id', '=', $action));
        // if not defined, load defaults
        if (empty($requiredLevels)) {
            $requiredLevels = \Yana\Application::getDefault('event.user');
            if (!empty($requiredLevels)) {
                $requiredLevels = array($requiredLevels);
            }
        }
        // if nothing else is defined, then the current event is public ...
        if (empty($requiredLevels)) {
            $cache["$profileId\\$userName\\$action"] = true;
            return true;
        }

        // ... else check user permissions
        assert('!isset($result); // Cannot redeclare var $result');
        $result = false;
        assert('!isset($required); // cannot redeclare $required');
        foreach ($requiredLevels as $required)
        {
            if ($this->_getRules()->checkRules($this->getDatasource(), $required, $profileId, $action, $userName)) {
                $result = true;
                break;
            }
        }
        unset($required);

        /* cache the result and return it */
        $cache["$profileId\\$userName\\$action"] = $result;
        assert('is_bool($result); // return type should be boolean');
        return $result;
    }

}

?>