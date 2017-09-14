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
 * @package  test
 * @license  http://www.gnu.org/licenses/gpl.txt
 */

namespace Yana\Log\ViewHelpers;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class MessageLevelEnumerationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function testValues()
    {
        $this->assertEquals('alert', \Yana\Log\ViewHelpers\MessageLevelEnumeration::ALERT);
        $this->assertEquals('error', \Yana\Log\ViewHelpers\MessageLevelEnumeration::ERROR);
        $this->assertEquals('message', \Yana\Log\ViewHelpers\MessageLevelEnumeration::MESSAGE);
        $this->assertEquals('warning', \Yana\Log\ViewHelpers\MessageLevelEnumeration::WARNING);
    }

}
