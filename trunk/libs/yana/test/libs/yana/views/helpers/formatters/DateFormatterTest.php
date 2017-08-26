<?php
/**
 * PHPUnit test-case
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

namespace Yana\Views\Helpers\Formatters;

/**
 * @ignore
 */
require_once dirname(__FILE__) . '/../../../../../include.php';

/**
 * @package  test
 */
class DateFormatterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function testInvoke()
    {
        \Yana\Views\Helpers\Formatters\DateFormatter::setFormat('r', 'date.toLocaleString()');
        $time = strtotime('2000-01-01 0:0:0');
        $dateFormatter = new \Yana\Views\Helpers\Formatters\DateFormatter();
        $string = $dateFormatter($time);
        $this->assertTrue(\Yana\Util\Strings::contains($string, 'new Date(' . $time . '000)'), 'Must contain JavaScript portion: ' . $string);
        $this->assertTrue(\Yana\Util\Strings::contains($string, date('r', $time)), 'Must contain PHP fallback: ' . $string);
    }

    /**
     * @test
     */
    public function testSetFormat()
    {
        \Yana\Views\Helpers\Formatters\DateFormatter::setFormat('c', 'date.toLocaleTimeString()');
        $time = strtotime('2000-01-01 0:0:0');
        $dateFormatter = new \Yana\Views\Helpers\Formatters\DateFormatter();
        $string = $dateFormatter($time);
        $this->assertTrue(\Yana\Util\Strings::contains($string, 'date.toLocaleTimeString()'), 'Must contain JavaScript value: ' . $string);
        $this->assertTrue(\Yana\Util\Strings::contains($string, date('c', $time)), 'Must contain PHP formatted value: ' . $string);
    }

}

?>
