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

namespace Yana\Core\Exceptions\Forms;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../../include.php';

/**
 * @package test
 */
class InvalidCharacterExceptionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Core\Exceptions\Forms\InvalidCharacterException
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Core\Exceptions\Forms\InvalidCharacterException();
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
    public function testSetField()
    {
        $this->object->setField('field');
        $this->assertEquals(array('FIELD' => 'field'), $this->object->getData());
    }

    /**
     * @test
     */
    public function testSetValue()
    {
        $this->object->setValue('value');
        $this->assertEquals(array('VALUE' => 'value'), $this->object->getData());
    }

    /**
     * @test
     */
    public function testSetValid()
    {
        $this->object->setValid('a-z');
        $this->assertEquals(array('VALID' => 'a-z'), $this->object->getData());
    }

}

?>