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

namespace Yana\Views\MetaData;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class SkinMetaDataTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var SkinMetaData
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Views\MetaData\SkinMetaData();
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
    public function testAddTemplate()
    {
        $template1 = new \Yana\Views\MetaData\TemplateMetaData();
        $template1->setId('1');
        $template2 = new \Yana\Views\MetaData\TemplateMetaData();
        $template2->setId('2');
        $templates = array('1' => $template1, '2' => $template2);
        $this->assertSame($templates, $this->object->addTemplate($template1)->addTemplate($template2)->getTemplates());
    }

    /**
     * @test
     */
    public function testGetTemplates()
    {
        $this->assertEquals(array(), $this->object->getTemplates());
    }

    /**
     * @test
     */
    public function testGetReport()
    {
        $this->assertTrue($this->object->getReport() instanceof \Yana\Report\IsReport);
    }

}
