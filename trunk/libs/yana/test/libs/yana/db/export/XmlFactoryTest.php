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
class XmlFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\IsConnectionFactory
     */
    protected $connectionFactory;

    /**
     * @var \Yana\Db\IsConnection
     */
    protected $db;

    /**
     * @var \Yana\Db\Export\XmlFactory
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        try {
            $schemaFactory = new \Yana\Db\SchemaFactory();
            $this->connectionFactory = new \Yana\Db\ConnectionFactory($schemaFactory);
            $this->db = $this->connectionFactory->createConnection($schemaFactory->createSchema('check'));
            // reset database
            $this->db->remove('i', array(), 0);
            $this->db->remove('t', array(), 0);
            $this->db->remove('ft', array(), 0);
            $this->db->commit();

            $this->object = new \Yana\Db\Export\XmlFactory();

        } catch (\Exception $e) {
            $this->markTestSkipped("Unable to connect to database");
        }
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }

    /**
     * @test
     */
    public function testCreateXML()
    {
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
        $this->assertSame($xml, $this->object->addDatabaseName('check')->createXML($this->connectionFactory));
    }

    /**
     * @test
     */
    public function testCreateXMLForeignKeys()
    {
        $this->db->insert('ft.1', array('ftvalue' => 1));

        $this->db->insert('t.foo', array('tvalue' => 1, 'ftid' => 1, 'tb' => true));

        $this->db->insert('i.foo', array('ta' => array('Test')));
        $this->db->commit();

        $this->object->setUsingForeignKeys(true)->addDatabaseName('check');
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
        $this->assertSame($xml, $this->object->createXML($this->connectionFactory));
    }

    /**
     * @test
     */
    public function testCreateXMLEmpty()
    {
        $xml = "<?xml version=\"1.0\"?>\n" .
            "<database>\n</database>";
        $this->assertSame($xml, $this->object->createXML($this->connectionFactory));
    }

}
