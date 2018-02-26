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
     * @covers Yana\Forms\ContextSensitiveWrapper::__call
     * @todo   Implement test__call().
     */
    public function test__call()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @test
     */
    public function testGetContext()
    {
        $this->assertSame($this->context, $this->object->getContext());
    }

    /**
     * @covers Yana\Forms\ContextSensitiveWrapper::getPrimaryKey
     * @todo   Implement testGetPrimaryKey().
     */
    public function testGetPrimaryKey()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Forms\ContextSensitiveWrapper::hasRows
     * @todo   Implement testHasRows().
     */
    public function testHasRows()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Forms\ContextSensitiveWrapper::getRowCount
     * @todo   Implement testGetRowCount().
     */
    public function testGetRowCount()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Forms\ContextSensitiveWrapper::nextRow
     * @todo   Implement testNextRow().
     */
    public function testNextRow()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

}
