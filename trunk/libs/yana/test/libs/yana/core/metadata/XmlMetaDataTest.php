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

namespace Yana\Core\MetaData;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class XmlMetaDataTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Core\MetaData\XmlMetaData
     */
    protected $object = null;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $xml = '<?xml version="1.0" encoding="utf-8" ?>
                <metadata>
                    <title>Default</title>
                    <author>Erik</author>
                    <author>Tom</author>
                    <url>http://test.url</url>
                    <description>this entry is a duplicate and should be ignored</description>
                    <description>default description</description>
                    <description lang="en">en description</description>
                </metadata>';
        $this->object = new \Yana\Core\MetaData\XmlMetaData($xml);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $streamFacade = new \Yana\Files\Streams\Stream();
        if ($streamFacade->isRegistered('null')) {
            $streamFacade->unregisterWrapper('null');
        }
    }

    /**
     * @test
     */
    public function testGetTitle()
    {
        $this->assertEquals('Default', $this->object->getTitle());
    }

    /**
     * @test
     */
    public function testGetDescriptions()
    {
        $expected = array('' => 'default description', 'en' => 'en description');
        $this->assertEquals($expected, $this->object->getDescriptions());
    }

    /**
     * @test
     */
    public function testGetAuthor()
    {
        $this->assertEquals('Erik, Tom', $this->object->getAuthor());
    }

    /**
     * @test
     */
    public function testGetUrl()
    {
        $this->assertEquals('http://test.url', $this->object->getUrl());
    }

}

?>
