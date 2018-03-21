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
 * @package  yana
 * @license  http://www.gnu.org/licenses/gpl.txt
 */

namespace Yana\Plugins\Dependencies;

/**
 * <<interface>> Dependency container for plugin menus.
 *
 * To collect all the dependencies needed for the menu builder.
 *
 * @package     yana
 * @subpackage  plugins
 */
class MenuContainer extends \Yana\Core\Object implements \Yana\Plugins\Dependencies\IsMenuContainer
{

    /**
     * @var  \Yana\Core\Dependencies\IsApplicationContainer
     */
    private $_applicationContainer = null;

    /**
     * <<constructor>> Initializes the dependencies.
     *
     * Yes, this is the lazy man's way to do it by creating a dependency to the whole application.
     * But this container is initialized by the application instance anyway, so no sweat!
     *
     * In fact, the menu depends on several settings that are part of the application
     * configuration, wrapped by (you probably guessed it) the application.
     *
     * But feel free to swap this implementation if you have a better idea >;)
     *
     * @param  \Yana\Core\Dependencies\IsApplicationContainer  $container
     */
    public function __construct(\Yana\Core\Dependencies\IsApplicationContainer $container)
    {
        $this->_applicationContainer = $container;
    }

    /**
     * Return the wrapped application instance.
     *
     * @return  \Yana\Application
     */
    protected function _getApplicationContainer()
    {
        return $this->_applicationContainer;
    }

    /**
     * Returns translation facade.
     *
     * @return  \Yana\Translations\IsFacade
     */
    public function getTranslationFacade()
    {
        return $this->_getApplicationContainer()->getLanguage();
    }

    /**
     * Returns security facade.
     *
     * @return  \Yana\Security\IsFacade
     */
    public function getSecurityFacade()
    {
        return $this->_getApplicationContainer()->getSecurity();
    }

    /**
     * Returns bool(true) if the currently active profile is the default profile.
     *
     * @return  bool
     */
    public function isDefaultProfile()
    {
        $application = $this->_getApplicationContainer();
        return 0 === \strcasecmp($application->getProfileId(), $application->getDefault('PROFILE'));
    }

    /**
     * Returns a plugin facade.
     *
     * @return \Yana\Plugins\Facade
     */
    public function getPluginFacade()
    {
        return $this->_getApplicationContainer()->getPlugins();
    }

    /**
     * Returns a formatting helper for menu URLs.
     *
     * @return  \Yana\Views\Helpers\Formatters\UrlFormatter
     */
    public function getUrlFormatter()
    {
        return new \Yana\Views\Helpers\Formatters\UrlFormatter();
    }

}

?>