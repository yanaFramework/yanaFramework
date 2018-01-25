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

namespace Yana\Views\Icons;

/**
 * @ignore
 */
require_once dirname(__FILE__) . '/../../../../include.php';

/**
 * @package test
 */
class LoaderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Views\Icons\Loader
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $file = new \Yana\Files\Text(\CWD . '/resources/icons.xml');
        $adapter = new \Yana\Views\Icons\XmlAdapter($file, \CWD . '/resources/');
        $this->object = new \Yana\Views\Icons\Loader($adapter);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function testGetIconsNotFoundException()
    {
        $file = new \Yana\Files\Text('no such file');
        $adapter = new \Yana\Views\Icons\XmlAdapter($file, \CWD . '/resources/');
        $loader = new \Yana\Views\Icons\Loader($adapter);
        $loader->getIcons();
    }

    /**
     * @test
     */
    public function testGetIcons()
    {
        $list = $this->object->getIcons();
        $this->assertCount(2, $list);
        /* @var $entity1 \Yana\Views\Icons\IsFile */
        $entity1 = $list['1'];
        /* @var $entity2 \Yana\Views\Icons\IsFile */
        $entity2 = $list['2'];
        $this->assertSame('<3', $entity1->getRegularExpression());
        $this->assertSame('\(\?\)', $entity2->getRegularExpression());
    }

}
