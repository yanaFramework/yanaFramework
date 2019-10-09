<?php
/**
 * YANA library
 *
 * Primary controller class
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
 * @package  yana
 * @license  http://www.gnu.org/licenses/gpl.txt
 */
declare(strict_types=1);

namespace Yana\Core\Dependencies;

/**
 * Dependency container for the exception base class.
 *
 * @package     yana
 * @subpackage  core
 */
class UrlFormatterContainer extends \Yana\Core\StdObject implements \Yana\Core\Dependencies\IsUrlFormatterContainer
{

    /**
     * @var  string
     */
    private $_applicationUrlParameters = "";

    /**
     * <<constructor>> Initialize the standard URL parameters.
     *
     * @param  string  $applicationUrlParameters  should start with ?id=... and contain the basic URL parameters of the application
     */
    public function __construct(string $applicationUrlParameters)
    {
        $this->_applicationUrlParameters = $applicationUrlParameters;
    }

    /**
     * Returns a string containing profile and session parameters.
     *
     * Looks like this: ?id={profileId}&{sessionName}={sessionId}.
     *
     * If the client accepts session cookies, the session information is not included.
     *
     * @return  string
     */
    public function getApplicationUrlParameters(): string
    {
        return $this->_applicationUrlParameters;
    }

}

?>