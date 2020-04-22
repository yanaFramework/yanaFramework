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
declare(strict_types=1);

namespace Yana\Views\Helpers\Functions;

/**
 * @ignore
 */
require_once dirname(__FILE__) . '/../../../../../include.php';


/**
 * @package  test
 * @ignore
 */
class MySmilies extends \Yana\Views\Helpers\Functions\Smilies
{

    /**
     * Returns a list of available icon files.
     *
     * The list is build from the profile configuration on demand.
     *
     * @return  \Yana\Views\Icons\Collection
     * @codeCoverageIgnore
     */
    protected function _buildListOfIcons()
    {
        $icons = new \Yana\Views\Icons\Collection();
        $icons["1"] = $icon1 = new \Yana\Views\Icons\File();
        $icon1->setId("Id1")->setPath("Path1");
        $icons["2"] = $icon2 = new \Yana\Views\Icons\File();
        $icon2->setId("Id2")->setPath("Path2");
        return $icons;
    }

}

/**
 * @package  test
 */
class SmiliesTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Views\Helpers\Functions\Smilies
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if (!\class_exists('\Smarty') || !\class_exists('\Smarty_Internal_Template')) {
            $this->markTestSkipped();
        }
        $configurationFactory = new \Yana\ConfigurationFactory();
        $configuration = $configurationFactory->loadConfiguration(CWD . 'resources/system.config.xml');
        $configuration->configdrive = YANA_INSTALL_DIR . 'config/system.drive.xml';
        $this->object = new \Yana\Views\Helpers\Functions\MySmilies(new \Yana\Core\Dependencies\Container($configuration));
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
    public function test__invoke()
    {
        $expected = '<table summary="smilies" class="gui_generator_smilies">' .
            '<tr><td title="{lang id=\'TITLE_SMILIES\'}" style="cursor: pointer">' .
            '<img alt="Id1" src="Path1" onmousedown="yanaAddIcon(\':Id1:\',event)"/></td>' . "\n" .
            '</tr><tr><td title="{lang id=\'TITLE_SMILIES\'}" style="cursor: pointer">' .
            '<img alt="Id2" src="Path2" onmousedown="yanaAddIcon(\':Id2:\',event)"/></td>' . "\n" .
            '</tr></table>';
        $this->assertSame($expected, $this->object->__invoke(array(), new \Smarty_Internal_Template("name", new \Smarty())));
    }

    /**
     * @test
     */
    public function test__invokeWithInvalidWidth()
    {
        $expected = '<table summary="smilies" class="gui_generator_smilies">' .
            '<tr><td title="{lang id=\'TITLE_SMILIES\'}" style="cursor: pointer">' .
            '<img alt="Id1" src="Path1" onmousedown="yanaAddIcon(\':Id1:\',event)"/></td>' . "\n" .
            '</tr><tr><td title="{lang id=\'TITLE_SMILIES\'}" style="cursor: pointer">' .
            '<img alt="Id2" src="Path2" onmousedown="yanaAddIcon(\':Id2:\',event)"/></td>' . "\n" .
            '</tr></table>';
        $this->assertSame($expected, $this->object->__invoke(array('width' => 0), new \Smarty_Internal_Template("name", new \Smarty())));
    }

    /**
     * @test
     */
    public function test__invokeWithWidth()
    {
        $expected = '<table summary="smilies" class="gui_generator_smilies">' .
            '<tr><td title="{lang id=\'TITLE_SMILIES\'}" style="cursor: pointer">' .
            '<img alt="Id1" src="Path1" onmousedown="yanaAddIcon(\':Id1:\',event)"/></td>' . "\n" .
            '<td title="{lang id=\'TITLE_SMILIES\'}" style="cursor: pointer">' .
            '<img alt="Id2" src="Path2" onmousedown="yanaAddIcon(\':Id2:\',event)"/></td>' . "\n" .
            '</tr></table>';
        $this->assertSame($expected, $this->object->__invoke(array('width' => 2), new \Smarty_Internal_Template("name", new \Smarty())));
    }

}
