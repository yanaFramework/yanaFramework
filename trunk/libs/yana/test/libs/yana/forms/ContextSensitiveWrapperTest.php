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

namespace Yana\Forms;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../include.php';

/**
 * @package  test
 */
class ContextSensitiveWrapperTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Forms\Setups\Context
     */
    protected $context;

    /**
     * @var \Yana\Forms\Facade
     */
    protected $form;

    /**
     * @var \Yana\Forms\ContextSensitiveWrapper
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->context = new \Yana\Forms\Setups\Context('Test Context');
        $this->form = new \Yana\Forms\Facade();
        $this->object = new \Yana\Forms\ContextSensitiveWrapper($this->form, $this->context);
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
    public function test__callContext()
    {
        $this->assertSame($this->context->getContextName(), $this->object->getContextName());
    }

    /**
     * @test
     */
    public function test__callForm()
    {
        $this->assertSame($this->form->getName(), $this->object->getName());
    }

    /**
     * @test
     */
    public function testGetContext()
    {
        $this->assertSame($this->context, $this->object->getContext());
    }

    /**
     * @test
     */
    public function testGetPrimaryKey()
    {
        $this->assertNull($this->object->getPrimaryKey());
    }

    /**
     * @test
     */
    public function testHasRows()
    {
        $this->assertFalse($this->object->hasRows());
        $this->object->getContext()->setRows(array(array('key' => 'value')));
        $this->assertTrue($this->object->hasRows());
    }

    /**
     * @test
     */
    public function testGetRowCount()
    {
        $this->assertSame(0, $this->object->getRowCount());
        $this->object->getContext()->setRows(array(array('key' => 'value')));
        $this->assertSame(1, $this->object->getRowCount());
    }

    /**
     * @test
     */
    public function testNextRow()
    {
        $this->assertSame($this->object, $this->object->nextRow());
        $this->object->getContext()->setRows(array('a' => array('key' => 'value')));
        $this->assertSame('A', $this->object->getContext()->getRows()->key());
        $this->assertNull($this->object->nextRow()->getRows()->key());
    }

}
