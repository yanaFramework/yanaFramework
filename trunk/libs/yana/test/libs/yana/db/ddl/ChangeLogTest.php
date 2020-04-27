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

namespace Yana\Db\Ddl;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';


/**
 * @package  test
 */
class ChangeLogTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Ddl\Database
     */
    protected $parent;

    /**
     * @var \Yana\Db\Ddl\ChangeLog
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->parent = new \Yana\Db\Ddl\Database('test');
        $this->object = new \Yana\Db\Ddl\ChangeLog($this->parent);
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
    public function testGetParent()
    {
        $this->assertSame($this->parent, $this->object->getParent());
    }

    /**
     * addEntry
     *
     * @test
     */
    public function testAddEntry()
    {
        for ($i = 1; $i <10; $i++)
        {
            $nr = sprintf("%04d",$i);
            $log = new \Yana\Db\Ddl\Logs\Create('logcreate');
            $log->setName("name_" . $nr);
            $log->setVersion($nr);
            $this->object->addEntry($log);
        }

        $countAll = count($this->object->getEntries());
        $countV1 = count($this->object->getEntries("0004"));

        $this->assertEquals($countAll , 9, '\Yana\Db\Ddl\ChangeLog, adding Logs or retrieving them failed');
        $this->assertEquals($countV1, 5, 'assert failed, adding Logs with a Version number or retrieving them failed');
    }

    /**
     * dropEntries
     *
     * @test
     */
    public function testDropEntries()
    {
        for ($i = 1; $i <10; $i++)
        {
            $nr = sprintf("%04d",$i);
            $log = new \Yana\Db\Ddl\Logs\Create('logcreate');
            $log->setName("name_" . $nr);
            $log->setVersion($nr);
            $this->object->addEntry($log);
        }

        // let's be bad guys, dan drop everything again
        $this->object->dropEntries();
        $countAll = count($this->object->getEntries());

        $this->assertEquals($countAll , 0, '\Yana\Db\Ddl\ChangeLog, dropping the entries has failed');
    }

    /**
     * getEntries
     *
     * @test
     */
    public function testGetEntries()
    {
        // First: create a lot of different entries
        // Second: count them
        for ($i = 1; $i <30; $i++)
        {
            $nr = sprintf("%04d",$i);
            if ($i % 3 == 0) {
                $log = new \Yana\Db\Ddl\Logs\Create('logcreate');
                $log->setName("name_" . $nr);
            } else {
                $log = new \Yana\Db\Ddl\Logs\Sql();
            }
            $log->setVersion($nr);
            switch ($i % 3)
            {
                case 1:
                    $log->setDBMS('mysql');
                break;
                case 2:
                    $log->setDBMS('oracle');
                break;
            }
            $this->object->addEntry($log);
        }

        $countAll = count($this->object->getEntries(null));
        $this->assertEquals($countAll , 9, '\Yana\Db\Ddl\ChangeLog, dropping the entries has failed');

        $countAll = count($this->object->getEntries(null, 'mysql'));
        $this->assertEquals($countAll , 19, '\Yana\Db\Ddl\ChangeLog, dropping the entries has failed');

        $countAll = count($this->object->getEntries(null, 'oracle'));
        $this->assertEquals($countAll , 19, '\Yana\Db\Ddl\ChangeLog, dropping the entries has failed');

        // truncate list of changes
        $this->object->dropEntries();
        $countAll = count($this->object->getEntries());
        $this->assertEquals($countAll , 0, '\Yana\Db\Ddl\ChangeLog, dropping the entries has failed');
    }

    /**
     * @test
     */
    public function testUnserializeFromXDDL()
    {
        $xddl = '
    <changelog>
        <create version="1.2" ignoreError="no" name="test_create" subject="trigger">
            <description>test</description>
        </create>
        <rename version="1.2" ignoreError="no" subject="table" name="test_rename">
            <description>test</description>
        </rename>
        <drop version="1.2" ignoreError="no" subject="view" name="test_drop">
            <description>test</description>
        </drop>
        <update version="1.2" ignoreError="no" subject="view" name="test_update" property="array" value="name" oldvalue="new">
            <description>test</description>
        </update>
        <sql version="1.2" ignoreError="no" dbms="generic">
            <description>test</description>
            <code>test</code>
        </sql>
        <change version="1.2" ignoreError="no" dbms="generic" type="default">
            <description>test</description>
            <logparam>1</logparam>
            <logparam name="test">2</logparam>
        </change>
    </changelog>';
        $node = \simplexml_load_string($xddl);
        $this->object = \Yana\Db\Ddl\ChangeLog::unserializeFromXDDL($node, $this->parent);
        $countAll = count($this->object->getEntries(null));
        $this->assertEquals($countAll , 6);
    }

}
