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

namespace Yana\Security\Data\Users;


/**
 * @ignore
 */
require_once __DIR__ . '/../../../../../include.php';

/**
 * Test-case
 *
 * @package  test
 */
class MapperTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Security\Data\Users\Mapper
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Security\Data\Users\Mapper();
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
     * @expectedException \Yana\Core\Exceptions\User\MissingNameException
     */
    public function testToEntityMissingNameException()
    {
        $this->object->toEntity(array());
    }

    /**
     * @test
     */
    public function testToEntity()
    {
        $databaseRow = array(
            \Yana\Security\Data\Tables\UserEnumeration::ID => "Náme!",
            \Yana\Security\Data\Tables\UserEnumeration::IS_ACTIVE => "",
            \Yana\Security\Data\Tables\UserEnumeration::LOGIN_FAILURE_COUNT => "1",
            \Yana\Security\Data\Tables\UserEnumeration::LOGIN_FAILURE_TIME => "2",
            \Yana\Security\Data\Tables\UserEnumeration::LOGIN_COUNT => "3",
            \Yana\Security\Data\Tables\UserEnumeration::LOGIN_TIME => "4",
            \Yana\Security\Data\Tables\UserEnumeration::IS_EXPERT_MODE => "1",
            \Yana\Security\Data\Tables\UserEnumeration::RECENT_PASSWORDS => "6",
            \Yana\Security\Data\Tables\UserEnumeration::LANGUAGE => "L@nguáge!",
            \Yana\Security\Data\Tables\UserEnumeration::PASSWORD => "P@sswórd!",
            \Yana\Security\Data\Tables\UserEnumeration::MAIL => "Máil?",
            \Yana\Security\Data\Tables\UserEnumeration::PASSWORD_RECOVERY_ID => "Id!",
            \Yana\Security\Data\Tables\UserEnumeration::PASSWORD_RECOVERY_TIME => "7",
            \Yana\Security\Data\Tables\UserEnumeration::PASSWORD_TIME => "8",
            \Yana\Security\Data\Tables\UserEnumeration::TIME_CREATED => "9",
            \Yana\Security\Data\Tables\UserEnumeration::SESSION_CHECKSUM => "Chècksum!"
        );
        $entity = $this->object->toEntity($databaseRow);
        $this->assertEquals($databaseRow[\Yana\Security\Data\Tables\UserEnumeration::ID], $entity->getId());
        $this->assertFalse($entity->isActive());
        $this->assertEquals($databaseRow[\Yana\Security\Data\Tables\UserEnumeration::LOGIN_FAILURE_COUNT], $entity->getFailureCount());
        $this->assertEquals($databaseRow[\Yana\Security\Data\Tables\UserEnumeration::LOGIN_FAILURE_TIME], $entity->getFailureTime());
        $this->assertEquals($databaseRow[\Yana\Security\Data\Tables\UserEnumeration::LOGIN_COUNT], $entity->getLoginCount());
        $this->assertEquals($databaseRow[\Yana\Security\Data\Tables\UserEnumeration::LOGIN_TIME], $entity->getLoginTime());
        $this->assertTrue($entity->isExpert());
        $this->assertEquals((array) $databaseRow[\Yana\Security\Data\Tables\UserEnumeration::RECENT_PASSWORDS], $entity->getRecentPasswords());
        $this->assertEquals($databaseRow[\Yana\Security\Data\Tables\UserEnumeration::LANGUAGE], $entity->getLanguage());
        $this->assertEquals($databaseRow[\Yana\Security\Data\Tables\UserEnumeration::PASSWORD], $entity->getPassword());
        $this->assertEquals($databaseRow[\Yana\Security\Data\Tables\UserEnumeration::MAIL], $entity->getMail());
        $this->assertEquals($databaseRow[\Yana\Security\Data\Tables\UserEnumeration::PASSWORD_RECOVERY_ID], $entity->getPasswordRecoveryId());
        $this->assertEquals($databaseRow[\Yana\Security\Data\Tables\UserEnumeration::PASSWORD_RECOVERY_TIME], $entity->getPasswordRecoveryTime());
        $this->assertEquals($databaseRow[\Yana\Security\Data\Tables\UserEnumeration::PASSWORD_TIME], $entity->getPasswordChangedTime());
        $this->assertEquals($databaseRow[\Yana\Security\Data\Tables\UserEnumeration::TIME_CREATED], $entity->getTimeCreated());
        $this->assertEquals($databaseRow[\Yana\Security\Data\Tables\UserEnumeration::SESSION_CHECKSUM], $entity->getSessionCheckSum());
    }

    /**
     * @test
     */
    public function testToDatabaseRow()
    {
        $entity = (new Entity("Náme!"))
            ->setActive(false)
            ->setFailureCount(1)
            ->setFailureTime(2)
            ->setLoginCount(3)
            ->setLoginTime(4)
            ->setExpert(true)
            ->setRecentPasswords(array("P@sswórd1", "P@sswórd2"))
            ->setLanguage("L@nguáge!")
            ->setPassword("P@sswórd!")
            ->setMail("Máil?")
            ->setPasswordRecoveryId("Id!")
            ->setPasswordRecoveryTime(7)
            ->setPasswordChangedTime(8)
            ->setTimeCreated(9)
            ->setSessionCheckSum("Chècksum!");
        $databaseRow = $this->object->toDatabaseRow($entity);
        $this->assertEquals($databaseRow[\Yana\Security\Data\Tables\UserEnumeration::ID], $entity->getId());
        $this->assertFalse($databaseRow[\Yana\Security\Data\Tables\UserEnumeration::IS_ACTIVE]);
        $this->assertEquals($databaseRow[\Yana\Security\Data\Tables\UserEnumeration::LOGIN_FAILURE_COUNT], $entity->getFailureCount());
        $this->assertEquals($databaseRow[\Yana\Security\Data\Tables\UserEnumeration::LOGIN_FAILURE_TIME], $entity->getFailureTime());
        $this->assertEquals($databaseRow[\Yana\Security\Data\Tables\UserEnumeration::LOGIN_COUNT], $entity->getLoginCount());
        $this->assertEquals($databaseRow[\Yana\Security\Data\Tables\UserEnumeration::LOGIN_TIME], $entity->getLoginTime());
        $this->assertTrue($databaseRow[\Yana\Security\Data\Tables\UserEnumeration::IS_EXPERT_MODE]);
        $this->assertEquals($databaseRow[\Yana\Security\Data\Tables\UserEnumeration::RECENT_PASSWORDS], $entity->getRecentPasswords());
        $this->assertEquals($databaseRow[\Yana\Security\Data\Tables\UserEnumeration::LANGUAGE], $entity->getLanguage());
        $this->assertEquals($databaseRow[\Yana\Security\Data\Tables\UserEnumeration::PASSWORD], $entity->getPassword());
        $this->assertEquals($databaseRow[\Yana\Security\Data\Tables\UserEnumeration::MAIL], $entity->getMail());
        $this->assertEquals($databaseRow[\Yana\Security\Data\Tables\UserEnumeration::PASSWORD_RECOVERY_ID], $entity->getPasswordRecoveryId());
        $this->assertEquals($databaseRow[\Yana\Security\Data\Tables\UserEnumeration::PASSWORD_RECOVERY_TIME], $entity->getPasswordRecoveryTime());
        $this->assertEquals($databaseRow[\Yana\Security\Data\Tables\UserEnumeration::PASSWORD_TIME], $entity->getPasswordChangedTime());
        $this->assertEquals($databaseRow[\Yana\Security\Data\Tables\UserEnumeration::TIME_CREATED], $entity->getTimeCreated());
        $this->assertEquals($databaseRow[\Yana\Security\Data\Tables\UserEnumeration::SESSION_CHECKSUM], $entity->getSessionCheckSum());
    }

}
