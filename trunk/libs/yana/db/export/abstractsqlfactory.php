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

namespace Yana\Db\Export;

/**
 * <<abstract>> database Creator.
 *
 * This decorator class is intended to create SQL DDL (data definition language)
 * from YANA Framework - database structure files.
 *
 * For this task it provides functions which create specific
 * DDL for various DBS.
 *
 * @package     yana
 * @subpackage  db
 */
abstract class AbstractSqlFactory extends \Yana\Core\StdObject
{

    /**
     * @var \Yana\Db\Export\Xsl\IsProvider
     */
    private $_provider = null;

    /**
     * @var \Yana\Db\Ddl\Database
     */
    private $_processor = null;

    /**
     * Get XSL-Document provider.
     *
     * @return \Yana\Db\Export\Xsl\IsProvider 
     */
    protected function _getProvider()
    {
        if (!isset($this->_provider)) {
            $this->_provider = new \Yana\Db\Export\Xsl\Provider();
        }
        return $this->_provider;
    }

    /**
     * Set XSL-Document provider.
     *
     * @param   \Yana\Db\Export\Xsl\IsProvider  $provider  loads XSL templates
     * @return  $this
     */
    protected function _setProvider(\Yana\Db\Export\Xsl\IsProvider $provider)
    {
        $this->_provider = $provider;
        return $this;
    }

    /**
     * Get XSLT processor.
     *
     * @return  \Yana\Db\Export\Xsl\IsProcessor
     * @throws  \Yana\Db\Export\Xsl\ProcessorException  When the class XSLTProcessor is not found
     */
    protected function _getProcessor()
    {
        if (!isset($this->_processor)) {
            $this->_processor = new \Yana\Db\Export\Xsl\Processor();
        }
        return $this->_processor;
    }

    /**
     * Set XSLT processor.
     *
     * @param   \Yana\Db\Export\Xsl\IsProcessor  $processor  handles the XSL transform
     * @return  $this
     */
    protected function _setProcessor(\Yana\Db\Export\Xsl\IsProcessor $processor)
    {
        $this->_processor = $processor;
        return $this;
    }

}

?>