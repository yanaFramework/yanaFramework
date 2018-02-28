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
class ArrayAdapterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ArrayAdapter
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Security\Data\Users\ArrayAdapter();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }

    /**
     * @covers Yana\Security\Data\Users\ArrayAdapter::findUserByMail
     * @todo   Implement testFindUserByMail().
     */
    public function testFindUserByMail()
    {
        $entity = new \Yana\Security\Data\Users\Entity('TEST');
        $entity->setMail('anymail@domain.tld');
        $this->object[] = $entity;
        $entity2 = new \Yana\Security\Data\Users\Entity('OTHER');
        $entity2->setMail('othermail@domain.tld');
        $this->object[] = $entity2;
        $this->assertSame($entity, $this->object->findUserByMail('anymail@domain.tld'));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\User\MailNotFoundException
     */
    public function testFindUserByMailNotFoundException()
    {
        $this->object->findUserByMail('noSuchMail@domain.tld');
    }

    /**
     * @test
     */
    public function testFindUserByRecoveryId()
    {
        $entity = new \Yana\Security\Data\Users\Entity('TEST');
        $entity->setPasswordRecoveryId('RECOVERY-ID');
        $this->object[] = $entity;
        $entity2 = new \Yana\Security\Data\Users\Entity('OTHER');
        $entity2->setPasswordRecoveryId('NONE');
        $this->object[] = $entity2;
        $this->assertSame($entity, $this->object->findUserByRecoveryId('RECOVERY-ID'));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\User\NotFoundException
     */
    public function testFindUserByRecoveryIdNotFoundException()
    {
        $this->object->findUserByRecoveryId('noSuchRecoveryId');
    }

    /**
     * @test
     */
    public function testDelete()
    {
        $entity = new \Yana\Security\Data\Users\Entity('TEST');
        $entity->setMail('anymail@domain.tld');
        $this->object['Test'] = $entity;
        $entity2 = new \Yana\Security\Data\Users\Entity('OTHER');
        $entity2->setMail('othermail@domain.tld');
        $this->object['Other'] = $entity2;
        $this->assertCount(2, $this->object);
        $this->object->delete($entity);
        $this->assertEmpty($this->object['Test']);
        $this->assertSame($entity2, $this->object['Other']);
    }

}
