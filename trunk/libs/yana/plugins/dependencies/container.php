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

namespace Yana\Plugins\Dependencies;

/**
 * Dependency ccntainer.
 *
 * Used to inject class dependencies into plugins.
 *
 * @package     yana
 * @subpackage  plugins
 */
class Container extends \Yana\Core\StdObject implements \Yana\Plugins\Dependencies\IsContainer
{

    use \Yana\Core\Dependencies\HasPlugin, \Yana\Core\Dependencies\HasSession;

    /**
     * @var \Yana\Plugins\Data\IsAdapter
     */
    private $_pluginAdapter = null;

    /**
     * @var  array
     */
    private $_defaultEvent = array();

    /**
     * 
     * @param  \Yana\Security\Sessions\IsWrapper  $session       bound to current environment parameters
     * @param  array                              $defaultEvent  tells us what to do as fallback and comes from application configuration file
     */
    public function __construct(\Yana\Security\Sessions\IsWrapper $session, array $defaultEvent)
    {
        $this->setSession($session);
        $this->setDefaultEvent($defaultEvent);
    }

    /**
     * Get default event settings.
     *
     * @return  array
     */
    public function getDefaultEvent()
    {
        return $this->_defaultEvent;
    }

    /**
     * Replace default event settings.
     *
     * @return  array
     */
    public function setDefaultEvent(array $event)
    {
        $this->_defaultEvent = $event;
        return $this;
    }

    /**
     * Get database adapter for table plugins.
     *
     * @return  \Yana\Plugins\Data\IsAdapter
     */
    public function getPluginAdapter()
    {
        if (!isset($this->_pluginAdapter)) {
            $factory = new \Yana\Db\ConnectionFactory(new \Yana\Db\SchemaFactory());
            $this->_pluginAdapter = new \Yana\Plugins\Data\Adapter($factory->createConnection('plugins'));
        }
        return $this->_pluginAdapter;
    }

    /**
     * Set database adapter.
     *
     * @param   \Yana\Plugins\Data\IsAdapter  $pluginAdapter  database adapter for table plugins
     * @return  $this
     */
    public function setPluginAdapter(\Yana\Plugins\Data\IsAdapter $pluginAdapter)
    {
        $this->_pluginAdapter = $pluginAdapter;
        return $this;
    }

}

?>