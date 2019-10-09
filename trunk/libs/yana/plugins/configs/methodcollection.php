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

namespace Yana\Plugins\Configs;

/**
 * <<Collection>> Plugin configuration method collection.
 *
 * This class is a type-safe collection of instances of {@see \Yana\Plugins\Configs\MethodConfiguration}.
 *
 * @package     yana
 * @subpackage  plugins
 *
 * @ignore
 */
class MethodCollection extends \Yana\Core\AbstractCollection implements \Yana\Plugins\Configs\IsMethodCollection
{

    /**
     * Unset item.
     *
     * @param  string  $offset  lower-cased method-name
     */
    public function offsetUnset($offset)
    {
        assert(is_string($offset), 'Invalid argument $offset: string expected');
        parent::offsetUnset(mb_strtolower($offset));
    }

    /**
     * Check if item exists.
     *
     * @param   scalar  $offset  index of item to test
     * @return  bool
     */
    public function offsetExists($offset)
    {
        assert(is_string($offset), 'Invalid argument $offset: string expected');
        return parent::offsetExists(mb_strtolower($offset));
    }

    /**
     * Get item.
     *
     * @param   string  $offset  lower-cased method-name
     */
    public function offsetGet($offset)
    {
        assert(is_string($offset), 'Invalid argument $offset: string expected');
        return parent::offsetGet(mb_strtolower($offset));
    }

    /**
     * Insert or replace item.
     *
     * @param   string                                       $offset  ignored
     * @param   \Yana\Plugins\Configs\IsMethodConfiguration  $value   newly added instance
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the value is not a valid item of the collection
     * @return  \Yana\Plugins\Configs\IsMethodConfiguration
     */
    public function offsetSet($offset, $value)
    {
        if ($value instanceof \Yana\Plugins\Configs\IsMethodConfiguration) {
            if (!is_string($offset)) {
                $offset = $value->getMethodName();
            }
            assert(is_string($offset), 'Invalid argument $offset: string expected');
            return $this->_offsetSet(mb_strtolower($offset), $value);
        } else {
            $message = "Instance of IsMethodConfiguration expected. " .
                "Found " . gettype($value) . "(" . ((is_object($value)) ? get_class($value) : $value) . ") instead.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message);
        }
    }

    /**
     * Returns a xml-report object, which you may print, transform or output to a file.
     *
     * @param   \Yana\Report\IsReport  $report  base report
     * @return  \Yana\Report\IsReport
     */
    public function getReport(\Yana\Report\IsReport $report = null)
    {
        if (is_null($report)) {
            $report = \Yana\Report\Xml::createReport(__CLASS__);
        }

        /**
         * loop through interface definitions
         */
        assert(!isset($key), 'Cannot redeclare var $key.');
        assert(!isset($element), 'Cannot redeclare var $element.');
        foreach ($this->toArray() as $key => $element)
        {
            assert($element instanceof \Yana\Plugins\Configs\IsMethodConfiguration);
            $element->getReport($report->addReport("$key"));
        } // end foreach

        return $report;
    }

}

?>