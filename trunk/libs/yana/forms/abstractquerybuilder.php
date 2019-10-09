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
namespace Yana\Forms;
/**
 * <<abstract>> Build a queries based on a given form.
 *
 * @package     yana
 * @subpackage  form
 */
class AbstractQueryBuilder extends \Yana\Core\StdObject
{

    /**
     * Database connection used to create the querys.
     *
     * @var  \Yana\Db\IsConnection
     */
    private $_db = null;

    /**
     * Definition of form.
     *
     * @var  \Yana\Forms\Facade
     */
    private $_form = null;

    /**
     * Object cache.
     *
     * @var  array
     */
    private $_cache = array();

    /**
     * Get cache contents.
     *
     * @param   string  $key  to check
     * @return  bool
     */
    protected function _isCached($key)
    {
        return isset($this->_cache[$key]);
    }

    /**
     * Get cached content.
     *
     * @return  \Yana\Db\Queries\Select
     */
    protected function _getCache($key)
    {
        return $this->_cache[$key];
    }

    /**
     * Replace contents of cache.
     *
     * @param   string                   $key    where to save content
     * @param   \Yana\Db\Queries\Select  $value  new content
     * @return  $this
     */
    protected function _setCache($key, \Yana\Db\Queries\Select $value)
    {
        assert(is_string($key), 'Invalid argument type $key: String expected.');
        $this->_cache[(string) $key] = $value;
        return $this;
    }

    /**
     * Flush contents of cache.
     *
     * @return  $this
     */
    protected function _resetCache()
    {
        $this->_cache = array();
        return $this;
    }

    /**
     * Replace database connection.
     *
     * @param   \Yana\Db\IsConnection  $db  database connection used to create the querys
     * @return  $this
     */
    protected function _setDatabase(\Yana\Db\IsConnection $db)
    {
        $this->_db = $db;
        return $this;
    }

    /**
     * Set form object.
     *
     * @param   \Yana\Forms\Facade  $form  configuring the contents of the form
     * @return  $this
     */
    public function setForm(\Yana\Forms\Facade $form)
    {
        $this->_form = $form;
        $this->_resetCache();
        return $this;
    }

    /**
     * Get form object.
     *
     * @return  \Yana\Forms\Facade
     */
    public function getForm()
    {
        return $this->_form;
    }

    /**
     * Get database connection
     *
     * @return  \Yana\Db\IsConnection
     */
    public function getDatabase()
    {
        return $this->_db;
    }

}

?>