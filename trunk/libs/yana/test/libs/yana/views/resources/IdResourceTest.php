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

namespace Yana\Views\Resources;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 * @ignore
 */
class MyIdResource extends \Yana\Views\Resources\IdResource
{
    public function fetch($name, &$source, &$mtime)
    {
        return parent::fetch($name, $source, $mtime);
    }

    public function fetchTimestamp($name)
    {
        return parent::fetchTimestamp($name);
    }

}

/**
 * @package  test
 */
class IdResourceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Views\Resources\MyIdResource
     */
    protected $object;

    /**
     * @var \Yana\Core\Dependencies\IsViewContainer
     */
    protected $container;

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
        \Yana\Views\Skins\Skin::setBaseDirectory(YANA_INSTALL_DIR . 'skins/');
        $this->container = new \Yana\Core\Dependencies\Container($configuration);
        $this->container->getRegistry()->setVar('PROFILE.SKIN', 'default');
        $this->object = new \Yana\Views\Resources\MyIdResource($this->container);
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
    public function testFetch()
    {
        $name = 'sitemap';
        $source = null;
        $mtime = null;
        $fileName = $this->container->getSkin()->getTemplateData('sitemap')->getFile();
        $this->assertNull($this->object->fetch($name, $source, $mtime));
        $this->assertSame(\filemtime($fileName), $mtime);
        $this->assertSame(file_get_contents($fileName), $source);
    }

    /**
     * @test
     */
    public function testFetchTimestamp()
    {
        $this->assertFalse($this->object->fetchTimestamp('no-such-file'));
        $fileName = $this->container->getSkin()->getTemplateData('sitemap')->getFile();
        $this->assertSame(\filemtime($fileName), $this->object->fetchTimestamp('sitemap'));
    }

}
