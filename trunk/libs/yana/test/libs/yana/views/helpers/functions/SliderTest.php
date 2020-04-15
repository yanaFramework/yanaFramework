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
class MySliderHelper extends \Yana\Views\Helpers\Functions\Slider
{
    public function _mapParameters(\Yana\Core\IsVarContainer $document, array $params)
    {
        return parent::_mapParameters($document, $params);
    }

}

/**
 * @package  test
 */
class SliderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Views\Helpers\Functions\MySliderHelper
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
        $this->container = new \Yana\Core\Dependencies\Container($configuration);
        $this->object = new \Yana\Views\Helpers\Functions\MySliderHelper($this->container);
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
    public function test_mapParametersEmpty()
    {
        $document = new \Yana\Core\VarContainer();
        $this->assertSame($this->object, $this->object->_mapParameters($document, array()));
    }

    /**
     * @test
     */
    public function test_mapParameters()
    {
        $params = array(
            'width' => '12',
            'min' => '34.5',
            'max' => '56.7',
            'step' => '78.9',
            'backgroundColor' =>  '#123456',
            'value' => '9.10',
            'inputName' => __FUNCTION__
        );
        $document = new \Yana\Core\VarContainer();
        $this->object->_mapParameters($document, $params);
        $this->assertSame((int) $params['width'], $document->getVar('width'));
        $this->assertSame((float) $params['min'], $document->getVar('min'));
        $this->assertSame((float) $params['max'], $document->getVar('max'));
        $this->assertSame((float) $params['step'], $document->getVar('step'));
        $this->assertSame($params['backgroundColor'], $document->getVar('background'));
        $this->assertSame((float) $params['value'], $document->getVar('value'));
        $this->assertSame($params['inputName'], $document->getVar('inputName'));
    }

}
