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

namespace Yana\Views\Resources\Helpers;

/**
 * <<abstract>> Helper for file resource.
 *
 * @package     yana
 * @subpackage  views
 */
abstract class AbstractRelativePathsFilter extends \Yana\Views\Helpers\AbstractViewHelper
{

    /**
     * @var string
     */
    private $_leftDelimiter = "";

    /**
     * @var string
     */
    private $_rightDelimiter = "";

    /**
     * Get left Smarty delimiter.
     *
     * Usually is "{".
     *
     * @return  string
     */
    public function getLeftDelimiter(): string
    {
        if ($this->_leftDelimiter === "") {
            // @codeCoverageIgnoreStart
            $this->_leftDelimiter = (string) $this->_getViewManager()->getSmarty()->left_delimiter;
            // @codeCoverageIgnoreEnd
        }
        return $this->_leftDelimiter;
    }

    /**
     * Get right Smarty delimiter.
     *
     * Usually is "}".
     *
     * @return  string
     */
    public function getRightDelimiter(): string
    {
        if ($this->_rightDelimiter === "") {
            // @codeCoverageIgnoreStart
            $this->_rightDelimiter = (string) $this->_getViewManager()->getSmarty()->right_delimiter;
            // @codeCoverageIgnoreEnd
        }
        return $this->_rightDelimiter;
    }

    /**
     * Set left Smarty delimiter.
     *
     * @param   string  $leftDelimiter  must not contain spaces (not checked)
     * @return  $this
     */
    public function setLeftDelimiter(string $leftDelimiter)
    {
        $this->_leftDelimiter = $leftDelimiter;
        return $this;
    }

    /**
     * Set right Smarty delimiter.
     *
     * @param   string  $rightDelimiter  must not contain spaces (not checked)
     * @return  $this
     */
    public function setRightDelimiter(string $rightDelimiter)
    {
        $this->_rightDelimiter = $rightDelimiter;
        return $this;
    }

}

?>