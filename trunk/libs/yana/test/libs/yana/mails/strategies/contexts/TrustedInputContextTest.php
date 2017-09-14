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

namespace Yana\Mails\Strategies\Contexts;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../../include.php';

/**
 * @package  test
 */
class TrustedInputContextTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Mails\Strategies\Contexts\TrustedInputContext
     */
    protected $object;

    /**
     * @var \Yana\Mails\Strategies\NullStrategy
     */
    protected $strategy;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->strategy = new \Yana\Mails\Strategies\NullStrategy();
        $this->object = new \Yana\Mails\Strategies\Contexts\TrustedInputContext($this->strategy);
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
    public function testInvoke()
    {
        $message = new \Yana\Mails\Messages\Message();
        $message->setSubject('Subject')
            ->setRecipient('Address')
            ->setText('Some Text')
            ->getHeaders()->setHighPriority();
        $this->object->__invoke($message);
        $headers = $message->getHeaders()->toArray();
        $expectedArguments = array($message->getRecipient(), $message->getSubject(), $message->getText(), $headers);
        $this->assertEquals(array($expectedArguments), $this->strategy->getMails());
    }

}
