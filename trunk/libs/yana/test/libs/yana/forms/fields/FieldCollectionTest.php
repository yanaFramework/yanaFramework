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

namespace Yana\Forms\Fields;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class FieldCollectionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Forms\Fields\IsFieldCollection
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Forms\Fields\FieldCollection();
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
    public function testOffsetSet()
    {
        $wrapper = new \Yana\Forms\Fields\FieldCollectionWrapper(new \Yana\Forms\Facade(), new \Yana\Forms\Setups\Context('test'));
        $item = new \Yana\Forms\Fields\Field($wrapper, new \Yana\Db\Ddl\Column('test'), new \Yana\Db\Ddl\Field('test'));
        $this->object['test'] = $item;
        $this->assertEquals($item, $this->object['test']);
    }

    /**
     * @test
     */
    public function testOffsetSetNull()
    {
        $wrapper = new \Yana\Forms\Fields\FieldCollectionWrapper(new \Yana\Forms\Facade(), new \Yana\Forms\Setups\Context('test'));
        $item = new \Yana\Forms\Fields\Field($wrapper, new \Yana\Db\Ddl\Column('test'), new \Yana\Db\Ddl\Field('test'));
        $this->object[] = $item;
        $this->assertEquals($item, $this->object['test']);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testOffsetSetInvalidArgumentException()
    {
        $this->object->offsetSet('test', 'invalid value');
    }

}
