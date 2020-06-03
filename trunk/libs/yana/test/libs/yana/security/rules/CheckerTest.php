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

namespace Yana\Security\Rules;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @ignore
 */
class MyFalseRule extends \Yana\Security\Rules\NullRule
{
    public function __invoke(Requirements\IsRequirement $required, string $profileId, string $action, \Yana\Security\Data\Behaviors\IsBehavior $user): ?bool
    {
        return false;
    }
}

/**
 * Test-case
 *
 * @package  test
 */
class CheckerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Security\Rules\Checker
     */
    protected $emptyChecker;

    /**
     * @var \Yana\Security\Rules\Checker
     */
    protected $filledChecker;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->emptyChecker = new \Yana\Security\Rules\Checker(new \Yana\Security\Rules\Requirements\NullReader());

        $adapter = new \Yana\Security\Rules\Requirements\NullReader(new \Yana\Security\Rules\Requirements\Requirement("group", "role", 0));
        $this->filledChecker = new \Yana\Security\Rules\Checker($adapter);
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
    public function testAddSecurityRule()
    {
        $rule = new \Yana\Security\Rules\NullRule();
        $this->assertTrue($this->emptyChecker->addSecurityRule($rule) instanceof \Yana\Security\Rules\Checker, 'Instance of Checker expected');
    }

    /**
     * @test
     */
    public function testCheckRulesNoRequirements()
    {
        $profileId = "test";
        $action = "test";
        $builder = new \Yana\Security\Data\Behaviors\Builder();
        $user = $builder(new \Yana\Security\Data\Users\Entity("test"));
        $this->assertTrue($this->emptyChecker->checkRules($profileId, $action, $user), 'If there are no requirements than true must be returned');
    }

    /**
     * @test
     */
    public function testCheckRulesNoRules()
    {
        $profileId = "test";
        $action = "test";
        $builder = new \Yana\Security\Data\Behaviors\Builder();
        $user = $builder(new \Yana\Security\Data\Users\Entity("test"));
        $this->assertFalse($this->filledChecker->checkRules($profileId, $action, $user), 'False must be the default');
    }

    /**
     * @test
     */
    public function testCheckRules()
    {
        $this->filledChecker->addSecurityRule(new \Yana\Security\Rules\NullRule()); // always returns TRUE
        $profileId = "test";
        $action = "test";
        $builder = new \Yana\Security\Data\Behaviors\Builder();
        $user = $builder(new \Yana\Security\Data\Users\Entity("test"));
        $this->assertTrue($this->filledChecker->checkRules($profileId, $action, $user), 'Must return TRUE if rule returns TRUE');
    }

    /**
     * @test
     */
    public function testCheckByRequirement()
    {
        $this->filledChecker->addSecurityRule(new \Yana\Security\Rules\NullRule()); // always returns TRUE
        $profileId = "test";
        $action = "test";
        $requirement = new \Yana\Security\Rules\Requirements\Requirement("group", "role", 0);
        $builder = new \Yana\Security\Data\Behaviors\Builder();
        $user = $builder(new \Yana\Security\Data\Users\Entity("test"));
        $this->assertTrue($this->filledChecker->checkByRequirement($requirement, $profileId, $action, $user), 'Must return TRUE if rule returns TRUE');
        $this->filledChecker->addSecurityRule(new \Yana\Security\Rules\MyFalseRule()); // always returns FALSE
        $this->assertFalse($this->filledChecker->checkByRequirement($requirement, $profileId, $action, $user), 'Must return FALSE if rule returns FALSE');
    }

}
