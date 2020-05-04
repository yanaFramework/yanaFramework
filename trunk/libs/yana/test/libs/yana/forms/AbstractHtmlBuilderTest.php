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
 * @ignore
 */
class MyAbstractHtmlBuilder extends \Yana\Forms\AbstractHtmlBuilder
{

    public function __invoke(): string
    {
        return "";
    }

    /**
     * Returns form facade.
     *
     * @return  \Yana\Forms\Facade
     */
    public function _getFacade()
    {
        return parent::_getFacade();
    }

    /**
     * Returns form facade.
     *
     * @return  \Yana\Views\Templates\IsTemplate
     */
    public function _getTemplate()
    {
        return parent::_getTemplate();
    }

}

/**
 * @package  test
 */
class AbstractHtmlBuilderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Forms\MyAbstractHtmlBuilder
     */
    protected $object;

    /**
     * @var \Yana\Forms\Facade
     */
    protected $facade;

    /**
     * @var \Yana\Views\Templates\IsTemplate
     */
    protected $template;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->facade = new \Yana\Forms\Facade();
        $this->template = new \Yana\Views\Templates\NullTemplate();
        $this->object = new \Yana\Forms\MyAbstractHtmlBuilder($this->facade, $this->template);
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
    public function test_getTemplate()
    {
        $this->assertSame($this->template, $this->object->_getTemplate());
    }

    /**
     * @test
     */
    public function test_getFacade()
    {
        $this->assertSame($this->facade, $this->object->_getFacade());
    }
}
