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
declare(strict_types=1);

namespace Yana\Db\Ddl\Factories;

/**
 * <<abstract>> Process database reverse engineering task.
 *
 * @package     yana
 * @subpackage  db
 */
abstract class AbstractDoctrineWorker extends \Yana\Core\StdObject implements \Yana\Db\Ddl\Factories\IsWorker
{

    /**
     * @var  \Yana\Db\Ddl\Factories\IsDoctrineMapper
     */
    private $_mapper = null;

    /**
     * @var  \Yana\Db\Ddl\Factories\IsDoctrineWrapper
     */
    private $_wrapper = null;

    /**
     *<<constructor>> Initialize instance.
     *
     * @param  \Yana\Db\Ddl\Factories\IsDoctrineMapper   $mapper   converts doctrine DBAL objects to Yana objects
     * @param  \Yana\Db\Ddl\Factories\IsDoctrineWrapper  $wrapper  wraps a doctrine database connection
     */
    public function __construct(\Yana\Db\Ddl\Factories\IsDoctrineMapper $mapper, \Yana\Db\Ddl\Factories\IsDoctrineWrapper $wrapper)
    {
        $this->_mapper = $mapper;
        $this->_wrapper = $wrapper;
    }

    /**
     * Returns Doctrine to XDDL mapping object.
     *
     * @return  \Yana\Db\Ddl\Factories\IsDoctrineMapper
     */
    protected function _getMapper(): \Yana\Db\Ddl\Factories\IsDoctrineMapper
    {
        return $this->_mapper;
    }

    /**
     * Returns Doctrine DBAL wrapper object.
     *
     * @return  \Yana\Db\Ddl\Factories\IsDoctrineWrapper
     */
    protected function _getWrapper(): \Yana\Db\Ddl\Factories\IsDoctrineWrapper
    {
        return $this->_wrapper;
    }

}

?>