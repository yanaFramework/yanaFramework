<?php
/**
 * PHPUnit test-case.
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

namespace Yana\Db\Export;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class XmlFactoryExporterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function testConvertArrayToXml()
    {
        $input = array(
            'ft' => array(),
            't' => array(),
            'i' => array(),
            'u' => array()
        );
        $xml = "<?xml version=\"1.0\"?>\n<database>\n" .
            "\t<table id=\"ft\">\n" .
            "\t</table>\n" .
            "\t<table id=\"t\">\n" .
            "\t</table>\n" .
            "\t<table id=\"i\">\n" .
            "\t</table>\n" .
            "\t<table id=\"u\">\n" .
            "\t</table>\n" .
            "</database>";
        $this->assertSame($xml, \Yana\Db\Export\XmlFactoryExporter::convertArrayToXml($input));
    }

    /**
     * @test
     */
    public function testConvertArrayToXml1()
    {
        $input = array(
            'ft' =>
            array(
                1 =>
                array(
                    'FTVALUE' => 1,
                    'FTID' => 1,
                    '@t' =>
                    array(
                        'FOO' =>
                        array(
                            'TVALUE' => 1,
                            'TB' => true,
                            'FTID' => 1,
                            'TID' => 'FOO',
                        ),
                    ),
                ),
            ),
            't' =>
            array(
                'FOO' =>
                array(
                    'TVALUE' => 1,
                    'TB' => true,
                    'FTID' => 1,
                    'TID' => 'FOO',
                    '@i' =>
                    array(
                        'FOO' =>
                        array(
                            'TA' =>
                            array(
                                0 => 'Test',
                            ),
                            'IID' => 'FOO',
                            'TVALUE' => 1,
                            'TB' => true,
                            'FTID' => 1,
                            'TID' => 'FOO',
                        ),
                    ),
                ),
            ),
            'i' =>
            array(
                'FOO' =>
                array(
                    'TA' =>
                    array(
                        0 => 'Test',
                    ),
                    'IID' => 'FOO',
                    'TVALUE' => 1,
                    'TB' => true,
                    'FTID' => 1,
                    'TID' => 'FOO',
                ),
            ),
            'u' =>
            array(
            ),
        );
        $xml = "<?xml version=\"1.0\"?>\n<database>\n" .
            "\t<table id=\"ft\">\n" .
            "\t\t<row id=\"1\">\n" .
            "\t\t\t<ftvalue>1</ftvalue>\n" .
            "\t\t\t<ftid>1</ftid>\n" .
            "\t\t\t<table id=\"t\">\n" .
            "\t\t\t\t<row id=\"FOO\">\n" .
            "\t\t\t\t\t<tvalue>1</tvalue>\n" .
            "\t\t\t\t\t<tb>true</tb>\n" .
            "\t\t\t\t\t<ftid>1</ftid>\n" .
            "\t\t\t\t\t<tid>FOO</tid>\n" .
            "\t\t\t\t</row>\n" .
            "\t\t\t</table>\n" .
            "\t\t</row>\n" .
            "\t</table>\n" .
            "\t<table id=\"t\">\n" .
            "\t\t<row id=\"FOO\">\n" .
            "\t\t\t<tvalue>1</tvalue>\n" .
            "\t\t\t<tb>true</tb>\n" .
            "\t\t\t<ftid>1</ftid>\n" .
            "\t\t\t<tid>FOO</tid>\n" .
            "\t\t\t<table id=\"i\">\n" .
            "\t\t\t\t<row id=\"FOO\">\n" .
            "\t\t\t\t\t<ta>\n" .
            "\t\t\t\t\t\t<string id=\"0\">Test</string>\n" .
            "\t\t\t\t\t</ta>\n" .
            "\t\t\t\t\t<iid>FOO</iid>\n" .
            "\t\t\t\t\t<tvalue>1</tvalue>\n" .
            "\t\t\t\t\t<tb>true</tb>\n" .
            "\t\t\t\t\t<ftid>1</ftid>\n" .
            "\t\t\t\t\t<tid>FOO</tid>\n" .
            "\t\t\t\t</row>\n" .
            "\t\t\t</table>\n" .
            "\t\t</row>\n" .
            "\t</table>\n" .
            "\t<table id=\"i\">\n" .
            "\t\t<row id=\"FOO\">\n" .
            "\t\t\t<ta>\n" .
            "\t\t\t\t<string id=\"0\">Test</string>\n" .
            "\t\t\t</ta>\n" .
            "\t\t\t<iid>FOO</iid>\n" .
            "\t\t\t<tvalue>1</tvalue>\n" .
            "\t\t\t<tb>true</tb>\n" .
            "\t\t\t<ftid>1</ftid>\n" .
            "\t\t\t<tid>FOO</tid>\n" .
            "\t\t</row>\n" .
            "\t</table>\n" .
            "\t<table id=\"u\">\n" .
            "\t</table>\n" .
            "</database>";
        $this->assertSame($xml, \Yana\Db\Export\XmlFactoryExporter::convertArrayToXml($input));
    }

}
