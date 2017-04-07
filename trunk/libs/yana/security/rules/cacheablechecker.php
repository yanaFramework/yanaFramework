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
 * This adds a cache-adapter to the rule-checker class.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class CacheableChecker extends \Yana\Security\Rules\Checker implements \Yana\Data\Adapters\IsCacheable
{

    /**
     * result cache.
     *
     * @var  array
     */
    private $_cache = array();

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
     * Check requirements.
     *
     * Check if user meets on of the applicable rules to apply changes to the profile identified by the argument $profileId.
     *
     * Returns bool(true) if the user's permission level is high enough to
     * execute the changes and bool(false) otherwise.
     *
     * @param   string                       $profileId  profile id in upper-case
     * @param   string                       $action     action parameter in lower-case
     * @param   \Yana\Security\Users\IsUser  $user       user information to check
     * @return  bool
     * @throws  \Yana\Security\Rules\Requirements\NotFoundException  when no requirements are found
     */
    public function checkRules($profileId, $action, \Yana\Security\Users\IsUser $user)
    {
        assert('is_string($profileId); // Invalid argument type: $profileId. String expected');
        assert('is_string($action); // Invalid argument type: $action. String expected');

        assert('!isset($cache); // Cannot redeclare $cache');
        $cache = $this->_getCache();

        assert('!isset($userName); // Cannot redeclare $userName');
        $userName = $user->getId();
        /**
         * {@internal
         * check if value has already been processed and cached
         * and if so, return the cached value instead, for a
         * better performance.
         * }}
         */
        if (!isset($cache["$profileId\\$userName\\$action"])) {

            $cache["$profileId\\$userName\\$action"] = parent::checkRules($profileId, $action, $user);
        }

        assert('is_bool($cache["$profileId\\\\$userName\\\\$action"]); /* unexpected result in cached value */');
        return $cache["$profileId\\$userName\\$action"];
    }

}

?>