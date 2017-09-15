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

namespace Yana\Views\Icons;

/**
 * <<abstract>> Provides access functions to base directory.
 *
 * @package     yana
 * @subpackage  views
 */
abstract class AbstractLoader extends \Yana\Core\Object implements \Yana\Views\Icons\IsLoader
{

    /**
     * @var \Yana\Views\Icons\IsDataAdapter
     */
    private $_adapter = null;

    /**
     * <<constructor>> Initialize directory.
     *
     * @param  \Yana\Views\Icons\IsDataAdapter  $adapter  containing configuration of icons
     */
    public function __construct(\Yana\Views\Icons\IsDataAdapter $adapter = null)
    {
        $this->_adapter = $adapter;
    }

    /**
     * Get adapter to load icons.
     *
     * @return  \Yana\Views\Icons\IsDataAdapter
     * @codeCoverageIgnore
     */
    protected function _getAdapter()
    {
        if (!isset($this->_adapter)) {
            assert('!isset($builder); // Cannot redeclare var $builder');
            assert('!isset($application); // Cannot redeclare var $application');
            $builder = new \Yana\ApplicationBuilder();
            $application = $builder->buildApplication();
            $this->_adapter = new \Yana\Views\Icons\XmlAdapter(new \Yana\Files\Dir($application->getVar('PROFILE.SMILEYDIR')));
            unset($builder, $application);
        }
        return $this->_adapter;
    }

    /**
     * Get collection of icon file entities.
     *
     * @return  \Yana\Views\Icons\Collection
     * @codeCoverageIgnore
     */
    protected function _getCollectionOfFiles()
    {
        return $this->_getAdapter()->getAll();
    }

}

?>