<?php
/**
 * PHPUnit test-case.
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

namespace Yana\Db;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../include.php';

/**
 * @package  test
 */
class DriverEnumerationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function test()
    {
        foreach (\Yana\Db\DriverEnumeration::getValidItems() as $item)
        {
            $this->assertInternalType('string', $item);
        }
    }

    /**
     * @test
     */
    public function testMapAliasToDriver()
    {
        $this->assertSame(__FUNCTION__, \Yana\Db\DriverEnumeration::mapAliasToDriver(__FUNCTION__));
        $this->assertSame(\Yana\Db\DriverEnumeration::DB2, \Yana\Db\DriverEnumeration::mapAliasToDriver(\Yana\Db\Doctrine\DriverEnumeration::DB2));
        $this->assertSame(\Yana\Db\DriverEnumeration::FRONTBASE, \Yana\Db\DriverEnumeration::mapAliasToDriver(\Yana\Db\Mdb2\DriverEnumeration::FRONTBASE));
        $this->assertSame(\Yana\Db\DriverEnumeration::INTERBASE, \Yana\Db\DriverEnumeration::mapAliasToDriver(\Yana\Db\Mdb2\DriverEnumeration::INTERBASE));
        $this->assertSame(\Yana\Db\DriverEnumeration::MSSQL, \Yana\Db\DriverEnumeration::mapAliasToDriver(\Yana\Db\Doctrine\DriverEnumeration::MSSQL));
        $this->assertSame(\Yana\Db\DriverEnumeration::MYSQL, \Yana\Db\DriverEnumeration::mapAliasToDriver(\Yana\Db\Doctrine\DriverEnumeration::MYSQL));
        $this->assertSame(\Yana\Db\DriverEnumeration::ORACLE, \Yana\Db\DriverEnumeration::mapAliasToDriver(\Yana\Db\Doctrine\DriverEnumeration::ORACLE));
        $this->assertSame(\Yana\Db\DriverEnumeration::POSTGRESQL, \Yana\Db\DriverEnumeration::mapAliasToDriver(\Yana\Db\Doctrine\DriverEnumeration::POSTGRESQL));
        $this->assertSame(\Yana\Db\DriverEnumeration::SQLLITE, \Yana\Db\DriverEnumeration::mapAliasToDriver(\Yana\Db\Doctrine\DriverEnumeration::SQLITE));
        $this->assertSame(\Yana\Db\DriverEnumeration::SYBASE, \Yana\Db\DriverEnumeration::mapAliasToDriver(\Yana\Db\Doctrine\DriverEnumeration::SYBASE));
        $this->assertSame(\Yana\Db\DriverEnumeration::QUERYSIM, \Yana\Db\DriverEnumeration::mapAliasToDriver(\Yana\Db\Mdb2\DriverEnumeration::QUERYSIM));
    }

}
